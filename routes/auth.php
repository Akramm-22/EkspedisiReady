<?php

use App\Http\Controllers\Api\Auth\CustomerAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul: Autentikasi API (Sanctum, khusus customer — dipakai mobile app)
|--------------------------------------------------------------------------
| Staf internal (admin/manager/cashier/courier) login lewat halaman
| Blade session (routes/web.php), tidak lewat API ini.
*/
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register'])->middleware(['throttle:5,1', 'recaptcha']);
    Route::post('/login', [CustomerAuthController::class, 'login'])->middleware(['throttle:10,1']);
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->middleware('auth:sanctum');
});
