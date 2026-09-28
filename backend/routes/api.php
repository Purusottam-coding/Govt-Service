<?php

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payment/initiate', [PaymentController::class, 'initiate']);

Route::post('/payment/callback', [PaymentController::class, 'callback']);

Route::get('/payment/status/{transactionId}', [PaymentController::class, 'status']);