<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\MidtransGateway;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private MidtransGateway $midtrans,
    ) {
    }

    /**
     * POST /api/v1/webhooks/midtrans
     * Endpoint publik (TIDAK pakai Sanctum) — keamanan lewat verifikasi
     * signature SHA512 sesuai spesifikasi Midtrans, bukan auth token.
     * Daftarkan URL ini di dashboard Midtrans (Settings > Configuration).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->midtrans->verifySignature($payload)) {
            Log::warning('Midtrans webhook: invalid signature', ['payload' => $payload]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = $this->paymentService->handleMidtransNotification($payload);

        return response()->json(['message' => 'OK', 'payment_id' => $payment->id]);
    }
}
