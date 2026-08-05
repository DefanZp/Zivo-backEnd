<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $order = $this->orderService->createOrder(
            $request->user()->id,
            $request->only([
                'customer_name',
                'phone',
                'address',
            ]),
            $request->input('items')
        );

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
}
