<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
  branches: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) },
});

const originBranchId = ref('');
const destinationBranchId = ref('');
const weight = ref('');
const calculatedPrice = ref(null);
const estimatedDays = ref(null);
const isCalculating = ref(false);
const calcError = ref(null);

let debounceTimeout = null;

const calculateRate = async () => {
  calcError.value = null;
  calculatedPrice.value = null;
  estimatedDays.value = null;

  if (!originBranchId.value || !destinationBranchId.value || !weight.value) {
    return;
  }

  const originCity = props.branches.find(b => String(b.id) === String(originBranchId.value))?.city;
  const destinationCity = props.branches.find(b => String(b.id) === String(destinationBranchId.value))?.city;

  if (!originCity || !destinationCity || weight.value <= 0) return;

  isCalculating.value = true;
  try {
    const response = await fetch('/api/v1/rates/calculate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        origin_city: originCity,
        destination_city: destinationCity,
        weight_kg: parseFloat(weight.value)
      })
    });
    
    const data = await response.json();
    if (response.ok) {
      calculatedPrice.value = data.data.price;
      estimatedDays.value = data.data.estimated_days;
    } else {
      calcError.value = data.message || 'Gagal menghitung tarif';
    }
  } catch (err) {
    calcError.value = 'Terjadi kesalahan jaringan.';
  } finally {
    isCalculating.value = false;
  }
};

watch([originBranchId, destinationBranchId, weight], () => {
  if (debounceTimeout) clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(calculateRate, 500);
});

const formatRupiah = (number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(number);
};
</script>

<template>
  <Head title="Kirim Paket — drgEkspedisi" />

  <PublicLayout>
    <section class="mx-auto max-w-6xl px-5 py-8">
      <h1 class="text-xl font-extrabold text-slate-800">Kirim Paket</h1>
      <p class="mt-1 text-sm text-slate-500">Isi tujuan & data penerima, tarif otomatis terhitung di samping.</p>

      <div v-if="Object.keys(errors).length" class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <ul class="list-disc pl-4">
          <li v-for="(msg, k) in errors" :key="k">{{ msg }}</li>
        </ul>
      </div>
      
      <div v-if="calcError" class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
        {{ calcError }}
      </div>

  <form method="POST" action="/customer/kirim" class="mt-6 grid gap-6 lg:grid-cols-3">
        <input type="hidden" name="_token" :value="$page.props.csrf_token" />

        <div class="space-y-6 lg:col-span-2">
          <div class="rounded-2xl border border-slate-100 bg-white p-6">
            <h2 class="font-bold text-slate-800">Rute Pengiriman</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-xs font-semibold text-slate-500">Cabang Asal</label>
                <select
                  name="origin_branch_id"
                  v-model="originBranchId"
                  required
                  class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none"
                >
                  <option value="">Pilih cabang asal...</option>
                  <option v-for="b in branches" :key="b.id" :value="b.id" :data-city="b.city">
                    {{ b.name }} ({{ b.city }})
                  </option>
                </select>
              </div>

              <div>
                <label class="text-xs font-semibold text-slate-500">Cabang Tujuan</label>
                <select
                  name="destination_branch_id"
                  v-model="destinationBranchId"
                  required
                  class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none"
                >
                  <option value="">Pilih cabang tujuan...</option>
                  <option v-for="b in branches" :key="b.id" :value="b.id" :data-city="b.city">
                    {{ b.name }} ({{ b.city }})
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-100 bg-white p-6">
            <h2 class="font-bold text-slate-800">Data Penerima</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-xs font-semibold text-slate-500">Nama Penerima</label>
                <input type="text" name="receiver_name" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none" />
              </div>
              <div>
                <label class="text-xs font-semibold text-slate-500">Email Penerima</label>
                <input type="email" name="receiver_email" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none" />
                <p class="mt-1 text-xs text-slate-400">Kalau belum pernah kirim/terima paket di sini, akan otomatis terdaftar.</p>
              </div>
              <div>
                <label class="text-xs font-semibold text-slate-500">No. HP Penerima</label>
                <input type="text" name="receiver_phone" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none" />
              </div>
              <div>
                <label class="text-xs font-semibold text-slate-500">Kota Penerima</label>
                <input type="text" name="receiver_city" required class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none" />
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-slate-500">Alamat Lengkap Penerima</label>
                <textarea name="receiver_address" required rows="2" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none"></textarea>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-100 bg-white p-6">
            <div class="flex items-center justify-between">
              <h2 class="font-bold text-slate-800">Barang yang Dikirim</h2>
              <button type="button" class="text-sm font-semibold text-brand-600" disabled>+ Tambah Barang</button>
            </div>

            <!-- Keep minimal (server validation remains). Full Alpine item add/remove can be re-enabled later. -->
            <div class="mt-4 grid grid-cols-12 gap-3 border-t border-slate-100 pt-4">
              <input type="text" name="items[0][item_name]" placeholder="Nama barang" required
                class="col-span-6 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" />
              <input type="number" name="items[0][quantity]" placeholder="Qty" min="1" required
                class="col-span-2 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" />
              <input type="number" step="0.1" name="items[0][weight]" v-model="weight" placeholder="Berat (kg)" required
                class="col-span-3 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none" />
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="sticky top-6 rounded-2xl border border-slate-100 bg-white p-6">
            <h2 class="font-bold text-slate-800">Ringkasan & Pembayaran</h2>

            <div class="mt-4 space-y-2 border-b border-slate-100 pb-4 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-500">Total Berat</span>
                <span class="font-medium text-slate-700">{{ weight ? weight + ' kg' : '—' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Estimasi Tiba</span>
                <span class="font-medium text-slate-700">
                  <span v-if="isCalculating">Menghitung...</span>
                  <span v-else>{{ estimatedDays ? estimatedDays + ' Hari' : '—' }}</span>
                </span>
              </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <span class="font-bold text-slate-800">Total Bayar</span>
              <span class="text-xl font-extrabold text-transit-600">
                <span v-if="isCalculating">...</span>
                <span v-else>{{ calculatedPrice ? formatRupiah(calculatedPrice) : '—' }}</span>
              </span>
            </div>

            <div class="mt-6">
              <input type="hidden" name="payment_method" value="transfer" />
            </div>

            <button type="submit" :disabled="isCalculating" class="mt-6 w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-50">
              Kirim & Bayar Sekarang
            </button>
            <p class="mt-2 text-center text-xs text-slate-400">Popup pembayaran Midtrans langsung terbuka setelah ini.</p>
          </div>
        </div>
      </form>
    </section>
  </PublicLayout>
</template>

