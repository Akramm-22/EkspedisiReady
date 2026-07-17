<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Rate;
use App\Services\PaymentService;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CreateShipmentController extends Controller
{
    public function __construct(
        private ShipmentService $shipmentService,
        private PaymentService $paymentService,
    ) {
    }

    /** GET /customer/kirim — form kirim paket: isi tujuan, penerima, barang, sekaligus pilih bayar. */
    public function create(Request $request): Response
    {
        $errors = $request->session()->get('errors');

        return Inertia::render('Customer/Kirim', [
            'branches' => Branch::orderBy('city')->get(['id', 'name', 'city']),
            'errors' => $errors ? $errors->toArray() : [],
        ]);
    }


    /**
     * POST /customer/kirim
     * Bikin shipment + catat pembayaran dalam 1 submit. Penerima gak
     * wajib punya akun duluan — kalau emailnya belum ada di `customers`,
     * sistem otomatis daftarin sebagai customer baru (password random,
     * bisa di-reset lewat "lupa password" kalau dia mau login sendiri).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin_branch_id' => ['required', 'exists:branches,id'],
            'destination_branch_id' => ['required', 'exists:branches,id', 'different:origin_branch_id'],

            'receiver_name' => ['required', 'string', 'max:50'],
            'receiver_email' => ['required', 'email', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:15'],
            'receiver_address' => ['required', 'string'],
            'receiver_city' => ['required', 'string', 'max:255'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.weight' => ['required', 'numeric', 'min:0.1'],

            'payment_method' => ['required', 'in:transfer,e-wallet'],
        ]);

        $originBranch = Branch::findOrFail($validated['origin_branch_id']);
        $destinationBranch = Branch::findOrFail($validated['destination_branch_id']);

        $rate = Rate::where('origin_city', $originBranch->city)
            ->where('destination_city', $destinationBranch->city)
            ->first();

        if (! $rate) {
            return back()->withInput()->with('error', 'Rute ini belum tersedia. Silakan pilih cabang lain.');
        }

        $shipment = DB::transaction(function () use ($validated, $originBranch, $destinationBranch, $rate, $request) {
            $receiver = Customer::firstOrCreate(
                ['email' => $validated['receiver_email']],
                [
                    'name' => $validated['receiver_name'],
                    'phone' => $validated['receiver_phone'],
                    'address' => $validated['receiver_address'],
                    'city' => $validated['receiver_city'],
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            return $this->shipmentService->create([
                'sender_id' => $request->user('customer')->id,
                'receiver_id' => $receiver->id,
                'origin_branch_id' => $originBranch->id,
                'destination_branch_id' => $destinationBranch->id,
                'rate_id' => $rate->id,
                'items' => $validated['items'],
            ]);
        });

        $this->paymentService->record($shipment, $validated['payment_method']);

        return Inertia::location(route('payment.show', $shipment));
    }
}

