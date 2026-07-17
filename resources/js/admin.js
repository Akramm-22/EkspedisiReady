import '../css/app.css';
import './bootstrap';
import './page-loader';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const ensurePwaMetadata = () => {
    if (!document.querySelector('meta[name="theme-color"]')) {
        const theme = document.createElement('meta');
        theme.name = 'theme-color';
        theme.content = '#18b378';
        document.head.appendChild(theme);
    }

    if (!document.querySelector('link[rel="manifest"]')) {
        const manifest = document.createElement('link');
        manifest.rel = 'manifest';
        manifest.href = '/manifest.json';
        document.head.appendChild(manifest);
    }

    if (!document.querySelector('link[rel="icon"]')) {
        const icon = document.createElement('link');
        icon.rel = 'icon';
        icon.type = 'image/svg+xml';
        icon.href = '/icons/icon.svg';
        document.head.appendChild(icon);
    }

    if (!document.querySelector('script[data-pwa-register]')) {
        const script = document.createElement('script');
        script.src = '/pwa-register.js';
        script.dataset.pwaRegister = 'true';
        document.head.appendChild(script);
    }
};

ensurePwaMetadata();

window.Alpine = Alpine;
Alpine.start();

// Reverb/Echo bersifat opsional (dipakai fitur realtime kalau env-nya
// diisi) — dibungkus try/catch + cek env dulu, supaya kalau REVERB_*
// belum diisi di .env, halaman tetap render normal tanpa JS error.
try {
    if (import.meta.env.VITE_REVERB_APP_KEY) {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    }
} catch (e) {
    console.warn('Reverb/Echo tidak diaktifkan:', e);
}
