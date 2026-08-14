<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(
        int $userId,
        int $addressId,
        array $cartItems)
    {

        return DB::transaction(function () use ($userId, $addressId, $cartItems) {
            return $this->processOrder($userId, $addressId, $cartItems);
        });
    }

    private function processOrder(Int $userId, int $addressId, array $cartItems)
    {
        // cari alamat yang di pilih user 
        $address = Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        if (!$address) {
            throw ValidationException::withMessages([
                'address_id' => ['Selected address is invalid.']
            ]); 
        }

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
        
        return $order->load('items.product');
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
            'items.product'
        ])
        ->where('user_id', $userId)
        ->where('id', $orderId)
        ->first();
    }


    // Admin Service

    public function getAllOrders()
    {
        return Order::with([
            'user', 'items.product'
            ])
            ->latest()
            ->paginate(12);
    }

    public function getOrderById(Int $id): ?Order
    {
        return Order::with([
            'user', 'items.product'
            ])
            ->find($id);
    }

    public function updateOrderStatus(Int $id, Array $data): ?Order
    {
        $order = Order::findOrFail($id);

        $order->update([
            "status" => $data['status']
        ]);

        return $order->fresh();
    }
    
}