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
