<x-courier-layout title="Tugas Saya">
    <!-- SUMMARY CARD -->
    <div class="mb-6 overflow-hidden rounded-2xl bg-brand-600 p-5 text-white shadow-lg shadow-brand-500/30">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-brand-100 uppercase tracking-wider">Tugas Aktif</p>
                <p class="text-2xl font-extrabold">{{ count($shipments) }} <span class="text-sm font-medium text-brand-100 normal-case tracking-normal">paket</span></p>
            </div>
        </div>
    </div>

    <!-- TASK LIST -->
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-extrabold text-slate-800 text-lg tracking-tight">Daftar Pengiriman</h2>
    </div>

    <div class="space-y-4 pb-16">
        @forelse ($shipments as $shipment)
            <a href="{{ route('courier.shipments.show', $shipment) }}"
               class="group block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-brand-300 hover:shadow-md active:scale-[0.98]">
                <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 mb-1">RESI</p>
                        <p class="font-extrabold text-slate-800 text-lg">{{ $shipment->tracking_number }}</p>
                    </div>
                    <span class="rounded-lg bg-brand-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-brand-700 ring-1 ring-inset ring-brand-600/10">
                        {{ $shipment->status->label() }}
                    </span>
                </div>
                <div class="mt-4 flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-50 text-slate-400 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="text-sm">
                        <p class="font-bold text-slate-700">{{ $shipment->receiver->name ?? '-' }}</p>
                        <p class="mt-1 text-slate-500 leading-snug line-clamp-2">{{ $shipment->receiver->address ?? '-' }}</p>
                        <p class="mt-1 font-medium text-slate-600">{{ $shipment->receiver->city ?? '-' }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-10 text-center">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                    <span class="text-2xl">🎉</span>
                </div>
                <h3 class="text-base font-bold text-slate-700">Semua Beres!</h3>
                <p class="mt-1 text-sm text-slate-500">Tidak ada tugas pengiriman saat ini.</p>
            </div>
        @endforelse
    </div>
</x-courier-layout>
