<x-admin-layout title="Kendaraan">
    <div x-data="{ modalOpen: false, editing: null }">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-500">Kelola armada kendaraan.</p>
            <button @click="modalOpen = true; editing = null" class="rounded-xl bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                + Tambah Kendaraan
            </button>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($vehicles as $vehicle)
                @php($vehicleEditData = $vehicle->only(['id', 'plate_number', 'type', 'courier_id']))
                <div class="rounded-2xl border border-slate-100 bg-white p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-bold text-slate-800">{{ $vehicle->plate_number }}</p>
                            <p class="text-xs text-slate-500">{{ ucfirst($vehicle->type) }}</p>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-slate-500">Kurir: {{ $vehicle->courier->name ?? '-' }}</p>

                    <div class="mt-4 flex gap-2 border-t border-slate-100 pt-3">
                        <button @click="modalOpen = true; editing = @json($vehicleEditData)"
                                class="text-sm font-semibold text-brand-600">Edit</button>
                        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-rose-500">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="col-span-full py-10 text-center text-slate-400">Belum ada kendaraan.</p>
            @endforelse
        </div>

        <div class="mt-5">{{ $vehicles->links() }}</div>

        <!-- MODAL -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-900/40 p-4">
            <div @click.outside="modalOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6">
                <h3 class="text-lg font-bold text-slate-800" x-text="editing ? 'Edit Kendaraan' : 'Tambah Kendaraan'"></h3>

                <form :action="editing ? `/admin/vehicles/${editing.id}` : '{{ route('admin.vehicles.store') }}'" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>

                    <input type="text" name="plate_number" x-bind:value="editing?.plate_number" placeholder="Plat Nomor" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">

                    <select name="type" x-bind:value="editing?.type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                        <option value="motor">Motor</option>
                        <option value="mobil">Mobil</option>
                        <option value="truck">Truck</option>
                    </select>

                    <select name="courier_id" x-bind:value="editing?.courier_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                        <option value="">Pilih kurir...</option>
                        @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>

                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="modalOpen = false" class="flex-1 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700">Batal</button>
                        <button type="submit" class="flex-1 rounded-xl bg-brand-gradient px-4 py-2.5 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>