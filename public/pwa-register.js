(() => {
    if (window.__drgPwaInitialized) return;
    window.__drgPwaInitialized = true;

    const state = {
        deferredPrompt: null,
        registration: null,
        installed:
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true,
    };

    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);

    const styles = `
        #drg-pwa-tools{position:fixed;right:16px;bottom:16px;z-index:9998;display:flex;align-items:center;gap:8px;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
        .drg-pwa-button,.drg-pwa-status{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;border:1px solid rgba(148,163,184,.28);border-radius:999px;padding:9px 14px;background:rgba(255,255,255,.96);box-shadow:0 10px 30px rgba(15,23,42,.15);color:#334155;font-size:12px;font-weight:800;line-height:1;backdrop-filter:blur(12px)}
        .drg-pwa-button{cursor:pointer;transition:transform .18s ease,box-shadow .18s ease,opacity .18s ease}.drg-pwa-button:hover{transform:translateY(-1px);box-shadow:0 14px 36px rgba(15,23,42,.2)}
        .drg-pwa-button[data-kind="install"]{border-color:transparent;background:linear-gradient(135deg,#18b378,#0786c7);color:#fff}.drg-pwa-button[data-kind="update"]{border-color:#fde68a;background:#fffbeb;color:#92400e}
        .drg-pwa-status-dot{width:9px;height:9px;border-radius:999px;background:#22c55e;box-shadow:0 0 0 4px rgba(34,197,94,.14)}
        .drg-pwa-status[data-online="false"]{color:#9a3412;background:#fff7ed;border-color:#fed7aa}.drg-pwa-status[data-online="false"] .drg-pwa-status-dot{background:#f97316;box-shadow:0 0 0 4px rgba(249,115,22,.14)}
        #drg-pwa-toast{position:fixed;left:50%;bottom:76px;z-index:10000;max-width:min(92vw,420px);transform:translate(-50%,16px);padding:12px 16px;border-radius:14px;background:#0f172a;color:#fff;font-family:Inter,ui-sans-serif,system-ui,sans-serif;font-size:13px;font-weight:700;line-height:1.45;box-shadow:0 18px 50px rgba(15,23,42,.3);opacity:0;pointer-events:none;transition:opacity .2s ease,transform .2s ease}#drg-pwa-toast[data-show="true"]{opacity:1;transform:translate(-50%,0)}
        #drg-pwa-dialog{position:fixed;inset:0;z-index:10001;display:none;place-items:center;padding:20px;background:rgba(15,23,42,.56);font-family:Inter,ui-sans-serif,system-ui,sans-serif}#drg-pwa-dialog[data-open="true"]{display:grid}
        .drg-pwa-dialog-card{width:min(100%,430px);border-radius:24px;background:#fff;padding:24px;box-shadow:0 24px 80px rgba(15,23,42,.3)}.drg-pwa-dialog-card h2{margin:0;color:#0f172a;font-size:21px}.drg-pwa-dialog-card p,.drg-pwa-dialog-card li{color:#64748b;font-size:14px;line-height:1.65}.drg-pwa-dialog-card ol{padding-left:20px}.drg-pwa-dialog-actions{display:flex;justify-content:flex-end;margin-top:20px}.drg-pwa-dialog-actions button{border:0;border-radius:12px;padding:10px 16px;background:#0f172a;color:#fff;font:inherit;font-size:13px;font-weight:800;cursor:pointer}
        @media(max-width:640px){#drg-pwa-tools{right:10px;bottom:82px;max-width:calc(100vw - 20px)}.drg-pwa-button,.drg-pwa-status{min-height:38px;padding:8px 11px}.drg-pwa-status-label{display:none}#drg-pwa-toast{bottom:136px}}
        @media(prefers-reduced-motion:reduce){.drg-pwa-button,#drg-pwa-toast{transition:none}}
    `;

    const style = document.createElement('style');
    style.textContent = styles;
    document.head.appendChild(style);

    const tools = document.createElement('div');
    tools.id = 'drg-pwa-tools';
    tools.setAttribute('aria-label', 'Status aplikasi');
    tools.innerHTML = `
        <div class="drg-pwa-status" role="status" aria-live="polite">
            <span class="drg-pwa-status-dot" aria-hidden="true"></span>
            <span class="drg-pwa-status-label">Online</span>
        </div>
        <button class="drg-pwa-button" data-kind="update" type="button" hidden>Perbarui</button>
        <button class="drg-pwa-button" data-kind="install" type="button" hidden>Install Aplikasi</button>
    `;

    const toast = document.createElement('div');
    toast.id = 'drg-pwa-toast';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');

    const dialog = document.createElement('div');
    dialog.id = 'drg-pwa-dialog';
    dialog.setAttribute('role', 'dialog');
    dialog.setAttribute('aria-modal', 'true');
    dialog.setAttribute('aria-labelledby', 'drg-pwa-dialog-title');
    dialog.innerHTML = `
        <div class="drg-pwa-dialog-card">
            <h2 id="drg-pwa-dialog-title">Install drgEkspedisi</h2>
            <div data-dialog-content></div>
            <div class="drg-pwa-dialog-actions"><button type="button" data-dialog-close>Tutup</button></div>
        </div>
    `;

    document.body.append(tools, toast, dialog);

    const statusElement = tools.querySelector('.drg-pwa-status');
    const statusLabel = tools.querySelector('.drg-pwa-status-label');
    const installButton = tools.querySelector('[data-kind="install"]');
    const updateButton = tools.querySelector('[data-kind="update"]');
    const dialogContent = dialog.querySelector('[data-dialog-content]');

    let toastTimer;
    const showToast = (message, duration = 3200) => {
        window.clearTimeout(toastTimer);
        toast.textContent = message;
        toast.dataset.show = 'true';
        toastTimer = window.setTimeout(() => {
            toast.dataset.show = 'false';
        }, duration);
    };

    const openInstallHelp = () => {
        dialogContent.innerHTML = isIos
            ? `<p>Di iPhone atau iPad, instalasi dilakukan dari menu Safari:</p><ol><li>Tekan tombol <strong>Bagikan</strong>.</li><li>Pilih <strong>Tambahkan ke Layar Utama</strong>.</li><li>Tekan <strong>Tambah</strong>.</li></ol>`
            : `<p>Browser belum memberikan prompt instalasi otomatis. Buka menu browser, lalu pilih <strong>Install app</strong>, <strong>Install drgEkspedisi</strong>, atau <strong>Add to Home screen</strong>.</p>`;
        dialog.dataset.open = 'true';
    };

    const closeDialog = () => {
        dialog.dataset.open = 'false';
    };

    dialog.querySelector('[data-dialog-close]').addEventListener('click', closeDialog);
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) closeDialog();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeDialog();
    });

    const updateInstallButton = () => {
        if (state.installed) {
            installButton.hidden = true;
            return;
        }

        installButton.hidden = false;
        installButton.textContent = state.deferredPrompt ? 'Install Aplikasi' : isIos ? 'Install di iPhone' : 'Cara Install';
    };

    const setConnectionState = ({ announce = false } = {}) => {
        const online = window.navigator.onLine;
        document.documentElement.dataset.connection = online ? 'online' : 'offline';
        statusElement.dataset.online = String(online);
        statusLabel.textContent = online ? 'Online' : 'Offline';
        statusElement.title = online ? 'Aplikasi terhubung ke internet' : 'Aplikasi menggunakan data yang tersedia di cache';

        if (announce) {
            showToast(online ? 'Koneksi kembali online. Data terbaru dapat dimuat.' : 'Kamu sedang offline. Halaman publik tersimpan tetap bisa dibuka.');
        }

        window.dispatchEvent(new CustomEvent('connectionchange', { detail: { online } }));
    };

    installButton.addEventListener('click', async () => {
        if (!state.deferredPrompt) {
            openInstallHelp();
            return;
        }

        const promptEvent = state.deferredPrompt;
        state.deferredPrompt = null;
        await promptEvent.prompt();
        const choice = await promptEvent.userChoice;

        if (choice.outcome === 'accepted') {
            showToast('Instalasi dimulai. Ikon aplikasi akan muncul di perangkat.');
        }

        updateInstallButton();
    });

    updateButton.addEventListener('click', () => {
        state.registration?.waiting?.postMessage({ type: 'SKIP_WAITING' });
        updateButton.hidden = true;
    });

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        state.deferredPrompt = event;
        updateInstallButton();
        showToast('drgEkspedisi siap di-install di perangkat ini.');
    });

    window.addEventListener('appinstalled', () => {
        state.installed = true;
        state.deferredPrompt = null;
        updateInstallButton();
        showToast('drgEkspedisi berhasil di-install.');
    });

    window.addEventListener('online', () => setConnectionState({ announce: true }));
    window.addEventListener('offline', () => setConnectionState({ announce: true }));

    // Hindari form transaksi terlihat seperti berhasil ketika perangkat offline.
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || window.navigator.onLine) return;

        const method = (form.getAttribute('method') || 'get').toLowerCase();
        if (method === 'get' || form.hasAttribute('data-offline-allowed')) return;

        event.preventDefault();
        showToast('Aksi ini membutuhkan internet. Data tidak dikirim agar tidak terjadi transaksi ganda.', 4500);
    });

    setConnectionState();
    updateInstallButton();

    if (!('serviceWorker' in window.navigator)) {
        showToast('Browser ini tidak mendukung mode offline penuh.');
        return;
    }

    const showUpdate = (registration) => {
        state.registration = registration;
        updateButton.hidden = false;
        showToast('Versi aplikasi terbaru tersedia. Tekan Perbarui.');
    };

    const observeRegistration = (registration) => {
        state.registration = registration;

        if (registration.waiting && window.navigator.serviceWorker.controller) {
            showUpdate(registration);
        }

        registration.addEventListener('updatefound', () => {
            const worker = registration.installing;
            if (!worker) return;

            worker.addEventListener('statechange', () => {
                if (worker.state === 'installed' && window.navigator.serviceWorker.controller) {
                    showUpdate(registration);
                }
            });
        });
    };

    const registerWorker = async () => {
        try {
            const registration = await window.navigator.serviceWorker.register('/service-worker.js', {
                scope: '/',
                updateViaCache: 'none',
            });

            observeRegistration(registration);
            await registration.update().catch(() => undefined);

            const readyRegistration = await window.navigator.serviceWorker.ready;
            readyRegistration.active?.postMessage({ type: 'CACHE_PUBLIC_PAGES' });
        } catch (error) {
            console.error('Service worker gagal didaftarkan:', error);
            showToast('Mode offline gagal disiapkan. Pastikan situs memakai HTTPS atau localhost.', 5000);
        }
    };

    if (document.readyState === 'complete') {
        registerWorker();
    } else {
        window.addEventListener('load', registerWorker, { once: true });
    }

    let refreshing = false;
    window.navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (refreshing) return;
        refreshing = true;
        window.location.reload();
    });

    window.applyPwaUpdate = () => {
        state.registration?.waiting?.postMessage({ type: 'SKIP_WAITING' });
    };
})();
