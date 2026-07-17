<?php

use App\Http\Controllers\Api\Public\LandingPageController;
use App\Http\Controllers\Api\Public\RateCalculatorController;
use App\Http\Controllers\Api\Public\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul: Landing Page (Publik) + Tracking Publik
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    Route::get('/home', [LandingPageController::class, 'home']);
    Route::get('/branches', [LandingPageController::class, 'branches']);

    Route::post('/rates/calculate', [RateCalculatorController::class, 'calculate'])
        ->middleware('throttle:30,1');

    Route::get('/track/{tracking_number}', [TrackingController::class, 'show'])
        ->middleware('throttle:20,1');
});
