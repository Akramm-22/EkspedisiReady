<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TrackingTimeline from '@/Components/Public/TrackingTimeline.vue';

const page = usePage();
const initial = page.props.tracking_number ?? '';

const trackingNumber = ref(initial);
const result = ref(null);
const loading = ref(false);
const errorMessage = ref('');

async function search() {
    if (!trackingNumber.value.trim()) return;
    loading.value = true;
    errorMessage.value = '';
    result.value = null;

    try {
        const { data } = await axios.get(`/api/v1/track/${encodeURIComponent(trackingNumber.value.trim().toUpperCase())}`);
        result.value = data.data;
        router.get('/lacak', { tracking_number: trackingNumber.value.trim().toUpperCase() }, { preserveState: true, replace: true });
    } catch (e) {
        errorMessage.value = e.response?.data?.errors?.tracking_number?.[0] ?? 'Nomor resi tidak ditemukan.';
    } finally {
        loading.value = false;
    }
}

if (initial) search();
</script>

<template>
    <Head title="Lacak Paket — drgEkspedisi" />

    <PublicLayout>
        <section class="bg-brand-gradient-soft py-14">
            <div class="mx-auto max-w-2xl px-5 text-center">
                <h1 class="text-2xl font-extrabold text-slate-800 sm:text-3xl">Lacak Paket Kamu</h1>
                <p class="mt-2 text-slate-500">Masukkan nomor resi untuk melihat status pengiriman terkini.</p>

                <form class="mt-6 flex gap-2 rounded-2xl bg-white p-2 shadow-lg" @submit.prevent="search">
                    <input v-model="trackingNumber" placeholder="DRG-20260701-0001"
                           class="flex-1 rounded-xl border-0 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-200" />
                    <button type="submit" :disabled="loading"
                            class="rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white disabled:opacity-60">
                        {{ loading ? 'Mencari...' : 'Lacak' }}
                    </button>
                </form>
            </div>
        </section>

        <section class="mx-auto max-w-2xl px-5 py-14">
            <p v-if="errorMessage" class="text-center text-sm font-medium text-rose-600">{{ errorMessage }}</p>

            <div v-if="result" class="rounded-3xl border border-slate-100 p-6 shadow-xl shadow-slate-200/50 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">No. Resi</p>
                        <p class="text-lg font-bold text-slate-800">{{ result.tracking_number }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ result.origin }} → {{ result.destination }}</p>
                    </div>
                    <span class="rounded-full bg-brand-gradient px-4 py-1.5 text-xs font-semibold text-white">
                        {{ result.status_label }}
                    </span>
                </div>

                <div class="mt-8">
                    <TrackingTimeline :timeline="result.timeline" />
                </div>
            </div>

            <div v-else-if="!errorMessage && !loading" class="rounded-2xl border border-dashed border-slate-200 p-10 text-center">
                <p class="text-sm text-slate-500">Masukkan nomor resi di atas untuk melihat status pengiriman.</p>
            </div>
        </section>
    </PublicLayout>
</template>
