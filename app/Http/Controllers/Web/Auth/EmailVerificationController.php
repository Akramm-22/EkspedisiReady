<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /** GET /customer/email/verify — halaman "cek email kamu" kalau belum verifikasi. */
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user('customer')?->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * GET /customer/email/verify/{id}/{hash} — link dari email verifikasi.
     * Route ini di-generate lewat `URL::temporarySignedRoute` di
     * VerifyEmail notification, jadi wajib pakai middleware `signed`.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user('customer')->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard')->with('success', 'Email sudah terverifikasi sebelumnya.');
        }

        if ($request->user('customer')->markEmailAsVerified()) {
            event(new Verified($request->user('customer')));
        }

        return redirect()->route('customer.dashboard')->with('success', 'Email berhasil diverifikasi!');
    }

    /** POST /customer/email/verification-notification — kirim ulang email verifikasi. */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user('customer')->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        $request->user('customer')->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru sudah dikirim ke email kamu.');
    }
}
