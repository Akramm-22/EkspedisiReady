<x-auth-layout title="Verifikasi Email">
    <div class="text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-brand-gradient-soft text-3xl">📧</div>
        <h1 class="mt-4 text-xl font-extrabold text-slate-800">Cek Email Kamu</h1>
        <p class="mt-2 text-sm text-slate-500">
            Kami sudah mengirim link verifikasi ke email kamu. Klik link itu
            untuk mengaktifkan akun sepenuhnya.
        </p>

        @if (session('warning'))
            <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">{{ session('warning') }}</div>
        @endif

        @if (session('success'))
            <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button type="submit" class="rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white hover:opacity-90">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('customer.logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-500 underline">Keluar</button>
        </form>
    </div>
</x-auth-layout>
