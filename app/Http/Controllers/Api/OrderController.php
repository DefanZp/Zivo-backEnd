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

    public function index(Request $request) {
        $userId = $request->user()->id;

        $orders = $this->orderService->getUserOrders($userId);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ]);
    }

    public function show(Request $request, int $id) {
        $userId = $request->user()->id;

        $order = $this->orderService
            ->getUserOrderById($userId, $id);
        
        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order
        ]);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validatedData = $request->validate([
            "address_id" => 'required|integer|exists:addresses,id',
            "items" => "required|array|min:1",
            "items.*.product_id" => "required|integer|exists:products,id",
            "items.*.quantity" => "required|integer|min:1",
        ]);

        $order = $this->orderService->createOrder(
            $userId,
            $validatedData['address_id'],
            $validatedData['items']
        );

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
}
