<x-admin-layout :title="$shipment->tracking_number">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <!-- HEADER -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">No. Resi</p>
                        <p class="text-xl font-extrabold text-slate-800">{{ $shipment->tracking_number }}</p>
                    </div>
                    <span class="rounded-full bg-brand-gradient px-4 py-1.5 text-xs font-semibold text-white">
                        {{ $shipment->status->label() }}
                    </span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Pengirim</p>
                        <p class="font-medium text-slate-700">{{ $shipment->sender->name ?? '-' }}</p>
                        <p class="text-slate-500">{{ $shipment->sender->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Penerima</p>
                        <p class="font-medium text-slate-700">{{ $shipment->receiver->name ?? '-' }}</p>
                        <p class="text-slate-500">{{ $shipment->receiver->phone ?? '-' }} · {{ $shipment->receiver->city ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Rute</p>
                        <p class="font-medium text-slate-700">{{ $shipment->originBranch->name ?? '-' }} → {{ $shipment->destinationBranch->name ?? '-' }}</p>
                        <p class="text-slate-500">{{ $shipment->total_weight }} kg</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400">Total Harga</p>
                        <p class="font-medium text-slate-700">Rp{{ number_format($shipment->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if ($shipment->photo)
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <p class="text-xs font-semibold text-slate-400">Foto Paket</p>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($shipment->photo) }}" class="mt-2 h-32 w-32 rounded-xl object-cover">
                    </div>
                @endif
            </div>

            <!-- TIMELINE -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <h2 class="font-bold text-slate-800">Riwayat Tracking</h2>
                <ol class="relative ml-3 mt-5 border-l-2 border-slate-100">
                    @foreach ($shipment->trackings as $tracking)
                        <li class="mb-6 ml-6 last:mb-0">
                            <span class="absolute -left-[9px] grid h-4 w-4 place-items-center rounded-full bg-brand-gradient ring-4 ring-white"></span>
                            <div class="flex items-baseline justify-between gap-3">
                                <h4 class="font-semibold text-slate-800">{{ \App\Enums\ShipmentStatus::from($tracking->status)->label() }}</h4>
                                <time class="text-xs text-slate-400">{{ $tracking->tracked_at->format('d/m/Y H:i') }}</time>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $tracking->description }}</p>
                            @if ($tracking->location)
                                <p class="text-xs font-medium text-transit-600">{{ $tracking->location }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="space-y-6">
            <!-- STATUS (READ-ONLY) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <h2 class="font-bold text-slate-800">Status Pengiriman</h2>
                <p class="mt-1 text-xs text-slate-400">
                    Status diperbarui otomatis mengikuti aksi kurir di lapangan. Admin tidak dapat mengubah status secara manual.
                </p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="rounded-full bg-brand-gradient px-4 py-1.5 text-xs font-semibold text-white">
                        {{ $shipment->status->label() }}
                    </span>
                </div>
                @if ($shipment->courier)
                    <p class="mt-3 text-xs text-slate-500">
                        Ditugaskan ke kurir: <span class="font-semibold text-slate-700">{{ $shipment->courier->name }}</span>
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
