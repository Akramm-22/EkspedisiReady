const CACHE_NAME = 'drgekspedisi-v1';
const APP_SHELL = ['/', '/manifest.json'];

// Install: cache app shell minimal (halaman utama + manifest). Vite build
// assets (JS/CSS ber-hash) di-cache on-demand lewat fetch handler di bawah,
// bukan di-precache di sini supaya tidak perlu update SW tiap kali build.
self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

// Strategy: network-first untuk API (data harus fresh), cache-first untuk
// asset statis (JS/CSS/gambar) supaya app shell tetap kebuka saat offline.
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request).catch(() =>
                new Response(JSON.stringify({ message: 'Sedang offline. Aksi ini akan disinkronkan otomatis.' }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' },
                })
            )
        );

        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            const network = fetch(request)
                .then((response) => {
                    if (response.ok) {
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, response.clone()));
                    }

                    return response;
                })
                .catch(() => cached);

            return cached || network;
        })
    );
});

// Background Sync: dipicu browser otomatis begitu koneksi kembali, PWA
// register tag ini dari sisi client (lihat app kurir) setelah action
// offline disimpan ke IndexedDB. Actual replay logic (baca IndexedDB,
// POST ke /api/v1/courier/sync) ada di kode aplikasi, bukan di SW ini,
// supaya SW tetap ringan & tidak bergantung ke library HTTP client.
self.addEventListener('sync', (event) => {
    if (event.tag === 'drg-offline-sync') {
        event.waitUntil(self.clients.matchAll().then((clients) => {
            clients.forEach((client) => client.postMessage({ type: 'TRIGGER_OFFLINE_SYNC' }));
        }));
    }
});

// Push notification (FCM via Web Push) — menampilkan notifikasi native
// browser saat ada pesan masuk dari FcmPushService di backend.
self.addEventListener('push', (event) => {
    const data = event.data?.json() ?? {};

    event.waitUntil(
        self.registration.showNotification(data.notification?.title ?? 'drgEkspedisi', {
            body: data.notification?.body ?? '',
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: data.data ?? {},
        })
    );
});
