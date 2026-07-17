<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /** GET /api/v1/admin/customers?search= — autocomplete form input shipment. */
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            })
            ->latest()
            ->limit(20)
            ->get(['id', 'name', 'phone', 'address', 'city']);

        return response()->json(['data' => $customers]);
    }

    /**
     * POST /api/v1/admin/customers — kasir bisa daftarkan customer baru
     * langsung saat input shipment (walk-in). Karena `customers` adalah
     * entitas auth sendiri (butuh password, sesuai PDM), sistem generate
     * password acak — customer bisa reset lewat "lupa password" nanti.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
        ]);

        $customer = Customer::create([
            ...$validated,
            'password' => Hash::make(Str::random(16)),
        ]);

        return response()->json(['data' => $customer], 201);
    }
}
