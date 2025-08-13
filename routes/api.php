<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\BatchImageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QrAccessLogController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public QR code access
Route::get('/batches/{batch}/qr', [BatchController::class, 'showQr']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/batches', [DashboardController::class, 'dashboardBatches']);

    Route::get('/batches/recent', [DashboardController::class, 'recentBatches']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Mobile specific endpoints
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('user.profile');
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // Categories
    Route::get('/categories/all-public', [CategoryController::class, 'allPublic']);
    Route::apiResource('categories', CategoryController::class);

    // Products
    Route::apiResource('products', ProductController::class);

    // Batches
    Route::get('/batches/all-farmer', [BatchController::class, 'allFarmerBatches']);
    Route::get('/batches/details/{batch}', [BatchController::class, 'details']);
    Route::apiResource('batches', BatchController::class);

    // Regenerate QR
    Route::post('/batches/{batch}/regenerate-qr', [BatchController::class, 'regenerateQr']);
    Route::get('/batches/{batch}/access-logs', [BatchController::class, 'accessLogs']);

    // Batch Images
    Route::apiResource('batch-images', BatchImageController::class);

    // Reviews
    Route::apiResource('reviews', ReviewController::class);

    // QR Access Logs (Admin only)
    Route::get('/qr-access-logs', [QrAccessLogController::class, 'index']);
});
