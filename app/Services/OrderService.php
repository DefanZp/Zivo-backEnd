<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(
        int $userId,
        array $shippingData,
        array $cartItems)
    {

        return DB::transaction(function () use ($userId, $shippingData, $cartItems) {
            return $this->processOrder($userId, $shippingData, $cartItems);
        });
    }

    private function processOrder(Int $userId, array $shippingData, array $cartItems)
    {
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
            "customer_name" => $shippingData['customer_name'],
            "phone" => $shippingData['phone'],
            "address" => $shippingData['address'],
            "total_price" => $total_price,
        ]);

        // buat order items
        foreach ($cartItems as $item)
        {
            $product = $products->get($item['product_id']);
            
            $order->items()->create([
                "order_id" => $order->id,
                "product_id" => $product->id,
                "quantity" => $item['quantity'],
                "price" => $product->price,
            ]);

            // kurangi stock
            $product->decrement('stock', $item['quantity']);
        }
        
        return $order->load('items.product');
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