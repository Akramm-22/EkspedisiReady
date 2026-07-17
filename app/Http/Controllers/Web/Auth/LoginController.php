<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Satu halaman login (`/login`) untuk SEMUA role: Admin, Manager,
     * Kasir, Kurir (guard `web`, tabel `users`) maupun Customer (guard
     * `customer`, tabel `customers`). Tidak ada lagi halaman login
     * terpisah per role — deteksi role dilakukan otomatis di `store()`
     * setelah kredensial cocok, lalu diarahkan ke dashboard masing-masing.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = strtolower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.ceil($seconds / 60).' menit.',
            ]);
        }

        $remember = $request->boolean('remember');

        // Coba guard staf (`web`) lebih dulu, baru guard `customer`. Kedua
        // guard punya tabel & hash password sendiri-sendiri, dan pesan
        // error untuk kredensial salah selalu identik di kedua jalur —
        // jadi tidak ada informasi yang bocor soal guard mana yang dicoba,
        // atau apakah email terdaftar di salah satunya.
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $guard = 'web';
        } elseif (Auth::guard('customer')->attempt($credentials, $remember)) {
            $guard = 'customer';
        } else {
            RateLimiter::hit($throttleKey, 900);

            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended($this->dashboardFor($guard));
    }

    /** Tentukan URL dashboard tujuan berdasarkan guard yang berhasil login & role user. */
    private function dashboardFor(string $guard): string
    {
        if ($guard === 'customer') {
            return route('customer.dashboard');
        }

        $user = Auth::guard('web')->user();

        return $user->isCourier() ? route('courier.index') : route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
