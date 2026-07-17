<x-admin-layout title="Verifikasi Pembayaran">
    <div>
        <p class="text-sm text-slate-500">Konfirmasi pembayaran cash dari customer.</p>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">No. Resi</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Metode</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-800">{{ $payment->shipment->tracking_number ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $payment->shipment->sender->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ ucfirst($payment->payment_method) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($payment->payment_status === 'pending')
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                            @elseif ($payment->payment_status === 'paid')
                                <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">Lunas</span>
                            @elseif ($payment->payment_status === 'failed')
                                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Gagal</span>
                            @elseif ($payment->payment_status === 'expired')
                                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-500">Kadaluarsa</span>
                            @else
                                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-500">{{ ucfirst($payment->payment_status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if ($payment->payment_status === 'pending' && $payment->payment_method === 'cash')
                                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" onsubmit="return confirm('Konfirmasi pembayaran ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-xl bg-brand-gradient px-4 py-2 text-xs font-semibold text-white hover:opacity-90">
                                        Konfirmasi Lunas
                                    </button>
                                </form>
                            @elseif ($payment->payment_status === 'pending' && $payment->usesMidtrans())
                                <span class="text-xs text-slate-400">Midtrans</span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada data pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $payments->links() }}</div>
</x-admin-layout>