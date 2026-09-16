<?php

namespace App\Services;

use App\Models\AgentState;

class AgentStateService
{
    public function getState(string $sessionId): ?AgentState
    {
        return AgentState::where('session_id', $sessionId)->first();
    }

    public function saveSelectedProduct(string $sessionId, int $productId): AgentState
    {
       return AgentState::updateOrCreate(
            [
                'session_id' => $sessionId
            ],
            [
                'selected_product_id' => $productId
            ]
       );
    }
}