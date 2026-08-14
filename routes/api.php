<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminOrderController;
use App\Http\Controllers\Api\AdminProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;

// Public Api
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);

// Raja ongkir api
Route::get('/regions/provinces', [RegionController::class, 'provinces']);
Route::get('/regions/cities/{provinceId}', [RegionController::class, 'cities']);
Route::get('/regions/districts/{cityId}', [RegionController::class, 'districts']);
Route::get('/regions/subdistricts/{districtId}', [RegionController::class, 'subdistricts']);

// Customer Api
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/user/profile', [AuthController::class, 'updateUser']);

    Route::get('/user/addresses', [AddressController::class, 'index']);

    Route::post('/user/addresses', [AddressController::class, 'store']);

    Route::put('/user/addresses/{id}', [AddressController::class, 'update']);

    Route::patch('/user/addresses/{id}/default', [AddressController::class, 'setDefault']);

    Route::delete('/user/addresses/{id}', [AddressController::class, 'destroy']);
    
    Route::get('/orders', [OrderController::class, 'index']);

    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::post('/orders', [OrderController::class, 'store']);
});

// Admin Api
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        Route::post('/products', [AdminProductController::class, 'store']);
        Route::put('/products/{id}', [AdminProductController::class, 'update']);
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{id}', [AdminOrderController::class, 'updateStatus']);
    });
