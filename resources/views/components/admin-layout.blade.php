<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — drgEkspedisi</title>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform bg-white shadow-xl transition-transform duration-200 lg:static lg:translate-x-0"
               :class="sidebarOpen && '!translate-x-0'">
            <div class="flex h-16 items-center gap-2 border-b border-slate-100 px-6">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-gradient text-white font-bold">D</span>
                <span class="text-lg font-extrabold">drg<span class="bg-brand-gradient bg-clip-text text-transparent">Ekspedisi</span></span>
            </div>

            <nav class="space-y-1 px-3 py-4">
                @php
                    $user = auth()->user();
                    $navItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ];

                    // Pengiriman: tugas operasional Admin & Kasir. Manager
                    // sengaja tidak dapat menu ini (cuma pemantau lewat Laporan).
                    if ($user->hasRole('admin|cashier')) {
                        $navItems[] = ['route' => 'admin.shipments.index', 'label' => 'Pengiriman', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'];
                        $navItems[] = ['route' => 'admin.payments.index', 'label' => 'Pembayaran', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'];
                    }

                    // Tarif & Kendaraan: master data, cuma Admin yang boleh ubah.
                    if ($user->hasRole('admin')) {
                        $navItems[] = ['route' => 'admin.rates.index', 'label' => 'Tarif', 'icon' => 'M9 7h6m0 10v-3m-3 3v-6m-3 6v-9m12 4a9 9 0 11-18 0 9 9 0 0118 0z'];
                        $navItems[] = ['route' => 'admin.vehicles.index', 'label' => 'Kendaraan', 'icon' => 'M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM5 17H3v-4l2-5h9l4 5v4h-2M5 17h8m4 0h2'];
                    }

                    // Laporan: tugas UTAMA Manager (view + export Excel/PDF),
                    // Admin juga bisa akses untuk oversight.
                    if ($user->hasRole('admin|manager')) {
                        $navItems[] = ['route' => 'admin.reports.index', 'label' => 'Laporan', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'];
                    }
                @endphp

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs($item['route'].'*') ? 'bg-brand-gradient-soft text-brand-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="absolute bottom-0 w-full border-t border-slate-100 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
             class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>

        <!-- MAIN -->
        <div class="flex-1">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-100 bg-white px-6">
                <div class="flex items-center gap-4">
                    <button class="lg:hidden" @click="sidebarOpen = true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-bold text-slate-800">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="flex items-center gap-3">
                    <span class="rounded-full bg-brand-gradient-soft px-3 py-1 text-xs font-semibold text-brand-700">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                    <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-200 text-sm font-bold text-slate-600">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden text-sm font-medium text-slate-700 sm:block">{{ auth()->user()->name }}</span>
                </div>
            </header>

            <main class="p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-xl bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
