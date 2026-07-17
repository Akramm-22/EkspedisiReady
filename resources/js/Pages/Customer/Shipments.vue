<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  shipments: { type: Array, default: () => [] },
});
</script>

<template>
  <Head title="Order History — drgEkspedisi" />
  <PublicLayout>
    <section class="mt-6">
      <h1 class="text-xl font-extrabold text-slate-800">Order History</h1>
      <p class="mt-1 text-sm text-slate-500">Riwayat pengiriman kamu.</p>

      <div class="mt-6 space-y-3">
        <div v-for="s in shipments" :key="s.id" class="rounded-2xl border border-slate-100 bg-white p-5">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <p class="font-bold">{{ s.tracking_number }}</p>
              <p class="text-sm text-slate-500">{{ s.origin_branch?.name ?? '-' }} → {{ s.destination_branch?.name ?? '-' }}</p>
            </div>
            <span class="rounded-full bg-brand-gradient-soft px-3 py-1 text-xs font-semibold text-brand-700">{{ s.status?.label ?? s.status ?? '-' }}</span>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

