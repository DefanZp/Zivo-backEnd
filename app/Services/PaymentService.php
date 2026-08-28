<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

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

        // hanya payment unpaid yang boleh membuka snap
        if ($payment->payment_status !== 'unpaid') {
            throw ValidationException::withMessages([
                'payment' => ['This payment is not available for payment.'] 
            ]);
        }

        // hanya order pending yang boleh dibayar
        if ($payment->order->status !== 'pending') {
            throw ValidationException::withMessages([
                'payment' => ['This order is not available for payment.'] 
            ]);
        }

        // Cek apakah transaksi midtrans sudah ada
        $transaction = $this->getMidtransTransactionStatus($payment);

        // Jika transaksi sudah ditemukan , jangan langsung membuat transaksi baru 
        if ($transaction) {
            
            $this->handleExistingMidtransTransaction($payment, $transaction);

        }

        // ambil data order berserta item dan product
        $payment->load('order.items.product');

        // siapkan data yang dibutuhkan midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $payment->gateway_order_id,
                'gross_amount' => (int) $payment->amount,
            ],

            'callbacks' => [
                'finish' => 
                    config('services.frontend.url')
                    . '/payment/result?payment_order_id='
                    . urlencode($payment->gateway_order_id),
            ]
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
            !isset($notification['signature_key']) ||
            !isset($notification['transaction_id']) ||
            !isset($notification['payment_type']) ||
            !isset($notification['transaction_status'])
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

    // private function untuk setiap transaction status dari midtrans

    // untuk payment yang berhasil
    private function markPaymentAsPaid(Payment $payment) {

        // jika status sudah paid tidak perlu melakukan apa-apa
        if($payment->payment_status === 'paid') {
            return;
        }

        // validasi order apakah boleh diproses
        $this->validateOrderBeforePayment($payment);

        // update status payment
        $payment->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // update status order
        $payment->order->update([
            'status' => 'processing',
        ]);
    }

    // untuk payment yang expired
    private function handleExpiredPayment(Payment $payment) {

        // jika status sudah expired tidak perlu melakukan apa-apa
        if($payment->payment_status === 'expired') {
            return;
        }

        // ubah status payment menjadi expired
        $payment->update([
            'payment_status' => 'expired',
        ]);

        // panggil cancel order agar stock dikembalikan
        app(OrderService::class)->cancelOrder($payment->order_id);
    } 

    // untuk payment failed
    private function handleFailedPayment(Payment $payment) {
        
        // jika status sudah failed atau cancelled tidak perlu melakukan apa-apa
        if($payment->payment_status === 'failed' || $payment->payment_status === 'cancelled') {
            return;
        }

        // ubah status payment menjadi failed
        $payment->update([
            'payment_status' => 'failed',
        ]);
    }



    // fungsi untuk process midtrans status
    private function processMidtransStatus(Payment $payment, string $transactionStatus) {

        // payment berhasil
        if ($transactionStatus === 'settlement') {
            $this->markPaymentAsPaid($payment);
            return;
        }

        // untuk transaksi kartu, capture juga berhasil
        if ($transactionStatus === 'capture') {
            $this->markPaymentAsPaid($payment);
            return;
        }

        // untuk yang masih menunggu pembayaran
        if ($transactionStatus === 'pending') {
            return;
        }

        // payment expired
        if ($transactionStatus === 'expire') {
            $this->handleExpiredPayment($payment);
            return;
        }

        // jika dibatalkan atau ditolak
        if ($transactionStatus === 'cancel' || $transactionStatus === 'deny') {
            $this->handleFailedPayment($payment);
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

            // cek apakah payment sebelumnya sudah paid
            $wasAlreadyPaid = $payment->payment_status === 'paid';

            // simpan transaction id ke payment
            $payment->update([
                'gateway_transaction_id' => $notification['transaction_id'],
                'payment_method' => $notification['payment_type'],
            ]);

            // process status dari midtrans
            $this->processMidtransStatus( 
                $payment,
                $notification['transaction_status']
            );

            // trigger webhook jika payment berhasil 
            if (
                !$wasAlreadyPaid &&
                (
                    $notification['transaction_status'] === 'settlement' ||
                    $notification['transaction_status'] === 'capture'
                )
            ) {
                DB::afterCommit(function () use ($payment) {
                    // ambil payment setelah diproses tadi agar datanya terbaru
                    $freshPayment = Payment::with([
                        'order.items.product',
                        'order.payment'
                    ])->find($payment->id);

                    if ($freshPayment) {
                        $this->sendPaymentSettledWebhook($freshPayment);
                    }
                });
            }

            return $payment;

        });
    }

    public function createPayment(int $orderId, string $amount): Payment {

        // Buat ID transaksi yang unik untuk payment gateway.
        $gatewayOrderId = 'ZIVO-' . Str::uuid();

        return Payment::create([
            'order_id' => $orderId,
            'gateway' => 'midtrans',
            'gateway_order_id' => $gatewayOrderId,
            'amount' => $amount,
            'payment_method' => 'midtrans',
            'payment_status' => 'unpaid',
        ]);
    }

    public function getUserPaymentByGatewayOrderId(int $userId, string $gatewayOrderId): ?Payment {
    
        // cari payment berdasarkan gateway order id
        $payment = Payment::with('order')
            ->where('gateway_order_id', $gatewayOrderId)
            ->first();

        // jika tidak ada payment
        if (!$payment) {
            return null;
        }

        // Pastikan payment tersebut milik user yang sedang login.
        if ($payment->order->user_id !== $userId) {
            return null;
        }

        return $payment;
    }

    private function getMidtransTransactionStatus(Payment $payment): ?object {
        try {
            // Gunakan transaction ID jika sudah tersedia.
            if ($payment->gateway_transaction_id) {
                return Transaction::status(
                    $payment->gateway_transaction_id
                );
            }

            // Jika transaction ID belum tersedia,
            // coba gunakan gateway order ID.
            return Transaction::status(
                $payment->gateway_order_id
            );

        } catch (\Exception $error) {

            return null;
        }
    }

    private function handleExistingMidtransTransaction(
        Payment $payment,
        object $transaction
    ): void {

        $transactionStatus = $transaction->transaction_status;

        // Jika transaksi masih pending jangan buat snap baru
        if ($transactionStatus === 'pending') {
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment is still pending. Please wait for the current transaction to finish.'
                ]
            ]);
        }

        // jika transaksi sudah berhasil singkronkan payment menjadi paid
        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            $this->markPaymentAsPaid($payment);
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment has already been completed.'
                ]
            ]);
        }

        // jika transaksi sudah expired, singkronkan payment menjadi expired
        if ($transactionStatus === 'expire') {
            $this->handleExpiredPayment($payment);
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment has expired and the order has been cancelled.'
                ]
            ]);
        }

        // Jika transaksi dibatalkan atau ditolak.
        if (
            $transactionStatus === 'cancel' ||
            $transactionStatus === 'deny'
        ) {
            $this->handleFailedPayment($payment);

            throw ValidationException::withMessages([
                'payment' => [
                    'This payment can no longer be completed.'
                ]
            ]);
        }

        throw ValidationException::withMessages([
            'payment' => [
                'This payment transaction is no longer available.'
            ]
        ]);
    }

    // Payment validation

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

    // fungsi untuk memanggil webhook ketika payment berhasil
    public function sendPaymentSettledWebhook(Payment $payment): void {
        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->withBasicAuth(
                    config('services.n8n.username'),
                    config('services.n8n.password')
                )->post(
                    config('services.n8n.payment_webhook_url'),
                    [
                        'event' => 'payment.settled',
                        'order' => [
                            'id' => $payment->order_id,
                            'status' => $payment->order->status,
                            'payment_status' => $payment->payment_status,
                            'total_price' => $payment->order->total_price,
                            'items' => $payment->order->items->map(function ($item) {
                                return [
                                    'product_name' => $item->product?->name,
                                    'quantity' => $item->quantity,
                                ];
                            })->values()->all(),
                        ]
                    ]
                );

            if ($response->successful()) {
                Log::info('Payment settled webhook sent.', [
                    'payment_id' => $payment->id,
                ]);

                return;
            }

            // jika gagal kirim log
            Log::warning('Failed to send payment settled webhook.', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to send payment settled webhook.', [
                'payment_id' => $payment->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

}