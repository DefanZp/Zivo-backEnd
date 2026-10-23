<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    public function chat(string $thread_id, string $user_message, string $access_token): array
    {
        $baseUrl = config('services.ai.url');

        $response = Http::withToken($access_token)
            ->timeout(300)
            ->post($baseUrl . '/chat', [
                    'thread_id' => $thread_id,
                    'user_message' => $user_message,
                ]);
        
        $response->throw();

        return $response->json();
    }   

    
}