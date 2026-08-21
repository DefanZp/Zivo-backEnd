<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{

    // config midtrans

    private function configureMidtrans() {

        // ambil config midtrans dari service config
        Config::$serverKey = config('services.midtrans.server_key');

        // ambil config midtrans dari service config
        Config::$isProduction = config('services.midtrans.is_production');

        // aktifkan sanitized
        Config::$isSanitized = true;

        // aktifkan 3ds untuk transaksi kartu
        Config::$is3ds = true;

    }

    // function create snap
    public function createSnapTransaction(int $userId,  int $paymentId): string {
        
        // load dulu konfigurasi midtrans
        $this->configureMidtrans();

        // ambil payment dan pastikan user id sesuai
        $payment = Payment::with('order')
            ->where('id', $paymentId)
            ->firstOrFail();
        
        // cek payment tersebut milik user yang sesuai
        if ($payment->order->user_id !== $userId) {
            throw ValidationException::withMessages([
                'payment' => ['You are not allowed to access this payment.']
            ]);
        }

        // ambil data order berserta item dan product
        $payment->load('order.items.product');

        // siapkan data yang dibutuhkan midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $payment->gateway_order_id,
                'gross_amount' => (int) $payment->amount,
            ],
        ];

        // minta snap token ke midtrans
        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }

    // private function untuk validate midtrans notification
    private function validateMidtransNotification(array $notification) {

        // pastikan datanya sesuai
        if ( 
            !isset($notification['order_id']) ||
            !isset($notification['status_code']) ||
            !isset($notification['gross_amount']) ||
            !isset($notification['signature_key'])
        ) {
            throw ValidationException::withMessages([
                'notification' => ['Invalid midtrans notification.']
            ]);
        }

        // ambil server key dari service config
        $serverKey = config('services.midtrans.server_key');

        // buat signature berdasarkan data dari midtrans
        $signature = hash(
            'sha512',
            $notification['order_id'] .
            $notification['status_code'] . 
            $notification['gross_amount'] . 
            $serverKey
        );

        // bandingkan signature dengan signature dari midtrans
        if (
            !hash_equals(
                $signature,
                $notification['signature_key']
            )
        ) {
            throw ValidationException::withMessages([
                'notification' => ['Invalid midtrans notification signature.']
            ]);
        }
    }

    // fungsi untuk handle midtrans notification
    public function handleMidtransNotification(array $notification) {
        return DB::transaction( function () use ($notification) {

            // validasi notification dari midtrans
            $this->validateMidtransNotification($notification);
            
            // cari payment berdasarkan gateway order id
            $payment = Payment::with('order')
                ->where('gateway_order_id', $notification['order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // simpan transaction id ke payment
            $payment->update([
                'gateway_transaction_id' => $notification['transaction_id'],
                'payment_method' => $notification['payment_type'],
            ]);


        });
    }

    public function createPayment(int $orderId, string $amount): Payment {

        $gatewayOrderId = 'ZIVO-ORDER-' . $orderId;

        return Payment::create([
            'order_id' => $orderId,
            'gateway' => 'midtrans',
            'gateway_order_id' => $gatewayOrderId,
            'amount' => $amount,
            'payment_method' => 'midtrans',
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