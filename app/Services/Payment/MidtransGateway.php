<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransGateway
{
    private string $serverKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->baseUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    /**
     * Midtrans menolak email dengan whitespace tersembunyi (mis. dari
     * copy-paste / import CSV) atau format yang tidak valid, meski lolos
     * validasi Laravel `email` rule di form registrasi. Bersihkan &
     * validasi ulang di sini supaya gagal cepat dengan pesan jelas
     * daripada error generik dari Midtrans.
     */
    private function sanitizeEmail(?string $email): string
    {
        $email = trim((string) $email);
        // Buang karakter kontrol/zero-width yang kadang lolos dari input.
        $email = preg_replace('/[\x00-\x1F\x7F\x{200B}-\x{200D}\x{FEFF}]/u', '', $email) ?? '';

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Midtrans charge: invalid customer email, using fallback', ['original' => $email]);

            return 'customer@drgekspedisi.id';
        }

        return $email;
    }

    /** Midtrans butuh format nomor telepon numerik (boleh diawali +), tanpa spasi/strip. */
    private function sanitizePhone(?string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', (string) $phone) ?? '';

        return $phone !== '' ? $phone : '000000000000';
    }

    /**
     * Buat Snap transaction. Satu Snap page menangani transfer/VA maupun
     * e-wallet sekaligus — payment_method di DB kita tetap enum sederhana
     * (transfer/e-wallet) sesuai PDM, Midtrans yang urus detail metodenya.
     */
    public function charge(Payment $payment): array
    {
        $shipment = $payment->shipment()->with('sender')->first();
        $sender = $shipment->sender;

        if (! $sender) {
            throw new \RuntimeException('Data pengirim tidak ditemukan, tidak bisa membuat transaksi pembayaran.');
        }

        $email = $this->sanitizeEmail($sender->email);
        $phone = $this->sanitizePhone($sender->phone);

        $response = Http::withBasicAuth($this->serverKey, '')
            ->when(app()->environment(['local', 'testing']), fn ($http) => $http->withoutVerifying())
            ->acceptJson()
            ->post("{$this->baseUrl}/transactions", [
                'transaction_details' => [
                    'order_id' => $payment->midtrans_order_id,
                    'gross_amount' => (int) $payment->amount,
                ],
                'customer_details' => [
                    'first_name' => trim($sender->name) ?: 'Pelanggan',
                    'phone' => $phone,
                    'email' => $email,
                ],
                'item_details' => [[
                    'id' => $shipment->tracking_number,
                    'price' => (int) $payment->amount,
                    'quantity' => 1,
                    'name' => "Ongkir {$shipment->tracking_number}",
                ]],
                'enabled_payments' => [
                    'bank_transfer', 'echannel', 'permata_va',
                    'gopay', 'shopeepay', 'other_qris',
                    'credit_card',
                ],
                'credit_card' => [
                    'secure' => true,
                ],
                'callbacks' => [
                    'finish' => config('app.url')."/pembayaran/{$payment->midtrans_order_id}/selesai",
                ],
            ]);

        if ($response->failed()) {
            Log::error('Midtrans charge failed', ['response' => $response->json(), 'payment_id' => $payment->id]);

            throw new \RuntimeException('Gagal membuat transaksi pembayaran Midtrans.');
        }

        return $response->json();
    }

    /**
     * Cek status transaksi langsung ke Midtrans Core API (bukan Snap).
     * Dipakai sebagai fallback aktif: webhook HTTP Notification kadang
     * tidak sampai (mis. server dev tanpa URL publik/ngrok), jadi begitu
     * Snap.js melapor onSuccess/onPending di sisi customer, kita
     * langsung tanya status sebenarnya ke Midtrans daripada nunggu pasif.
     */
    public function checkStatus(string $orderId): array
    {
        $baseUrl = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $response = Http::withBasicAuth($this->serverKey, '')
            ->when(app()->environment(['local', 'testing']), fn ($http) => $http->withoutVerifying())
            ->acceptJson()
            ->get("{$baseUrl}/{$orderId}/status");

        if ($response->failed() && $response->status() !== 404) {
            Log::error('Midtrans status check failed', ['order_id' => $orderId, 'response' => $response->json()]);

            throw new \RuntimeException('Gagal memeriksa status transaksi Midtrans.');
        }

        return $response->json() ?? [];
    }

    /** Verifikasi signature webhook: SHA512(order_id+status_code+gross_amount+server_key). */
    public function verifySignature(array $payload): bool
    {
        $expected = hash('sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$this->serverKey
        );

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }

    /**
     * Map status transaksi Midtrans -> payment_status enum kita.
     *
     * Midtrans transaction_status values:
     * - capture       → paid (credit card successful)
     * - settlement    → paid (transfer/VA/e-wallet successful)
     * - pending       → pending
     * - deny          → failed
     * - cancel        → failed
     * - expire        → expired
     * - refund        → refunded
     * - partial_refund → refunded (partial)
     */
    public function mapStatus(string $transactionStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => 'paid',
            'pending' => 'pending',
            'expire' => 'expired',
            'refund', 'partial_refund' => 'refunded',
            default => 'failed', // deny, cancel, and unknown
        };
    }
}