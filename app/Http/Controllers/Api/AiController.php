<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(
        private AiService $aiService
    ){}

    public function chat(Request $request)
    {
        $request->validate([
            'thread_id' => 'required|string',
            'user_message' => 'required|string',
        ]);

        $access_token = $request->bearerToken();

        $result = $this->aiService->chat(
            $request->thread_id,
            $request->user_message,
            $access_token
        );

        return response()->json($result);
    }
}
