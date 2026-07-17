<x-admin-layout title="Laporan">
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-100 bg-white p-5">
        <div>
            <label class="text-xs font-semibold text-slate-500">Dari Tanggal</label>
            <input type="date" name="from" value="{{ $from }}" class="mt-1 rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Sampai Tanggal</label>
            <input type="date" name="to" value="{{ $to }}" class="mt-1 rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <button type="submit" class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Terapkan</button>

        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to, 'format' => 'xlsx']) }}"
               class="rounded-xl bg-brand-gradient-soft px-4 py-2.5 text-sm font-semibold text-brand-700">Export Excel</a>
            <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to, 'format' => 'pdf']) }}"
               class="rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white">Export PDF</a>
        </div>
    </form>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 bg-white p-6">
            <h2 class="font-bold text-slate-800">Grafik Operasional</h2>
            <p class="mt-3 text-3xl font-extrabold text-slate-800">{{ $operational['total_shipments'] }}</p>
            <p class="text-xs text-slate-500">Total pengiriman per status</p>
            @php $maxStatus = max([1, ...$operational['by_status']->values()->all()]); @endphp
            <div class="mt-5 space-y-3 border-t border-slate-100 pt-4">
                @forelse ($operational['by_status'] as $status => $total)
                    <div>
                        <div class="flex justify-between text-xs text-slate-500">
                            <span>{{ \App\Enums\ShipmentStatus::from($status)->label() }}</span>
                            <span class="font-semibold text-slate-700">{{ $total }}</span>
                        </div>
                        <div class="mt-1 h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-brand-gradient" style="width: {{ max(4, round($total / $maxStatus * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data di periode ini.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-6">
            <h2 class="font-bold text-slate-800">Grafik Keuangan</h2>
            <p class="mt-3 text-3xl font-extrabold text-transit-600">Rp{{ number_format($financial['total_revenue'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">{{ $financial['total_transactions'] }} transaksi lunas — per metode bayar</p>
            @php $maxMethod = max([1, ...$financial['by_method']->values()->all()]); @endphp
            <div class="mt-5 space-y-3 border-t border-slate-100 pt-4">
                @forelse ($financial['by_method'] as $method => $total)
                    <div>
                        <div class="flex justify-between text-xs text-slate-500">
                            <span>{{ ucfirst(str_replace('-', ' ', $method)) }}</span>
                            <span class="font-semibold text-slate-700">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-1 h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-transit-400 to-transit-600" style="width: {{ max(4, round($total / $maxMethod * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data di periode ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
