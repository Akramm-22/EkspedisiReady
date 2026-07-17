<x-courier-layout :title="$shipment->tracking_number">
    <div class="rounded-2xl border border-slate-100 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-slate-400">Penerima</p>
        <p class="mt-1 font-bold text-slate-800">{{ $shipment->receiver->name ?? '-' }}</p>
        <p class="text-sm text-slate-500">{{ $shipment->receiver->phone ?? '-' }}</p>
        <p class="mt-2 text-sm text-slate-600">{{ $shipment->receiver->address ?? '-' }}, {{ $shipment->receiver->city ?? '-' }}</p>

        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode(($shipment->receiver->address ?? '').', '.($shipment->receiver->city ?? '')) }}"
           target="_blank" class="mt-3 inline-block text-sm font-semibold text-transit-600">
            📍 Buka Navigasi Google Maps
        </a>
    </div>

    <form method="POST" action="{{ route('courier.shipments.update-status', $shipment) }}" enctype="multipart/form-data"
          class="mt-5 space-y-4 rounded-2xl border border-slate-100 bg-white p-4">
        @csrf
        @method('PATCH')

        @if ($nextStatus)
            <div>
                <label class="text-xs font-semibold text-slate-500">Update Status</label>
                <select name="status" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                    @foreach (\App\Enums\ShipmentStatus::timelineOrder() as $step)
                        @continue($step->value === 'pending')
                        <option value="{{ $step->value }}"
                                @selected($step === $nextStatus)
                                @disabled($step !== $nextStatus)>
                            {{ $step->label() }}
                            @unless ($step === $nextStatus)
                                — belum bisa dipilih
                            @endunless
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">
                    Status harus diperbarui berurutan. Langkah berikutnya: <span class="font-semibold text-slate-600">{{ $nextStatus->label() }}</span>.
                </p>
            </div>
        @else
            <div class="rounded-xl bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">
                ✅ Shipment ini sudah pada status akhir ({{ $shipment->status->label() }}).
            </div>
        @endif

        <div>
            <label class="text-xs font-semibold text-slate-500">Catatan</label>
            <textarea name="description" required rows="2" placeholder="Contoh: Diterima oleh penerima langsung"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"></textarea>
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-500">Foto Bukti Serah Terima (opsional)</label>
            <input type="file" name="photo" accept="image/*" capture="environment" class="mt-1 w-full text-sm">
        </div>

        <button type="submit" @disabled(! $nextStatus) class="w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white disabled:opacity-50">
            Simpan Update
        </button>
    </form>

    <div class="mt-5 rounded-2xl border border-slate-100 bg-white p-4">
        <h3 class="text-sm font-bold text-slate-800">Riwayat</h3>
        <ol class="mt-3 space-y-3">
            @foreach ($shipment->trackings->reverse() as $tracking)
                <li class="text-sm">
                    <p class="font-medium text-slate-700">{{ \App\Enums\ShipmentStatus::from($tracking->status)->label() }}</p>
                    <p class="text-xs text-slate-400">{{ $tracking->tracked_at->format('d/m/Y H:i') }} — {{ $tracking->description }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</x-courier-layout>
