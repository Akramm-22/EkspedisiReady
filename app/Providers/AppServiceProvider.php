<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 5 percobaan gagal login -> lockout 15 menit, sesuai PRD 3.2.
        // (Enforcement utama ada di AuthenticatedSessionController; limiter
        // ini sebagai lapisan tambahan di level route/HTTP.)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(15, 5)->by(strtolower($request->input('email')).'|'.$request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Karena /login sekarang dijaga oleh `guest:web,customer` (login
        // terpadu), orang yang sudah authenticated di salah satu guard dan
        // membuka /login harus diarahkan ke dashboard-nya sendiri — bukan
        // ke fallback default Laravel (route 'dashboard'/'home').
        RedirectIfAuthenticated::redirectUsing(function () {
            if (Auth::guard('customer')->check()) {
                return route('customer.dashboard');
            }

            if (Auth::guard('web')->check()) {
                return Auth::guard('web')->user()->isCourier()
                    ? route('courier.index')
                    : route('admin.dashboard');
            }

            return route('home');
        });
    }
}
