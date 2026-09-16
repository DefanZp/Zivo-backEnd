<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgentStateService;
use Illuminate\Http\Request;

class AgentStateController extends Controller
{
    public function __construct(
        private AgentStateService $agentStateService
    ) {}

    public function show(Request $request)
    {
        $sessionId = $request->query('session_id');

        $state = $this->agentStateService->getState($sessionId);

        return response()->json([
            'success' => true,
            'data' => $state,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'session_id' => 'required|string',
            'selected_product_id' => 'required|integer',
        ]);

        $state = $this->agentStateService->saveSelectedProduct(
            $validatedData['session_id'],
            $validatedData['selected_product_id']
        );

        return response()->json([
            'success' => true,
            'data' => $state,
        ]);
    }
}
