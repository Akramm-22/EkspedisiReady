<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  shipments: { type: Array, default: () => [] },
});

const safeStatusLabel = (s) => s?.label ?? s?.status_label ?? s ?? '—';

const safeBranch = (b) => b?.name ?? '—';
const safeReceiver = (r) => r?.name ?? '—';

</script>

<template>
  <Head title="Dashboard Saya — drgEkspedisi" />

  <PublicLayout>
    <section class="mx-auto max-w-6xl px-5 py-8">
      <div class="overflow-hidden rounded-2xl bg-brand-gradient-soft p-6 sm:p-8">
        <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-brand-700 shadow-sm">
          👋 Selamat datang kembali
        </span>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-800 sm:text-3xl">
          Halo, {{ $page.props.auth?.customer?.name ?? '-' }}!
        </h1>
        <p class="mt-2 max-w-lg text-sm text-slate-600">
          Pantau semua pengiriman kamu di sini, atau langsung kirim paket baru.
        </p>
        <a
          href="/customer/kirim"
          class="mt-5 inline-block rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-500/20 transition hover:opacity-90"
        >
          + Kirim Paket Baru
        </a>
      </div>



      <div class="mt-8">
        <h2 class="text-lg font-bold text-slate-800">Pengiriman Saya</h2>

        <div class="mt-4 space-y-3">
          <template v-if="shipments.length">
            <div
              v-for="shipment in shipments"
              :key="shipment.id"
              class="rounded-2xl border border-slate-100 bg-white p-5 transition hover:border-brand-200"
            >
              <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p class="font-bold text-slate-800">{{ shipment.tracking_number }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ safeBranch(shipment.origin_branch) }} → {{ safeBranch(shipment.destination_branch) }}</p>
                  <p class="text-sm text-slate-500">Kepada: {{ safeReceiver(shipment.receiver) }}</p>
                </div>
                <span class="rounded-full bg-brand-gradient-soft px-3 py-1 text-xs font-semibold text-brand-700">
                  {{ safeStatusLabel(shipment.status) }}
                </span>
              </div>

              <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                <p class="text-sm">
                  <span class="text-slate-400">Total:</span>
                  <span class="font-semibold text-slate-800">Rp{{ new Intl.NumberFormat('id-ID').format(shipment.total_price ?? 0) }}</span>
                </p>

                <div class="flex gap-2">
                  <a
                    :href="`/lacak?tracking_number=${shipment.tracking_number}`"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:border-brand-300"
                  >
                    Lacak
                  </a>

                  <template v-if="shipment.is_paid">
                    <span class="rounded-xl bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700">✅ Lunas</span>
                  </template>
                  <template v-else>
                    <a
                      :href="`/shipments/${shipment.id}/payment`"
                      class="rounded-xl bg-brand-gradient px-4 py-2 text-sm font-semibold text-white hover:opacity-90"
                    >
                      Bayar Sekarang
                    </a>
                  </template>
                </div>
              </div>
            </div>
          </template>

          <template v-else>
            <div class="rounded-2xl border border-dashed border-slate-200 p-10 text-center">
              <p class="text-sm text-slate-500">Belum ada pengiriman.</p>
              <a href="/customer/kirim" class="mt-3 inline-block text-sm font-semibold text-brand-600">
                Mulai kirim paket pertamamu →
              </a>
            </div>
          </template>
        </div>

        <!-- pagination intentionally omitted to avoid layout breaks; keep server-side later if needed -->
      </div>
    </section>
  </PublicLayout>
</template>

