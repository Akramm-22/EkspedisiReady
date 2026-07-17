<?php

use App\Http\Controllers\Api\Payment\MidtransWebhookController;
use App\Http\Controllers\Api\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul: Payment (API, Sanctum) — dipakai mobile customer
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/shipments/{shipment}/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::patch('/admin/payments/{payment}/verify', [PaymentController::class, 'verify']);
});

// Webhook Midtrans — TIDAK pakai Sanctum, keamanan lewat verifikasi
// signature di dalam MidtransWebhookController.
Route::post('/v1/webhooks/midtrans', MidtransWebhookController::class);
