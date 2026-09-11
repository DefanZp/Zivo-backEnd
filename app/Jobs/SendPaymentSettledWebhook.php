<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendPaymentSettledWebhook implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3; // Maksimal percobaan untuk menjalankan job ini adalah 3 kali
    public array $backoff = [5, 15, 30]; // Waktu tunggu sebelum coba lagi
    public int $timeout = 15; // Waktu maksimal untuk menjalankan job ini adalah 15 detik

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $paymentId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = Payment::with([
            'order.user',
            'order.items.product',
            'order.payment'
        ])->findOrFail($this->paymentId);

        $response = Http::timeout(5)
            ->connectTimeout(3)
            ->withBasicAuth(
                config('services.n8n.username'),
                config('services.n8n.password')
            )->post(
                config('services.n8n.payment_webhook_url'),
                [
                    'event' => 'payment.settled',
                    'customer' => [
                        'name' => $payment->order->user->name,
                        'email' => $payment->order->user->email,
                    ],
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

        if (!$response->successful()) {
            throw new RuntimeException(
                "Payment settled webhook failed with status {$response->status()}."
            );
        }

        Log::info('Payment settled webhook sent successfully.', [
            'payment_id' => $payment->id,
        ]);

    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Payment settled webhook job failed permanently.', [
            'payment_id' => $this->paymentId,
            'message' => $exception->getMessage(),
        ]);
    }
}
