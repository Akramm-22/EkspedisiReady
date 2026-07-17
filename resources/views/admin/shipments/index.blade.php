<x-admin-layout title="Pengiriman">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="flex flex-1 gap-2">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari no. resi..."
                   class="w-full max-w-xs rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                <option value="">Semua Status</option>
                @foreach (\App\Enums\ShipmentStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Filter</button>
        </form>

        @can('create', \App\Models\Shipment::class)
            <a href="{{ route('admin.shipments.create') }}"
               class="rounded-xl bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                + Shipment Baru
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-3">No. Resi</th>
                    <th class="px-5 py-3">Pengirim</th>
                    <th class="px-5 py-3">Tujuan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($shipments as $shipment)
                    <tr onclick="window.location='{{ route('admin.shipments.show', $shipment) }}'" class="cursor-pointer hover:bg-slate-50">
                        <td class="px-5 py-3 font-semibold text-slate-800">{{ $shipment->tracking_number }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $shipment->sender->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $shipment->receiver->city ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-brand-gradient-soft px-3 py-1 text-xs font-semibold text-brand-700">
                                {{ $shipment->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $shipment->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada data pengiriman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $shipments->links() }}</div>
</x-admin-layout>
