<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    /** POST /api/v1/auth/register — registrasi customer, dapat Sanctum token (dipakai mobile). */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $customer = Customer::create([
            ...collect($validated)->except('password')->all(),
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'data' => ['customer' => $customer->only(['id', 'name', 'email']), 'token' => $customer->createToken('api')->plainTextToken],
        ], 201);
    }

    /** POST /api/v1/auth/login */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('email', $credentials['email'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        return response()->json([
            'data' => ['customer' => $customer->only(['id', 'name', 'email']), 'token' => $customer->createToken('api')->plainTextToken],
        ]);
    }

    /** POST /api/v1/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }
}
