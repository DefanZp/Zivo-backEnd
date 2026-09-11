<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendOrderCreatedWebhook implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3; // Maksimal percobaan untuk menjalankan job ini adalah 3 kali
    public array $backoff = [5, 15, 30]; // Waktu tunggu sebelum coba lagi
    public int $timeout = 15; // Waktu maksimal untuk menjalankan job ini adalah 15 detik

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $orderId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::with([
            'items.product',
            'payment',
        ])->findOrFail($this->orderId);

        $response = Http::timeout(5)
            ->connectTimeout(3)
            ->withBasicAuth(
                config('services.n8n.username'),
                config('services.n8n.password')
            )->post(
                config('services.n8n.order_webhook_url'),
                [
                    'event' => 'order.created',

                    'order' => [
                        'id' => $order->id,
                        'status' => $order->status,
                        'payment_status' => $order->payment->payment_status,
                        'total_price' => $order->total_price,

                        'items' => $order->items->map(function ($item) {
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
                "Order created webhook failed with status {$response->status()}."
            );
        }

        Log::info('Order created webhook sent successfully.', [
            'order_id' => $order->id,
        ]);

    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Order created webhook job failed permanently.', [
            'order_id' => $this->orderId,
            'message' => $exception->getMessage(),
        ]);
    }
}
