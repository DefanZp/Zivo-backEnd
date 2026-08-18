<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function updateStatus(Request $request, int $id) {

        $validatedData = $request->validate([
            'payment_status' => 'required|string|in:unpaid,paid,failed,cancelled,expired',
        ]);

        $payment = $this->paymentService->updatePaymentStatus(
            $id,
            $validatedData['payment_status'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'data' => $payment
        ], 200);
    }
}
