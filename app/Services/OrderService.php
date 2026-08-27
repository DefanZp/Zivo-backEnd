<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class OrderService
{
    // inject PaymentService
    public function __construct(
        protected PaymentService $paymentService
    ){}

    public function createOrder(
        int $userId,
        int $addressId,
        array $cartItems)
    {

        return DB::transaction(function () use ($userId, $addressId, $cartItems) {

            // proses order
            $order =  $this->processOrder($userId, $addressId, $cartItems);

            // triger webhook n8n
            DB::afterCommit(function () use ($order) {
                $this->sendOrderCreatedWebhook($order);
            });

            return $order;
        });

    }

    private function processOrder(Int $userId, int $addressId, array $cartItems)
    {
        // cari alamat yang di pilih user 
        $address = Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // fetch product
        $productIds = array_column($cartItems, 'product_id');
        $products = Product::lockForUpdate()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        // cek stok dan hitung total
        $total_price = 0;

        foreach ($cartItems as $item)
        {
            // cek stock
            $product = $products->get($item['product_id']);

            if ($product->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'stock' => ["{$product->name} stok tidak mencukupi."]
                ]); 
            }

            // hitung total
            $total_price += $product->price * $item['quantity'];
        }

        // buat order
        $order = Order::create([
            "user_id" => $userId,

            // Snapshot penerima
            "recipient_name" => $address->recipient_name,
            "phone" => $address->phone,
            "full_address" => $address->full_address,

            // Snapshot wilayah
            "province_id" => $address->province_id,
            "province_name" => $address->province_name,

            "city_id" => $address->city_id,
            "city_name" => $address->city_name,

            "district_id" => $address->district_id,
            "district_name" => $address->district_name,

            "subdistrict_id" => $address->subdistrict_id,
            "subdistrict_name" => $address->subdistrict_name,

            'postal_code' => $address->postal_code,

            // Snapshot lokasi
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,

            "total_price" => $total_price,
        ]);

        // buat order items
        foreach ($cartItems as $item)
        {
            $product = $products->get($item['product_id']);
            
            $order->items()->create([
                "product_id" => $product->id,
                "quantity" => $item['quantity'],
                "price" => $product->price,
            ]);

            // kurangi stock
            $product->decrement('stock', $item['quantity']);
        }

        // Buat payment
        $this->paymentService->createPayment( 
            $order->id, 
            $total_price 
        );
        
        return $order->load([
            'items.product',
            'payment'
        ]);
    }

    // fungsi untuk send webhook ke n8n
    public function sendOrderCreatedWebhook(Order $order): void {
        Http::withBasicAuth(
            config('services.n8n.username'),
            config('services.n8n.password')
        )->post(
            config('services.n8n.order_webhook_url'),
            [
                'event' => 'order.created',

                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment->payment_status,
                    'total_price' => $order->total_price,

                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product?->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ];
                    })->values()->all(),
                ]
            ]
        );
    }

    // Cari order berdasarkan id user
    public function getUserOrders(int $userId) {
        return Order::with([
            'items.product'
        ])
        ->where('user_id', $userId)
        ->latest()
        ->paginate(12);
    }

    // Cari Order berdasarkan id order
    public function getUserOrderById(int $userId, int $orderId): ?Order {
        return Order::with([
            'items.product',
            'payment'
        ])
        ->where('user_id', $userId)
        ->where('id', $orderId)
        ->first();
    }


    // Admin Service

    public function getAllOrders()
    {
        return Order::with([
            'user', 
            'items.product',
            'payment'
            ])
            ->latest()
            ->paginate(12);
    }

    public function getOrderById(int $id): ?Order
    {
        return Order::with([
            'user', 
            'items.product',
            'payment'
            ])
            ->find($id);
    }

    
    // fungsi cancel order
    public function cancelOrder(int $id): ?Order {
        return DB::transaction( function () use ($id) {

            $order = Order::with('items.product', 'payment')
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();
            
            // jika order sudah dibatalkan, tidak perlu lakukan apa-apa
            if ($order->status === 'cancelled') {
                return $order; 
            }

            // Order yang sudah selesai tidak boleh dibatalkan
            if ($order->status === 'completed') {
                throw ValidationException::withMessages([
                    'status' => ['Completed orders cannot be cancelled.']
                ]);
            }

            // Order yang sudah dibayar tidak boleh dibatalkan (belum ada refund)
            if ($order->payment && $order->payment->payment_status === 'paid') {
                throw ValidationException::withMessages([
                    'status' => ['Paid orders cannot be cancelled yet.']
                ]);
            }

            $order->update([
                'status' => 'cancelled',
            ]);

            // jika ada data payment dan statusnya unpaid maka ubah statusnya menjadi expired
            if ($order->payment && $order->payment->payment_status === 'unpaid') {
                $order->payment->update([
                    'payment_status' => 'expired'
                ]);
            }

            // Kembalikan stock
            $this->restoreStockForOrder($order);

            return $order->fresh(['items.product', 'payment']);
        });
    }

    private function restoreStockForOrder(Order $order) {
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }

    // fungsi untuk menangani transisi update order status
    public function validateStatusTransition (Order $order, string $newStatus) {

        // Order yang sudah selesai tidak boleh diubah
        if ($order->status === 'completed') {
            throw ValidationException::withMessages([
                'status' => ['Completed orders cannot be changed.']
            ]);
        }

        // Order yang sudah dibatalkan tidak boleh diproses lagi 
        if ($order->status === 'cancelled') {
            throw ValidationException::withMessages([
                'status' => ['Cancelled orders cannot be changed.']
            ]);
        }

        // Pending hanya boleh berubah menjadi processing atau cancelled
        if ($order->status === 'pending') {

            if (
                $newStatus !== 'processing' &&
                $newStatus !== 'cancelled'
                )
            {
                throw ValidationException::withMessages([
                    'status' => ['Pending orders can only become processing or cancelled.']
                ]);
            }
        }

        // Processing hanya boleh berubah menjadi completed atau cancelled
        if ($order->status === 'processing') {

            if (
                $newStatus !== 'completed' && 
                $newStatus !== 'cancelled'
            )
            {
                throw ValidationException::withMessages([
                    'status' => ['Processing orders can only become completed or cancelled.']
                ]);
            }
        }

        // Processing hanya boleh dilakukan  jika payment sudah dibayar
        if (
            $newStatus === 'processing' && 
            (!$order->payment || $order->payment->payment_status !== 'paid')
        ) {
            throw ValidationException::withMessages([
                'status' => ['Order cannot be processed before payment is paid.']
            ]); 
        }
    }

    public function updateOrderStatus(int $id, array $data): ?Order
    {

        $newStatus = $data['status'];

        // jika admin membatalkan order
        if ($newStatus === 'cancelled') {
            return $this->cancelOrder($id);
        }

        return DB::transaction( function () use ($id, $newStatus) {

            // Lock order agar tidak ada dua request yang mengubah status secara bersamaan.
            $order = Order::with('payment')
            ->where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();
        
            // Jika status sama, tidak perlu lakukan apapun
            if($order->status === $newStatus) {
                return $order;
            }

            // Pastikan perubahan status diperbolehkan
            $this->validateStatusTransition($order, $newStatus);

            // Simpan status baru
            $order->update([
                "status" => $newStatus
            ]);

            return $order->fresh();
        });
    }    
    
}