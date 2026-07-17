<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'g-recaptcha-response' => ['required'],
        ]);


        $customer = Customer::create([
            ...collect($validated)->except('password')->all(),
            'password' => Hash::make($validated['password']),
        ]);

        $customer->sendEmailVerificationNotification();

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')->with('success', 'Registrasi berhasil! Cek email kamu untuk verifikasi akun.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
