<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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


    public function createSnapTransaction(Request $request, int $paymentId) {

        // ambil user id
        $userId = $request->user()->id;
        
        $token = $this->paymentService
            ->createSnapTransaction($userId, $paymentId);

        return response()->json([
            'success' => true,
            'message' => 'Snap token created successfully',
            'data' => [
                'snap_token' => $token,
            ],
        ]);
    }

    public function handleMidtransNotification(Request $request) {

        $notification = $request->all();

        $this->paymentService->handleMidtransNotification($notification);

        return response()->json([
            'success' => true,
            'message' => 'Midtrans notification handled successfully',
        ]);
    }

    public function getPaymentByGatewayOrderId(Request $request, string $gatewayOrderId) {

        // ambil user id
        $userId = $request->user()->id;

        $payment = $this->paymentService
            ->getUserPaymentByGatewayOrderId($userId, $gatewayOrderId);


        // Jika payment tidak ditemukan atau bukan milik user ini.
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment found successfully',
            'data' => $payment,
        ]);
    }
}
