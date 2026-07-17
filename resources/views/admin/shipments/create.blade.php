<x-admin-layout title="Shipment Baru">
    <form method="POST" action="{{ route('admin.shipments.store') }}" enctype="multipart/form-data"
          x-data="shipmentForm()" class="grid gap-6 lg:grid-cols-3">
        @csrf

        <div class="space-y-6 lg:col-span-2">
            <!-- PENGIRIM -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <h2 class="font-bold text-slate-800">Pengirim</h2>
                <div class="relative mt-4">
                    <input type="text" x-model="senderQuery" @input.debounce.400ms="search('sender')"
                           placeholder="Cari nama/HP customer pengirim..."
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
                    <div x-show="senderResults.length" x-cloak class="absolute z-10 mt-1 w-full rounded-xl border border-slate-100 bg-white shadow-lg">
                        <template x-for="c in senderResults" :key="c.id">
                            <button type="button" @click="select('sender', c)" class="block w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50">
                                <span x-text="c.name"></span> — <span class="text-slate-400" x-text="c.phone"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <input type="hidden" name="sender_id" x-model="senderId" required>
                <p class="mt-2 text-xs text-slate-500" x-show="senderId">Terpilih: <span class="font-semibold" x-text="senderName"></span></p>
            </div>

            <!-- PENERIMA -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <h2 class="font-bold text-slate-800">Penerima</h2>
                <p class="mt-1 text-xs text-slate-400">Penerima juga harus terdaftar sebagai customer.</p>
                <div class="relative mt-4">
                    <input type="text" x-model="receiverQuery" @input.debounce.400ms="search('receiver')"
                           placeholder="Cari nama/HP customer penerima..."
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
                    <div x-show="receiverResults.length" x-cloak class="absolute z-10 mt-1 w-full rounded-xl border border-slate-100 bg-white shadow-lg">
                        <template x-for="c in receiverResults" :key="c.id">
                            <button type="button" @click="select('receiver', c)" class="block w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50">
                                <span x-text="c.name"></span> — <span class="text-slate-400" x-text="c.phone"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <input type="hidden" name="receiver_id" x-model="receiverId" required>
                <p class="mt-2 text-xs text-slate-500" x-show="receiverId">Terpilih: <span class="font-semibold" x-text="receiverName"></span></p>
            </div>

            <!-- ITEM BARANG -->
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-800">Barang</h2>
                    <button type="button" @click="addItem()" class="text-sm font-semibold text-brand-600">+ Tambah Barang</button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="mt-4 grid grid-cols-12 gap-3 border-t border-slate-100 pt-4 first:border-0 first:pt-0">
                        <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" placeholder="Nama barang" required
                               class="col-span-6 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" placeholder="Qty" min="1" required
                               class="col-span-2 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
                        <input type="number" step="0.01" :name="`items[${index}][weight]`" x-model="item.weight" placeholder="Berat (kg)" required
                               class="col-span-3 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
                        <button type="button" @click="removeItem(index)" class="col-span-1 text-rose-500">✕</button>
                    </div>
                </template>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <label class="text-xs font-semibold text-slate-500">Foto Paket (opsional)</label>
                <input type="file" name="photo" accept="image/*" class="mt-1 w-full text-sm">
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6">
                <h2 class="font-bold text-slate-800">Rute & Tarif</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Cabang Asal</label>
                        <select name="origin_branch_id" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Cabang Tujuan</label>
                        <select name="destination_branch_id" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500">Tarif (Rute)</label>
                        <select name="rate_id" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
                            @foreach ($rates as $rate)
                                <option value="{{ $rate->id }}">
                                    {{ $rate->origin_city }} → {{ $rate->destination_city }} · Rp{{ number_format($rate->price_per_kg, 0, ',', '.') }}/kg
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white hover:opacity-90">
                Buat Shipment
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        function shipmentForm() {
            return {
                items: [{ item_name: '', quantity: 1, weight: '' }],
                senderQuery: '', senderResults: [], senderId: '', senderName: '',
                receiverQuery: '', receiverResults: [], receiverId: '', receiverName: '',
                addItem() { this.items.push({ item_name: '', quantity: 1, weight: '' }); },
                removeItem(i) { this.items.splice(i, 1); },
                async search(target) {
                    const query = target === 'sender' ? this.senderQuery : this.receiverQuery;
                    if (query.length < 2) return;
                    const res = await fetch(`{{ route('admin.shipments.search-customers') }}?q=${encodeURIComponent(query)}`);
                    const data = await res.json();
                    if (target === 'sender') this.senderResults = data; else this.receiverResults = data;
                },
                select(target, c) {
                    if (target === 'sender') {
                        this.senderId = c.id; this.senderName = c.name; this.senderQuery = c.name; this.senderResults = [];
                    } else {
                        this.receiverId = c.id; this.receiverName = c.name; this.receiverQuery = c.name; this.receiverResults = [];
                    }
                },
            };
        }
    </script>
    @endpush
</x-admin-layout>
