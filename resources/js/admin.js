import '../css/app.css';
import './bootstrap';
import './page-loader';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

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
