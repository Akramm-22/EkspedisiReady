<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#18b378">
    <link rel="manifest" href="/manifest.json">
    <title>{{ $title ?? 'Kurir' }} — drgEkspedisi</title>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased pb-20 sm:pb-0">
    <header class="sticky top-0 z-20 border-b border-brand-600 bg-brand-gradient px-4 py-4 text-white shadow-md">
        <div class="mx-auto flex max-w-md items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-white/20 font-extrabold backdrop-blur-sm shadow-inner">
                    D
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-none tracking-tight">{{ $title ?? 'Kurir' }}</h1>
                    <p class="mt-1 text-xs font-medium text-white/80">{{ explode(' ', auth()->user()->name)[0] ?? 'Kurir' }}</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="flex h-10 px-3 sm:w-auto w-10 items-center justify-center gap-2 rounded-xl bg-white/10 text-white transition hover:bg-white/20 active:bg-white/30" title="Keluar">
                    <span class="hidden sm:inline text-sm font-bold">Keluar</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-md px-4 pt-6 pb-6">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-bold text-brand-700 shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 shadow-sm">{{ session('error') }}</div>
        @endif

        {{ $slot }}
    </main>

    <!-- Bottom Navigation for Mobile -->
    <nav class="fixed bottom-0 left-0 right-0 z-30 border-t border-slate-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] sm:hidden pb-safe">
        <div class="mx-auto flex max-w-md justify-around px-2 py-2">
            <a href="{{ route('courier.index') }}" class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl p-2 transition {{ request()->routeIs('courier.index') ? 'text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                <svg class="h-6 w-6" fill="{{ request()->routeIs('courier.index') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('courier.index') ? '0' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-[10px] font-bold tracking-wide">Tugas</span>
            </a>
            <div class="flex flex-1 flex-col items-center justify-center gap-1 p-2 text-slate-400">
                <svg class="h-6 w-6 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-bold tracking-wide opacity-50">Profil</span>
            </div>
        </div>
    </nav>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js').catch(console.error);
        }
    </script>

    @stack('scripts')
</body>
</html>
