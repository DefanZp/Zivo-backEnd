<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected OrderService $orderService;
    
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->getAllOrders();

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ]);
    }

    public function show(Int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order
        ]);
    }

    public function updateStatus(Request $request, Int $id): JsonResponse
    {
        $order = $this->orderService
            ->updateOrderStatus($id, $request->only('status'));
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}
