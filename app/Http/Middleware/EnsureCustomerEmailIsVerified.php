<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Versi khusus guard `customer` dari `Illuminate\Auth\Middleware\EnsureEmailIsVerified`.
 *
 * Middleware bawaan Laravel ('verified') selalu cek `$request->user()` pakai
 * guard DEFAULT (di project ini: 'web'), bukan guard yang lagi dipakai di
 * route group. Karena guard customer login lewat `auth:customer`, bukan
 * `auth:web`, middleware bawaan itu akan selalu menganggap customer belum
 * login/belum verifikasi — meskipun sudah.
 *
 * Catatan penting: parameter setelah tanda titik dua pada middleware bawaan
 * ('verified:xxx') itu nama ROUTE tujuan redirect, BUKAN nama guard. Jadi
 * `verified:customer` bukan berarti "cek guard customer" — itu artinya
 * "kalau belum verifikasi, redirect ke route bernama 'customer'", yang
 * memang tidak ada -> makanya muncul error "Route [customer] not defined".
 *
 * Middleware ini menghindari kebingungan itu dengan eksplisit memeriksa
 * guard `customer`.
 */
class EnsureCustomerEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = $request->user('customer');

        if (! $customer ||
            ($customer instanceof MustVerifyEmail && ! $customer->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Alamat email kamu belum diverifikasi.')
                : Redirect::guest(URL::route('verification.notice'))
                    ->with('warning', 'Your email address has not been verified. Please verify your email before accessing your account.');
        }

        return $next($request);
    }
}
