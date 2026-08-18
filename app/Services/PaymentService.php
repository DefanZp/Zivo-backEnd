<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{

    public function createPayment(int $orderId, string $amount): Payment {
        return Payment::create([
            'order_id' => $orderId,
            'amount' => $amount,
            'payment_method' => 'manual',
            'payment_status' => 'unpaid',
        ]);
    }

    // Admin 

    // fungsi untuk validasi status payment sebelum update status payment
    private function validatePaymentStatus(
        Payment $payment
    ) {
        // Payment yang sudah dibayar tidak boleh diubah lagi.
        if ($payment->payment_status === 'paid') {
            throw ValidationException::withMessages([
            'status' => ['Paid payments cannot be changed.']
            ]);
        }

        // Payment yang sudah expired tidak boleh diubah lagi.
        if($payment->payment_status === 'expired') {
            throw ValidationException::withMessages([
            'status' => ['Expired payments cannot be changed.']
            ]);
        }
    }

    // fungsi untuk validasi order status sebelum update order status 
    private function validateOrderBeforePayment(
        Payment $payment
    ) {
        // Order yang sudah dibatalkan tidak boleh diproses kembali.
        if ($payment->order->status === 'cancelled') {
            throw ValidationException::withMessages([
                'status' => [
                    'Cannot process payment for a cancelled order.'
                ]
            ]);
        }

        // Order yang sudah selesai tidak membutuhkan pembayaran lagi.
        if ($payment->order->status === 'completed') {
            throw ValidationException::withMessages([
                'status' => [
                    'Cannot process payment for a completed order.'
                ]
            ]);
        }
    }

    public function updatePaymentStatus(
        int $paymentId,
        string $status
    ): Payment {

        return DB::transaction(function () use ($paymentId, $status) {

            $payment = Payment::with('order.items.product')
                ->where('id', $paymentId)
                ->lockForUpdate()
                ->firstOrFail();

            // Jika status sama, tidak perlu melakukan apa-apa.
            if ($payment->payment_status === $status) {
                return $payment;
            }

            // Pastikan perubahan status diperbolehkan.
            $this->validatePaymentStatus($payment);

            // Update payment status.
            $payment->update([
                'payment_status' => $status,
            ]);

            // Jika payment berhasil dibayar.
            if ($status === 'paid') {

                $payment->update([
                    'paid_at' => now(),
                ]);

                // validasi order sebelum payment
                $this->validateOrderBeforePayment($payment);

                $payment->order->update([
                    'status' => 'processing',
                ]);
            }

            // Jika payment expired.
            if ($status === 'expired') {
                // panggil cancel order service
                app(OrderService::class)->cancelOrder($payment->order_id);
            }

            return $payment->fresh('order');
        });
    }
}