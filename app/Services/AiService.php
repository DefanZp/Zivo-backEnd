<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    public function chat(string $thread_id, string $user_message, string $access_token): array
    {
        $response = Http::withToken($access_token)
            ->timeout(300)
            ->post('http://127.0.0.1:8001/chat', [
                    'thread_id' => $thread_id,
                    'user_message' => $user_message,
                ]);
        
        $response->throw();

        return $response->json();
    }   

    
}