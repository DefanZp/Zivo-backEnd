<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

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
        ]);
    }
}
