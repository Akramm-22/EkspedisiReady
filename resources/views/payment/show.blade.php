<x-auth-layout title="Pembayaran">
    <div class="w-full max-w-md">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Pembayaran</h1>
                <p class="mt-1 text-sm text-slate-500">Shipment {{ $shipment->tracking_number }}</p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                ← Kembali
            </a>
        </div>
        @if (session('success'))
            <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">{{ session('success') }}</div>
        @endif

        <div class="mt-6 rounded-2xl border border-slate-100 p-5">
            <div class="flex justify-between text-base">
                <span class="font-bold text-slate-800">Total Tagihan</span>
                <span class="font-extrabold text-transit-600">Rp{{ number_format($shipment->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        @php
            // Satu jalur pembayaran: langsung lewat Midtrans, gak ada pilihan
            // metode lagi. Data lama (cash) cuma ditampilkan buat kompatibilitas.
            $isPaid = $latestPayment && $latestPayment->payment_status === 'paid';
            $hasSnapToken = $latestPayment && $latestPayment->usesMidtrans() && $latestPayment->midtrans_snap_token && $latestPayment->payment_status !== 'paid';
            $isMidtransFailed = $latestPayment && $latestPayment->usesMidtrans() && $latestPayment->payment_status === 'failed';
        @endphp

        @if ($isPaid)
            <div class="mt-6 rounded-2xl px-5 py-4 text-center text-sm font-semibold bg-brand-50 text-brand-700">
                ✅ Pembayaran lunas pada {{ $latestPayment->payment_date?->format('d/m/Y') }}
            </div>
        @elseif ($hasSnapToken)
            <button id="pay-now-btn" type="button"
                    class="mt-2 w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white hover:opacity-90">
                Bayar Sekarang
            </button>
            <p id="snap-status" class="mt-3 text-center text-xs text-slate-400"></p>

            <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const snapToken = @json($latestPayment->midtrans_snap_token);
                    const syncUrl = @json(route('payment.sync', $shipment));
                    const csrfToken = @json(csrf_token());
                    const statusEl = document.getElementById('snap-status');
                    const btn = document.getElementById('pay-now-btn');

                    // Jangan cuma percaya webhook Midtrans (bisa gak pernah sampai
                    // di server dev/local tanpa URL publik). Begitu Snap.js
                    // melapor sukses/pending, langsung tanya status SEBENARNYA
                    // ke Midtrans lewat backend, baru reload.
                    const syncThenReload = async (fallbackMessage) => {
                        statusEl.textContent = 'Memeriksa status pembayaran...';
                        try {
                            await fetch(syncUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                            });
                        } catch (e) {
                            statusEl.textContent = fallbackMessage;
                        } finally {
                            window.location.reload();
                        }
                    };

                    const openSnap = () => {
                        snap.pay(snapToken, {
                            onSuccess: () => syncThenReload('Pembayaran berhasil, memperbarui status...'),
                            onPending: () => syncThenReload('Selesaikan sesuai instruksi yang muncul di popup.'),
                            onError: () => {
                                statusEl.textContent = 'Pembayaran gagal. Silakan coba lagi.';
                            },
                            onClose: () => {
                                // Popup ditutup tanpa hasil final — tetap sync, siapa
                                // tahu pembayaran sudah settle di sisi Midtrans.
                                syncThenReload('Popup ditutup. Memeriksa status...');
                            },
                        });
                    };

                    btn.addEventListener('click', openSnap);
                    // Buka otomatis begitu halaman dimuat — langsung bayar, gak ada jeda.
                    openSnap();
                });
            </script>
        @else
            @if ($isMidtransFailed)
                <div class="mt-6 rounded-2xl bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    <p class="font-semibold">⚠️ Transaksi pembayaran sebelumnya gagal dibuat.</p>
                    @php($reason = $latestPayment->midtrans_raw_response['error'] ?? $latestPayment->midtrans_raw_response['status_message'] ?? null)
                    @if ($reason)
                        <p class="mt-1 text-xs text-rose-600">{{ $reason }}</p>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('payment.store', $shipment) }}" class="mt-6 space-y-3">
                @csrf
                <input type="hidden" name="payment_method" value="transfer">
                <button type="submit" class="w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white hover:opacity-90">
                    Bayar Sekarang
                </button>
            </form>
        @endif
    </div>
</x-auth-layout>
