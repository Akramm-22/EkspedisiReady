// Loading feedback buat halaman Blade (bukan Inertia) — dua bagian:
// 1. Progress bar tipis di atas, animasi jalan pas pindah halaman/submit form.
// 2. Tombol submit otomatis kasih spinner + teks "Memproses..." dan
//    ke-disable, supaya orang gak double-klik pas nunggu response
//    (termasuk pas nunggu verifikasi reCAPTCHA yang butuh roundtrip ke Google).

function createProgressBar() {
    const bar = document.createElement('div');
    bar.id = 'drg-page-progress';
    Object.assign(bar.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        height: '3px',
        width: '0%',
        background: 'linear-gradient(90deg, #18b378, #3389fd)',
        zIndex: '9999',
        transition: 'width 0.3s ease, opacity 0.3s ease',
    });
    document.body.appendChild(bar);
    return bar;
}

function startProgress() {
    const bar = document.getElementById('drg-page-progress') ?? createProgressBar();
    bar.style.opacity = '1';
    bar.style.width = '20%';
    requestAnimationFrame(() => {
        setTimeout(() => { bar.style.width = '70%'; }, 100);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Progress bar jalan tiap klik link biasa (bukan anchor/target-blank/download).
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link || !link.href) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;
        if (link.href.startsWith('javascript:') || link.href.includes('#')) return;
        if (e.metaKey || e.ctrlKey) return;

        startProgress();
    });

    // Progress bar + disable tombol submit tiap form dikirim.
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        startProgress();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            submitBtn.dataset.originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.innerHTML = `
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memproses...
                </span>`;

            // Safety net: kalau ternyata gak jadi pindah halaman (misalnya
            // validasi client-side gagal / e.preventDefault dipanggil script
            // lain), tombol otomatis balik normal setelah 8 detik biar gak
            // ke-stuck permanen.
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.innerHTML = submitBtn.dataset.originalText;
                }
            }, 8000);
        }
    });
});

// Bar auto-selesai (ilang) begitu halaman baru selesai render.
window.addEventListener('pageshow', () => {
    const bar = document.getElementById('drg-page-progress');
    if (bar) {
        bar.style.width = '100%';
        setTimeout(() => { bar.style.opacity = '0'; bar.style.width = '0%'; }, 300);
    }
});
