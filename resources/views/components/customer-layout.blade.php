<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard Saya' }} — drgEkspedisi</title>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <!-- HEADER -->
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="/" class="flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-gradient text-white font-bold shadow-sm">D</span>
                <span class="text-lg font-extrabold tracking-tight">
                    drg<span class="bg-brand-gradient bg-clip-text text-transparent">Ekspedisi</span>
                </span>
            </a>

            <nav class="hidden gap-6 md:flex">
                <a href="/" class="text-sm font-semibold text-slate-500 transition hover:text-brand-600">Beranda</a>
                <a href="{{ route('customer.kirim') }}"
                   class="text-sm font-semibold transition {{ request()->routeIs('customer.kirim*') ? 'text-brand-600' : 'text-slate-500 hover:text-brand-600' }}">
                    Kirim Paket
                </a>
                <a href="/lacak" class="text-sm font-semibold text-slate-500 transition hover:text-brand-600">Lacak Paket</a>
                <a href="{{ route('customer.dashboard') }}"
                   class="text-sm font-semibold transition {{ request()->routeIs('customer.dashboard') ? 'text-brand-600' : 'text-slate-500 hover:text-brand-600' }}">
                    Dashboard
                </a>
            </nav>

            <div class="flex items-center gap-3">
                <span class="hidden text-sm font-medium text-slate-600 sm:block">Hai, {{ explode(' ', auth('customer')->user()->name)[0] }}</span>
                <form method="POST" action="{{ route('customer.logout') }}" class="m-0">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-rose-50 hover:text-rose-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Mobile Nav Tabs -->
    <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white md:hidden">
        <div class="flex justify-around px-2 py-2">
            <a href="/" class="flex flex-col items-center gap-1 p-2 text-slate-500 hover:text-brand-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] font-bold">Home</span>
            </a>
            <a href="{{ route('customer.kirim') }}" class="flex flex-col items-center gap-1 p-2 {{ request()->routeIs('customer.kirim*') ? 'text-brand-600' : 'text-slate-500 hover:text-brand-600' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="text-[10px] font-bold">Kirim</span>
            </a>
            <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center gap-1 p-2 {{ request()->routeIs('customer.dashboard') ? 'text-brand-600' : 'text-slate-500 hover:text-brand-600' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-bold">Akun</span>
            </a>
            <form method="POST" action="{{ route('customer.logout') }}" class="m-0 flex">
                @csrf
                <button type="submit" class="flex flex-col items-center justify-center gap-1 p-2 text-slate-500 hover:text-rose-600 w-full">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="text-[10px] font-bold">Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <div class="mx-auto max-w-5xl px-4 py-4 sm:px-6">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-700 shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm">{{ session('error') }}</div>
        @endif

        @unless (auth('customer')->user()->hasVerifiedEmail())
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 shadow-sm">
                Email kamu belum diverifikasi.
                <a href="{{ route('verification.notice') }}" class="font-bold text-brand-600 underline decoration-2 underline-offset-2">Verifikasi sekarang</a>
                supaya bisa melakukan pembayaran.
            </div>
        @endunless
    </div>

    <main class="mx-auto max-w-5xl px-4 pb-24 sm:px-6 md:pb-16">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="mt-auto border-t border-slate-200 bg-white pb-20 md:pb-0">
        <div class="mx-auto grid max-w-5xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-4">
            <div class="md:col-span-2">
                <span class="text-lg font-extrabold tracking-tight">
                    drg<span class="bg-brand-gradient bg-clip-text text-transparent">Ekspedisi</span>
                </span>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-500">Platform pengiriman barang terpadu & terintegrasi, dari gerbang cabang hingga pintu penerima. Cepat, aman, dan terpercaya.</p>
            </div>
            <div>
                <h4 class="text-sm font-bold tracking-wide text-slate-800">Layanan</h4>
                <ul class="mt-4 space-y-3 text-sm font-medium text-slate-500">
                    <li><a href="{{ route('customer.kirim') }}" class="transition hover:text-brand-600">Kirim Paket</a></li>
                    <li><a href="/lacak" class="transition hover:text-brand-600">Lacak Paket</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-bold tracking-wide text-slate-800">Perusahaan</h4>
                <ul class="mt-4 space-y-3 text-sm font-medium text-slate-500">
                    <li><a href="/tentang" class="transition hover:text-brand-600">Tentang Kami</a></li>
                    <li><span class="block">cs@drgekspedisi.id</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-100 py-6 text-center text-xs font-medium text-slate-400">
            © {{ date('Y') }} drgEkspedisi. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
