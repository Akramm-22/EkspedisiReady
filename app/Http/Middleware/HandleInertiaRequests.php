<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Data yang selalu tersedia di semua halaman Vue/Inertia. Yang
     * paling penting: status login customer (guard `customer`), supaya
     * PublicLayout.vue bisa nampilin "Dashboard Saya" alih-alih
     * "Masuk"/"Daftar" begitu customer sudah login.
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'customer' => $request->user('customer')?->only(['id', 'name', 'email']),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            // Beberapa halaman (mis. Customer/Kirim.vue) pakai native <form>
            // POST biasa, bukan Inertia router.post() — butuh token CSRF
            // di-share manual di sini, jangan cuma andalin meta tag.
            'csrf_token' => fn () => csrf_token(),
        ];
    }
}
