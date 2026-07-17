<x-auth-layout title="Masuk">
    <h1 class="text-2xl font-extrabold text-slate-800">Masuk</h1>
    <p class="mt-1 text-sm text-slate-500">Satu halaman login untuk semua: Customer, Admin, Manajer, Kasir, dan Kurir.</p>

    @if ($errors->any())
        <div class="mt-5 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="text-xs font-semibold text-slate-500">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Kata Sandi</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-200">
            Ingat saya
        </label>

        <button type="submit"
                class="w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun customer? <a href="{{ route('customer.register') }}" class="font-semibold text-brand-600">Daftar di sini</a>
    </p>

    @if (app()->environment('local'))
        <div class="mt-6 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
            <p class="font-semibold text-slate-600">Akun demo (dari seeder):</p>
            <p>admin@drgekspedisi.id · manager@drgekspedisi.id</p>
            <p>cashier@drgekspedisi.id · courier@drgekspedisi.id</p>
            <p>Password semua: <span class="font-mono">Password123</span></p>
        </div>
    @endif
</x-auth-layout>
