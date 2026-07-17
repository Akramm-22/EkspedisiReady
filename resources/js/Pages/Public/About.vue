<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    branches: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Tentang Kami — drgEkspedisi" />

    <PublicLayout>
        <!-- HERO SECTION -->
        <section class="relative overflow-hidden bg-brand-gradient-soft pt-20 pb-24 sm:pt-28 sm:pb-32">
            <div class="relative z-10 mx-auto max-w-4xl px-5 text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-1.5 text-xs font-bold text-brand-700 shadow-sm backdrop-blur">
                    Tentang Perusahaan
                </span>
                <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-800 sm:text-5xl md:text-6xl">
                    Melangkah Lebih Jauh Bersama <span class="bg-brand-gradient bg-clip-text text-transparent">drgEkspedisi</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-slate-600">
                    Kami hadir untuk memberikan solusi pengiriman barang yang cepat, aman, dan terpercaya. Menghubungkan berbagai kota dari gerbang cabang hingga pintu penerima dengan sepenuh hati.
                </p>
            </div>
            <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 h-[500px] w-[500px] rounded-full bg-brand-300/20 blur-3xl"></div>
        </section>

        <!-- MENGAPA MEMILIH KAMI SECTION -->
        <section class="mx-auto max-w-6xl px-5 py-20">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-slate-800">Mengapa Memilih drgEkspedisi?</h2>
                <p class="mt-4 text-slate-500">Nilai-nilai inti yang membuat kami menjadi pilihan utama.</p>
            </div>

            <div class="mt-12 grid gap-8 sm:grid-cols-3">
                <div class="rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-800">Pengiriman Cepat</h3>
                    <p class="mt-3 leading-relaxed text-slate-500">Prioritas utama kami adalah ketepatan waktu. Paket Anda akan sampai di tujuan dengan estimasi waktu yang jelas.</p>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-800">Keamanan Terjamin</h3>
                    <p class="mt-3 leading-relaxed text-slate-500">Seluruh paket terpantau secara ketat. Dari cabang ke kurir hingga penerima, keamanan selalu terjamin.</p>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-800">Harga Kompetitif</h3>
                    <p class="mt-3 leading-relaxed text-slate-500">Layanan premium dengan tarif yang tetap bersahabat untuk semua kebutuhan pengiriman Anda.</p>
                </div>
            </div>
        </section>

        <!-- JARINGAN CABANG SECTION -->
        <section class="border-t border-slate-100 bg-slate-50 py-20">
            <div class="mx-auto max-w-6xl px-5">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-800">Jaringan Cabang Kami</h2>
                        <p class="mt-3 max-w-2xl text-lg text-slate-500">Dengan <span class="font-bold text-brand-600">{{ branches.length }} cabang</span>, kami siap melayani pengiriman Anda ke berbagai penjuru kota.</p>
                    </div>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div v-for="branch in branches" :key="branch.id"
                         class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-md">
                        <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 line-clamp-1">{{ branch.name }}</h3>
                                <span class="mt-1 inline-flex rounded-md bg-brand-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-brand-600">{{ branch.city }}</span>
                            </div>
                        </div>
                        <div class="pt-4">
                            <div class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <p class="text-sm text-slate-500 line-clamp-2">{{ branch.address }}</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <p class="text-sm font-semibold text-slate-700">{{ branch.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
