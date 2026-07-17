<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shipment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /** POST /api/v1/shipments/{shipment}/payments — catat/charge pembayaran (token Sanctum milik customer). */
    public function store(Request $request, Shipment $shipment): JsonResponse
    {
        // Sanctum resolve $request->user() ke pemilik token langsung
        // (Customer), tidak lewat guard session `customer`.
        abort_unless($shipment->sender_id === $request->user()->id, 403);

        if ($shipment->payments()->where('payment_status', 'paid')->exists()) {
            return response()->json([
                'message' => 'Shipment ini sudah lunas, tidak bisa dibayar lagi.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:transfer,e-wallet'],
        ]);

        $payment = $this->paymentService->record($shipment, $validated['payment_method']);

        return response()->json([
            'message' => $payment->usesMidtrans()
                ? 'Transaksi dibuat, selesaikan pembayaran lewat Midtrans.'
                : 'Pembayaran dicatat, menunggu konfirmasi kasir.',
            'data' => [
                'id' => $payment->id,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'amount' => $payment->amount,
                'snap_token' => $payment->midtrans_snap_token,
            ],
        ], 201);
    }

    /** GET /api/v1/payments/{payment} — cek status (polling dari mobile). */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json(['data' => $payment]);
    }

    /** PATCH /api/v1/admin/payments/{payment}/verify — kasir konfirmasi pembayaran cash. */
    public function verify(Request $request, Payment $payment): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin|manager|cashier'), 403);

        $this->paymentService->markAsPaid($payment);

        return response()->json(['message' => 'Pembayaran berhasil diverifikasi.', 'data' => $payment->fresh()]);
    }
}
