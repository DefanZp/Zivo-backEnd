<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;



class DashboardService
{
    public function getlatestOrderData() {
        return $this->getLatestOrders();
    }

    public function getStatisticData(): array {
        return [
            'total_products' => $this->getTotalProducts(),
            'total_orders' => $this->getTotalOrders(),
            'pending_orders' => $this->getPendingOrders(),
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }

    private function getTotalProducts(): int {
        $products = Product::count();
        return $products;
    }

    private function getTotalOrders(): int {
        $orders = Order::count();
        return $orders;
    }

    private function getPendingOrders(): int {
        $pendingOrders = Order::where('status', 'pending')
            ->count();
        return $pendingOrders;
    }

    private function getTotalRevenue(): int {
        $revenue = Order::where('status', 'completed')
            ->sum('total_price');
        return $revenue;
    }

    private function getLatestOrders() {
        $latestOrders = Order::select([
            'id', 
            'user_id', 
            'recipient_name', 
            'total_price', 
            'status', 
            'created_at'
            ])
            ->latest()
            ->take(5)
            ->get();
        return $latestOrders;
    }
}
