<?php

use App\Http\Controllers\Api\CampaignCategoryController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CurrentUserController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Route;

// Authenticated endpoints
Route::middleware('auth:sanctum')->group(function () {
    // Auth / Profile
    Route::get('/user', CurrentUserController::class);
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);

    // Donations
    Route::get('/donations', [DonationController::class, 'index']);
    Route::get('/donations/summary', [DonationController::class, 'summary']);
    Route::get('/donations/{id}', [DonationController::class, 'show']);
});

// Public API — Campaigns
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/featured', [CampaignController::class, 'featured']);
Route::get('/campaigns/{slug}', [CampaignController::class, 'show']);
Route::get('/campaigns/{slug}/articles', [CampaignController::class, 'articles']);

// Public API — Categories
Route::get('/categories', [CampaignCategoryController::class, 'index']);
Route::get('/categories/{id}', [CampaignCategoryController::class, 'show']);

// Authenticated — Create donation (needs campaign slug)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/campaigns/{slug}/donations', [DonationController::class, 'store']);
});

// Webhook (no auth — called by Midtrans)
Route::post('webhook/midtrans', [MidtransController::class, 'callback']);
