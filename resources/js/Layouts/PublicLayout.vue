<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const customer = computed(() => page.props.auth?.customer ?? null);

const menuOpen = ref(false);
const nav = [
    { label: 'Beranda', href: '/', inertia: true },
    { label: 'Kirim Paket', href: '/customer/kirim', inertia: false },
    { label: 'Lacak Paket', href: '/lacak', inertia: true },
    { label: 'Tentang Kami', href: '/tentang', inertia: true },
];
</script>

<template>
    <div class="min-h-screen bg-white text-slate-800 font-sans antialiased">
        <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
                <Link href="/" class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-gradient text-white font-bold">D</span>
                    <span class="text-lg font-extrabold tracking-tight">
                        drg<span class="bg-brand-gradient bg-clip-text text-transparent">Ekspedisi</span>
                    </span>
                </Link>

                <nav class="hidden gap-8 md:flex">
                    <template v-for="item in nav" :key="item.label">
                        <Link v-if="item.inertia" :href="item.href"
                              class="text-sm font-medium text-slate-600 transition hover:text-brand-600">
                            {{ item.label }}
                        </Link>
                        <a v-else :href="item.href"
                           class="text-sm font-medium text-slate-600 transition hover:text-brand-600">
                            {{ item.label }}
                        </a>
                    </template>
                </nav>

                <div class="hidden items-center gap-3 md:flex">
                    <template v-if="customer">
                        <span class="text-sm text-slate-500">Hai, {{ customer.name.split(' ')[0] }}</span>
                        <a href="/customer/dashboard"
                           class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/30 transition hover:opacity-90">
                            Dashboard Saya
                        </a>
                        <Link href="/customer/logout" method="post" as="button"
                           class="rounded-full border border-rose-200 bg-rose-50 px-5 py-2.5 text-sm font-semibold text-rose-600 shadow-sm transition hover:bg-rose-100">
                            Keluar
                        </Link>
                    </template>
                    <template v-else>
                        <a href="/login" class="text-sm font-semibold text-slate-600 hover:text-brand-600">Masuk</a>
                        <a href="/customer/register"
                           class="rounded-full bg-brand-gradient px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/30 transition hover:opacity-90">
                            Daftar
                        </a>
                    </template>
                </div>

                <button class="md:hidden" @click="menuOpen = !menuOpen" aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <div v-if="menuOpen" class="border-t border-slate-100 px-5 py-4 md:hidden">
                <template v-for="item in nav" :key="item.label">
                    <Link v-if="item.inertia" :href="item.href" class="block py-2 text-sm font-medium text-slate-600">{{ item.label }}</Link>
                    <a v-else :href="item.href" class="block py-2 text-sm font-medium text-slate-600">{{ item.label }}</a>
                </template>
                <a v-if="customer" href="/customer/dashboard"
                   class="mt-2 block rounded-full bg-brand-gradient px-5 py-2.5 text-center text-sm font-semibold text-white">
                    Dashboard Saya
                </a>
                <Link v-if="customer" href="/customer/logout" method="post" as="button"
                   class="mt-2 w-full block rounded-full border border-rose-200 bg-rose-50 px-5 py-2.5 text-center text-sm font-semibold text-rose-600 hover:bg-rose-100">
                    Keluar
                </Link>
                <a v-else href="/login" class="mt-2 block rounded-full bg-brand-gradient px-5 py-2.5 text-center text-sm font-semibold text-white">
                    Masuk
                </a>
            </div>
        </header>

        <div v-if="page.props.flash?.success" class="bg-brand-50 px-5 py-3 text-center text-sm font-medium text-brand-700">
            {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash?.error" class="bg-rose-50 px-5 py-3 text-center text-sm font-medium text-rose-700">
            {{ page.props.flash.error }}
        </div>

        <main>
            <slot />
        </main>

        <footer class="mt-24 border-t border-slate-100 bg-slate-50">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 md:grid-cols-4">
                <div>
                    <span class="text-lg font-extrabold">
                        drg<span class="bg-brand-gradient bg-clip-text text-transparent">Ekspedisi</span>
                    </span>
                    <p class="mt-3 text-sm text-slate-500">Platform pengiriman barang terpadu & terintegrasi, dari gerbang cabang hingga pintu penerima.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-700">Layanan</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-500">
                        <li><a href="/customer/kirim" class="hover:text-brand-600">Kirim Paket</a></li>
                        <li><a href="/lacak" class="hover:text-brand-600">Lacak Paket</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-700">Perusahaan</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-500">
                        <li><a href="/tentang" class="hover:text-brand-600">Tentang Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-700">Hubungi Kami</h4>
                    <p class="mt-3 text-sm text-slate-500">cs@drgekspedisi.id</p>
                    <p class="text-sm text-slate-500">0800-1-DRGEKS</p>
                </div>
            </div>
            <div class="border-t border-slate-100 py-5 text-center text-xs text-slate-400">
                © {{ new Date().getFullYear() }} drgEkspedisi. Seluruh hak cipta dilindungi.
            </div>
        </footer>
    </div>
</template>
