<x-admin-layout title="Tarif">
    <div x-data="{ modalOpen: false, editing: null }">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-500">Kelola tarif per rute pengiriman.</p>
            <button @click="modalOpen = true; editing = null" class="rounded-xl bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                + Tambah Tarif
            </button>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Rute</th>
                        <th class="px-5 py-3">Harga/kg</th>
                        <th class="px-5 py-3">Estimasi</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rates as $rate)
                        @php($rateEditData = $rate->only(['id', 'origin_city', 'destination_city', 'price_per_kg', 'estimated_days']))
                        <tr>
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $rate->origin_city }} → {{ $rate->destination_city }}</td>
                            <td class="px-5 py-3 text-slate-600">Rp{{ number_format($rate->price_per_kg, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $rate->estimated_days }} hari</td>
                            <td class="px-5 py-3 text-right">
                                <button @click="modalOpen = true; editing = @json($rateEditData)"
                                        class="text-sm font-semibold text-brand-600">Edit</button>
                                <form method="POST" action="{{ route('admin.rates.destroy', $rate) }}" class="inline" onsubmit="return confirm('Hapus tarif ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-3 text-sm font-semibold text-rose-500">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Belum ada tarif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $rates->links() }}</div>

        <!-- MODAL -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-900/40 p-4">
            <div @click.outside="modalOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6">
                <h3 class="text-lg font-bold text-slate-800" x-text="editing ? 'Edit Tarif' : 'Tambah Tarif'"></h3>

                <form :action="editing ? `/admin/rates/${editing.id}` : '{{ route('admin.rates.store') }}'" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="origin_city" x-bind:value="editing?.origin_city" placeholder="Kota Asal" required class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                        <input type="text" name="destination_city" x-bind:value="editing?.destination_city" placeholder="Kota Tujuan" required class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                    </div>
                    <input type="number" step="0.01" name="price_per_kg" x-bind:value="editing?.price_per_kg" placeholder="Harga per kg" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                    <input type="number" name="estimated_days" x-bind:value="editing?.estimated_days" placeholder="Estimasi (hari)" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">

                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="modalOpen = false" class="flex-1 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700">Batal</button>
                        <button type="submit" class="flex-1 rounded-xl bg-brand-gradient px-4 py-2.5 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>