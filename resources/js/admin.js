import '../css/app.css';
import './bootstrap';
import './page-loader';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const ensurePwaMetadata = () => {
    const ensureMeta = (name, content) => {
        if (document.querySelector(`meta[name="${name}"]`)) return;
        const meta = document.createElement('meta');
        meta.name = name;
        meta.content = content;
        document.head.appendChild(meta);
    };

    const ensureLink = (selector, attributes) => {
        if (document.querySelector(selector)) return;
        const link = document.createElement('link');
        Object.entries(attributes).forEach(([key, value]) => link.setAttribute(key, value));
        document.head.appendChild(link);
    };

    ensureMeta('theme-color', '#18b378');
    ensureMeta('application-name', 'drgEkspedisi');
    ensureMeta('mobile-web-app-capable', 'yes');
    ensureMeta('apple-mobile-web-app-capable', 'yes');
    ensureMeta('apple-mobile-web-app-title', 'drgEkspedisi');
    ensureMeta('apple-mobile-web-app-status-bar-style', 'default');

    ensureLink('link[rel="manifest"]', { rel: 'manifest', href: '/manifest.json' });
    ensureLink('link[rel="icon"][sizes="192x192"]', {
        rel: 'icon',
        href: '/icons/icon-192.png',
        type: 'image/png',
        sizes: '192x192',
    });
    ensureLink('link[rel="apple-touch-icon"]', {
        rel: 'apple-touch-icon',
        href: '/icons/apple-touch-icon.png',
        sizes: '180x180',
    });

    if (!document.querySelector('script[data-pwa-register]')) {
        const script = document.createElement('script');
        script.src = '/pwa-register.js';
        script.dataset.pwaRegister = 'true';
        script.defer = true;
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
