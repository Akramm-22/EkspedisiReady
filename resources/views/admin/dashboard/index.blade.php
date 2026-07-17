<x-admin-layout title="Dashboard">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <p class="text-xs font-medium text-slate-500">Pengiriman Hari Ini</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ $summary['shipments_today'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <p class="text-xs font-medium text-slate-500">Pengiriman Bulan Ini</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ $summary['shipments_this_month'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <p class="text-xs font-medium text-slate-500">Revenue Bulan Ini</p>
            <p class="mt-2 text-2xl font-extrabold text-transit-600">Rp{{ number_format($summary['revenue_this_month'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5">
            <p class="text-xs font-medium text-slate-500">Kurir Aktif</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-800">{{ $summary['active_couriers'] }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-100 bg-white p-6 lg:col-span-2">
            <h2 class="font-bold text-slate-800">Pengiriman Terbaru</h2>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse ($recentShipments as $shipment)
                    @if (auth()->user()->hasRole('admin|cashier'))
                        <a href="{{ route('admin.shipments.show', $shipment) }}" class="flex items-center justify-between gap-4 py-3 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $shipment->tracking_number }}</p>
                                <p class="text-xs text-slate-500">{{ $shipment->sender->name ?? '-' }}</p>
                            </div>
                            <span class="rounded-full bg-brand-gradient-soft px-3 py-1 text-xs font-semibold text-brand-700">
                                {{ $shipment->status->label() }}
                            </span>
                        </a>
                    @else
                        <div class="flex items-center justify-between gap-4 py-3 -mx-2 px-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $shipment->tracking_number }}</p>
                                <p class="text-xs text-slate-500">{{ $shipment->sender->name ?? '-' }}</p>
                            </div>
                            <span class="rounded-full bg-brand-gradient-soft px-3 py-1 text-xs font-semibold text-brand-700">
                                {{ $shipment->status->label() }}
                            </span>
                        </div>
                    @endif
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">Belum ada pengiriman.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-6">
            <h2 class="font-bold text-slate-800">Performa Cabang</h2>
            <div class="mt-4 space-y-3">
                @foreach ($branchPerformance as $branch)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">{{ $branch->name }}</span>
                        <span class="font-semibold text-slate-800">{{ $branch->shipments_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
