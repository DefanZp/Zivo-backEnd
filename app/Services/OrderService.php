<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(
        int $userId,
        array $shippingData,
        array $cartItems)
    {
        $validationData = [
            "customer_name" => $shippingData['customer_name'] ?? null,
            "phone" => $shippingData['phone'] ?? null,
            "address" => $shippingData['address'] ?? null,
            "items" => $cartItems,
        ];

        $rules = [
            "customer_name" => "required|string|max:255",
            "phone" => "required|string|max:20",
            "address" => "required|string|max:500",
            "items" => "required|array|min:1",
            "items.*.product_id" => "required|integer|exists:products,id",
            "items.*.quantity" => "required|integer|min:1",
        ];

        $validator = Validator::make($validationData, $rules);

        if ($validator->fails()) 
        {
            throw new ValidationException($validator);
        }

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
            ->get();
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
        $validationData = [
            "status" => $data['status'] ?? null,
        ];

        $rules = [
            "status" => "required|string|in:pending,processing,completed,cancelled",
        ];

        $validator = Validator::make($validationData, $rules);

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        $order = Order::findOrFail($id);

        $order->update([
            "status" => $data['status']
        ]);

        return $order->fresh();
    }
    
}