<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Validasi Google reCAPTCHA v2.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('services.recaptcha.secret_key')) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Konfigurasi reCAPTCHA server belum tersedia. Silakan coba lagi nanti.',
            ]);
        }


        $token = $request->input('g-recaptcha-response');

        if (! $token) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Verifikasi captcha wajib diisi.',
            ]);
        }

        try {
            $response = Http::when(app()->environment(['local', 'testing']), fn ($http) => $http->withoutVerifying())
                ->asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ])->json();
        } catch (\Throwable $e) {
            // Google gak bisa diakses (jaringan lambat/diblokir) — jangan
            // sampai user kena hang nunggu, anggap gagal verifikasi dan
            // suruh coba lagi daripada request menggantung lama.
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Verifikasi captcha gagal (server captcha tidak dapat dihubungi). Coba lagi.',
            ]);
        }

        if (! ($response['success'] ?? false)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Verifikasi captcha gagal, silakan coba lagi.',
            ]);
        }

        return $next($request);
    }
}
