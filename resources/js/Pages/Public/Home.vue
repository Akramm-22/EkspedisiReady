<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    home: { type: Object, required: true }, // { rates, stats }
});

const faqs = [

    { q: 'Bagaimana cara mengirim paket lewat drgEkspedisi?', a: 'Daftar/masuk sebagai customer, buka menu "Kirim Paket", isi cabang asal-tujuan, data penerima, dan barang yang dikirim. Tarif otomatis terhitung sebelum kamu bayar.' },
    { q: 'Metode pembayaran apa saja yang tersedia?', a: 'Cash (dibayar langsung ke kasir cabang), Transfer Bank, dan E-Wallet (GoPay/ShopeePay) lewat Midtrans.' },
    { q: 'Bagaimana cara melacak paket saya?', a: 'Buka menu "Lacak Paket", masukkan nomor resi yang kamu dapat setelah membuat pengiriman. Gak perlu login.' },
    { q: 'Apakah penerima harus punya akun drgEkspedisi?', a: 'Tidak wajib. Kalau penerima belum terdaftar, sistem akan otomatis mendaftarkannya berdasarkan data yang kamu isi.' },
    { q: 'Berapa lama estimasi pengiriman?', a: 'Tergantung rute — bisa dilihat di tabel tarif rute atau langsung muncul otomatis di halaman "Kirim Paket" setelah kamu pilih cabang asal & tujuan.' },
];
const openFaq = ref(null);

const setupReveal = () => {
    const els = Array.from(document.querySelectorAll('[data-reveal]'));
    if (!els.length) return;

    // Munculkan elemen dengan menghapus utility opacity-0/translate-y-2 yang
    // sudah ditulis langsung di template (bukan menambah class 'reveal-in'
    // yang tidak ada definisinya di CSS — itu sebabnya konten sempat "hilang"
    // permanen karena opacity-0 tidak pernah dilepas).
    const reveal = (el) => el.classList.remove('opacity-0', 'translate-y-2');

    const prefersReduced = window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
    if (prefersReduced) {
        els.forEach(reveal);
        return;
    }

    const io = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    reveal(entry.target);
                    io.unobserve(entry.target);
                }
            }
        },
        { threshold: 0.12 }
    );

    els.forEach((el) => io.observe(el));
};

onMounted(() => {
    setupReveal();
});

</script>


<template>
    <Head title="drgEkspedisi — Pengiriman Cepat, Terlacak, Terpercaya" />

    <PublicLayout>
        <!-- HERO -->
        <section class="relative overflow-hidden">
            <div class="inset-0 pointer-events-none absolute opacity-0 transition-opacity duration-300" aria-hidden="true" />

            <div class="absolute inset-0 -z-10 bg-brand-gradient-soft [mask-image:linear-gradient(to_bottom,black,transparent)]"></div>

            <div class="mx-auto grid max-w-6xl items-center gap-10 px-5 pb-16 pt-16 lg:grid-cols-2 lg:pt-24">
                <div data-reveal class="reveal-init opacity-0 translate-y-2 transition-all duration-700">

                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-brand-700 shadow-sm shadow-slate-200">
                        🚚 Jaringan 5 cabang di seluruh Indonesia
                    </span>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight text-slate-800 sm:text-5xl">
                        Pengiriman Cepat,
                        <span class="bg-brand-gradient bg-clip-text text-transparent">Terlacak</span>,
                        Terpercaya
                    </h1>
                    <p class="mt-4 max-w-md text-slate-500">
                        drgEkspedisi mengantarkan paket Anda dengan aman dan tepat waktu — dari gerbang cabang sampai ke depan pintu penerima.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="/customer/kirim"
                           class="inline-block rounded-xl bg-brand-gradient px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-500/20 transition hover:opacity-90">
                            Mulai Kirim Paket →
                        </a>
                    </div>
                </div>

                <!-- ILUSTRASI RUTE PENGIRIMAN -->
                <div data-reveal class="relative mx-auto w-full max-w-md reveal-init opacity-0 translate-y-2 transition-all duration-700">

                    <svg viewBox="0 0 400 320" class="w-full drop-shadow-sm">
                        <defs>
                            <linearGradient id="routeGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#18b378" />
                                <stop offset="100%" stop-color="#3389fd" />
                            </linearGradient>
                        </defs>

                        <!-- jalur putus-putus dari gudang ke rumah -->
                        <path d="M40,240 C120,240 100,80 200,80 S280,240 360,200"
                              fill="none" stroke="url(#routeGrad)" stroke-width="3" stroke-dasharray="8 8" opacity="0.5" />

                        <!-- titik cabang -->
                        <circle cx="40" cy="240" r="7" fill="#18b378" />
                        <circle cx="200" cy="80" r="7" fill="#3389fd" />
                        <circle cx="360" cy="200" r="7" fill="#18b378" />

                        <!-- gudang (asal) -->
                        <g transform="translate(10,255)">
                            <rect x="0" y="10" width="60" height="35" rx="4" fill="#ecfdf5" stroke="#18b378" stroke-width="2"/>
                            <path d="M-4,10 L30,-10 L64,10" fill="none" stroke="#18b378" stroke-width="2" stroke-linejoin="round"/>
                            <rect x="24" y="24" width="12" height="21" fill="#18b378"/>
                        </g>

                        <!-- truk bergerak di tengah jalur -->
                        <g transform="translate(160,55)">
                            <rect x="0" y="10" width="46" height="24" rx="3" fill="#3389fd"/>
                            <rect x="46" y="16" width="18" height="18" rx="2" fill="#1c6cf2"/>
                            <circle cx="14" cy="38" r="6" fill="#1e293b"/>
                            <circle cx="52" cy="38" r="6" fill="#1e293b"/>
                            <rect x="8" y="15" width="14" height="10" rx="1" fill="#eef7ff"/>
                        </g>

                        <!-- rumah (tujuan) -->
                        <g transform="translate(330,170)">
                            <rect x="0" y="15" width="55" height="32" rx="3" fill="#eef7ff" stroke="#3389fd" stroke-width="2"/>
                            <path d="M-5,15 L27,-8 L60,15" fill="none" stroke="#3389fd" stroke-width="2" stroke-linejoin="round"/>
                            <rect x="20" y="28" width="14" height="19" fill="#3389fd"/>
                        </g>

                        <!-- paket melayang -->
                        <g transform="translate(95,150) rotate(-8)">
                            <rect x="0" y="0" width="24" height="24" rx="3" fill="#fff" stroke="#18b378" stroke-width="2"/>
                            <path d="M0,8 L24,8 M12,0 L12,24" stroke="#18b378" stroke-width="1.5"/>
                        </g>
                        <g transform="translate(250,120) rotate(10)">
                            <rect x="0" y="0" width="20" height="20" rx="3" fill="#fff" stroke="#3389fd" stroke-width="2"/>
                            <path d="M0,7 L20,7 M10,0 L10,20" stroke="#3389fd" stroke-width="1.5"/>
                        </g>
                    </svg>
                </div>
            </div>
        </section>



        <!-- KENAPA PILIH KAMI -->
        <section class="mx-auto mt-24 max-w-5xl px-5">
            <h2 data-reveal class="reveal-init opacity-0 translate-y-2 transition-all duration-700 text-center text-2xl font-extrabold text-slate-800">Kenapa drgEkspedisi?</h2>

            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 p-6 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-brand-gradient-soft text-2xl">🚚</div>
                    <h3 class="mt-4 font-bold text-slate-800">Cepat</h3>
                    <p class="mt-2 text-sm text-slate-500">Jaringan 5 cabang di kota-kota besar Indonesia, rute pengiriman efisien.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-6 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-brand-gradient-soft text-2xl">📍</div>
                    <h3 class="mt-4 font-bold text-slate-800">Terlacak</h3>
                    <p class="mt-2 text-sm text-slate-500">Pantau status paket secara realtime dari pickup sampai diterima.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-6 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-brand-gradient-soft text-2xl">🔒</div>
                    <h3 class="mt-4 font-bold text-slate-800">Aman</h3>
                    <p class="mt-2 text-sm text-slate-500">Pembayaran terverifikasi lewat Midtrans, data akun terlindungi.</p>
                </div>
            </div>
        </section>

        <!-- CARA KERJA -->
        <section class="mx-auto mt-24 max-w-5xl px-5">
            <h2 class="text-center text-2xl font-extrabold text-slate-800">Cara Kerja</h2>
            <p class="mx-auto mt-2 max-w-md text-center text-sm text-slate-500">
                Dari klik "Kirim Paket" sampai barang diterima, prosesnya cuma 4 langkah.
            </p>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative rounded-2xl border border-slate-100 p-6">
                    <span class="absolute -top-3 left-6 grid h-7 w-7 place-items-center rounded-full bg-brand-gradient text-xs font-bold text-white">1</span>
                    <div class="mt-3 text-2xl">📝</div>
                    <h3 class="mt-3 font-bold text-slate-800">Isi Detail Kirim</h3>
                    <p class="mt-2 text-sm text-slate-500">Pilih cabang asal-tujuan, data penerima, dan barang yang dikirim.</p>
                </div>
                <div class="relative rounded-2xl border border-slate-100 p-6">
                    <span class="absolute -top-3 left-6 grid h-7 w-7 place-items-center rounded-full bg-brand-gradient text-xs font-bold text-white">2</span>
                    <div class="mt-3 text-2xl">💳</div>
                    <h3 class="mt-3 font-bold text-slate-800">Bayar & Kasir Konfirmasi</h3>
                    <p class="mt-2 text-sm text-slate-500">Tarif otomatis terhitung. Kasir cabang merekap pembayaran kamu.</p>
                </div>
                <div class="relative rounded-2xl border border-slate-100 p-6">
                    <span class="absolute -top-3 left-6 grid h-7 w-7 place-items-center rounded-full bg-brand-gradient text-xs font-bold text-white">3</span>
                    <div class="mt-3 text-2xl">📦</div>
                    <h3 class="mt-3 font-bold text-slate-800">Dikemas & Ditugaskan</h3>
                    <p class="mt-2 text-sm text-slate-500">Paket otomatis ditugaskan ke kurir aktif berikutnya secara bergantian.</p>
                </div>
                <div class="relative rounded-2xl border border-slate-100 p-6">
                    <span class="absolute -top-3 left-6 grid h-7 w-7 place-items-center rounded-full bg-brand-gradient text-xs font-bold text-white">4</span>
                    <div class="mt-3 text-2xl">🏠</div>
                    <h3 class="mt-3 font-bold text-slate-800">Diantar Sampai Tujuan</h3>
                    <p class="mt-2 text-sm text-slate-500">Pantau tiap perpindahan status secara realtime lewat Lacak Paket.</p>
                </div>
            </div>
        </section>



        <!-- TENTANG KAMI SINGKAT -->
        <section class="mx-auto mt-24 max-w-4xl px-5">
            <div class="rounded-2xl bg-brand-gradient-soft p-8 sm:p-10">
                <h2 class="text-xl font-bold text-slate-800">Tentang drgEkspedisi</h2>
                <p class="mt-3 max-w-2xl text-sm text-slate-600">
                    drgEkspedisi adalah platform pengiriman barang terpadu yang menghubungkan
                    5 cabang di kota-kota besar Indonesia. Kami fokus pada kecepatan,
                    transparansi tarif, dan kemudahan melacak paket dari gerbang cabang
                    hingga sampai ke tangan penerima.
                </p>
                <Link href="/tentang" class="mt-4 inline-block text-sm font-semibold text-brand-700">
                    Selengkapnya tentang kami →
                </Link>
            </div>
        </section>



        <!-- FAQ -->
        <section class="mx-auto mb-24 mt-24 max-w-3xl px-5">
            <h2 class="text-xl font-bold text-slate-800">Pertanyaan Umum</h2>
            <div class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-100">
                <div v-for="(faq, index) in faqs" :key="index">
                    <button @click="openFaq = openFaq === index ? null : index"
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-sm font-semibold text-slate-800">
                        {{ faq.q }}
                        <span class="text-slate-400">{{ openFaq === index ? '−' : '+' }}</span>
                    </button>
                    <p v-if="openFaq === index" class="px-5 pb-4 text-sm text-slate-500">{{ faq.a }}</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
