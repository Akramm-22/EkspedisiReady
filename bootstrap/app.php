<?php

use App\Http\Middleware\EnsureCustomerEmailIsVerified;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\VerifyRecaptcha;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            // Modul API tambahan (auth, admin/CRUD, payment) didaftarkan
            // di sini dengan prefix `api` + middleware group `api`,
            // terpisah dari routes/api.php (modul publik) supaya tiap
            // file route tetap ringkas per modul.
            foreach (['auth', 'admin', 'payment'] as $module) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path("routes/{$module}.php"));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'recaptcha' => VerifyRecaptcha::class,
            'role' => EnsureUserHasRole::class,
            'verified.customer' => EnsureCustomerEmailIsVerified::class,
        ]);

        // Wajib untuk semua halaman Vue/Inertia (Home, Track, About) —
        // tanpa ini, request Inertia gagal karena tidak ada yang set
        // root view & shared props (lihat App\Http\Middleware\HandleInertiaRequests).
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // Login sudah terpadu (satu halaman /login untuk semua guard), jadi
        // guest yang kena redirect dari guard manapun (`web` atau `customer`)
        // cukup diarahkan ke satu tempat yang sama.
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
