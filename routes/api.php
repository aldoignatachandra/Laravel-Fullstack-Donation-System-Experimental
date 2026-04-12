<?php

use App\Http\Controllers\Api\CurrentUserController;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Route;

Route::get('/user', CurrentUserController::class)->middleware('auth:sanctum');

Route::post('webhook/midtrans', [MidtransController::class, 'callback']);
