<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * GET /admin/payments — daftar pembayaran untuk diverifikasi kasir.
     * Kasir bisa lihat semua pembayaran pending (cash) dan mengonfirmasinya.
     */
    public function index(Request $request): View
    {
        $payments = Payment::with(['shipment.sender', 'shipment.originBranch'])
            ->when($request->status, fn ($q) => $q->where('payment_status', $request->status))
            ->when($request->method, fn ($q) => $q->where('payment_method', $request->method))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * PATCH /admin/payments/{payment}/verify — kasir konfirmasi pembayaran cash.
     */
    public function verify(Payment $payment): RedirectResponse
    {
        abort_unless($payment->payment_method === 'cash', 400, 'Pembayaran non-cash tidak bisa diverifikasi manual.');
        abort_unless($payment->payment_status === 'pending', 400, 'Pembayaran ini sudah diproses.');

        $this->paymentService->markAsPaid($payment);

        return back()->with('success', 'Pembayaran berhasil diverifikasi. Kurir akan otomatis ditugaskan.');
    }
}