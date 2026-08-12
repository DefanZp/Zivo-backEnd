<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ){}

    public function index() {
        
        $statisticData = $this->dashboardService
            ->getStatisticData();
        
        $latestOrder = $this->dashboardService
            ->getlatestOrderData();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard retrieved successfully',
            'data' => [
                'statistic_data' => $statisticData,
                'latest_orders_data' => $latestOrder,
            ]
        ], 200);
    }
}
