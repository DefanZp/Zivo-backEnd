<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    )
    {}

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validatedData = $request->validate([
            "customer_name" => "required|string|max:255",
            "phone" => "required|string|max:20",
            "address" => "required|string|max:500",
            "items" => "required|array|min:1",
            "items.*.product_id" => "required|integer|exists:products,id",
            "items.*.quantity" => "required|integer|min:1",
        ]);

        // mengelompokkan shipping data
        $shippingData = [
            'customer_name' => $validatedData['customer_name'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
        ];

        $order = $this->orderService->createOrder(
            $userId,
            $shippingData,
            $validatedData['items']
        );

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
}
