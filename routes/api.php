<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RealizationController;
use App\Http\Controllers\EstimateController;

// Endpoints publics
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/slug/{slug}', [ProductController::class, 'showBySlug']);

Route::get('/realizations', [RealizationController::class, 'index']);
Route::get('/realizations/{id}', [RealizationController::class, 'show']);

// Estimates publics (sans auth)
Route::get('/estimates/email/{email}', [EstimateController::class, 'byEmail']);
Route::post('/estimates', [EstimateController::class, 'store']);
Route::get('/estimates/{id}', [EstimateController::class, 'show']);

// Endpoints protégés avec Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Products management
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Realizations management
    Route::post('/realizations', [RealizationController::class, 'store']);
    Route::put('/realizations/{id}', [RealizationController::class, 'update']);
    Route::delete('/realizations/{id}', [RealizationController::class, 'destroy']);

    // Estimates management
    Route::get('/estimates', [EstimateController::class, 'index']);
    Route::get('/estimates/user/my', [EstimateController::class, 'myEstimates']);
    Route::put('/estimates/{id}', [EstimateController::class, 'update']);
    Route::patch('/estimates/{id}/status', [EstimateController::class, 'updateStatus']);
    Route::delete('/estimates/{id}', [EstimateController::class, 'destroy']);
    Route::get('/estimates/statistics/overview', [EstimateController::class, 'statistics']);
    Route::post('/estimates/{id}/duplicate', [EstimateController::class, 'duplicate']);
});
