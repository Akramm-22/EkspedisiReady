const VERSION = 'drgekspedisi-v6';
const STATIC_CACHE = `${VERSION}-static`;
const PAGE_CACHE = `${VERSION}-pages`;
const INERTIA_CACHE = `${VERSION}-inertia`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.json',
    '/pwa-register.js',
    '/icons/icon.svg',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon-maskable.svg',
    '/icons/icon-maskable-512.png',
    '/icons/apple-touch-icon.png',
];

const PUBLIC_PAGE_PATHS = ['/', '/lacak', '/tentang'];

const PRIVATE_PREFIXES = [
    '/admin',
    '/customer',
    '/courier',
    '/login',
    '/logout',
    '/shipments',
    '/pembayaran',
    '/sanctum',
    '/broadcasting',
];

const isPrivatePath = (pathname) =>
    PRIVATE_PREFIXES.some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));

const isCacheableResponse = (response) => {
    if (!response || !response.ok || response.redirected || response.bodyUsed) {
        return false;
    }

    if (!['basic', 'cors'].includes(response.type)) {
        return false;
    }

    // Laravel lazim memberi `Cache-Control: no-cache, private` pada halaman
    // publik. Karena rute akun sudah dipisahkan di atas, response publik tetap
    // aman disimpan di cache browser perangkat ini. Hanya `no-store` ditolak.
    const cacheControl = response.headers.get('Cache-Control') || '';
    return !/no-store/i.test(cacheControl);
};

const cloneForCache = (response) => {
    if (!isCacheableResponse(response)) {
        return null;
    }

    try {
        // Clone dibuat sinkron sebelum operasi async lain membaca body.
        return response.clone();
    } catch (error) {
        console.warn('[PWA] Response tidak dapat di-clone untuk cache.', error);
        return null;
    }
};

const putSafely = async (cache, request, response) => {
    const copy = cloneForCache(response);
    if (!copy) {
        return false;
    }

    try {
        await cache.put(request, copy);
        return true;
    } catch (error) {
        console.warn('[PWA] Gagal menyimpan response ke cache.', error);
        return false;
    }
};

const canonicalPageRequest = (request) => {
    const url = new URL(request.url);
    return new Request(`${url.origin}${url.pathname}`, {
        method: 'GET',
        headers: { Accept: 'text/html' },
        credentials: 'same-origin',
    });
};

self.addEventListener('install', (event) => {
    event.waitUntil(
        (async () => {
            const cache = await caches.open(STATIC_CACHE);

            await Promise.allSettled(
                PRECACHE_URLS.map(async (url) => {
                    const request = new Request(url, { cache: 'reload' });
                    const response = await fetch(request);
                    await putSafely(cache, request, response);
                })
            );

            // Service worker baru langsung menggantikan versi lama yang error.
            await self.skipWaiting();
        })()
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        (async () => {
            const activeCaches = new Set([STATIC_CACHE, PAGE_CACHE, INERTIA_CACHE]);
            const keys = await caches.keys();

            await Promise.all(
                keys
                    .filter((key) => key.startsWith('drgekspedisi-') && !activeCaches.has(key))
                    .map((key) => caches.delete(key))
            );

            await self.clients.claim();
        })()
    );
});

const offlinePage = async () => (await caches.match(OFFLINE_URL)) || Response.error();

const networkOnlyWithOfflineFallback = async (request) => {
    try {
        return await fetch(request);
    } catch (error) {
        return request.mode === 'navigate' ? offlinePage() : Response.error();
    }
};

const networkFirstNavigation = async (request) => {
    const cache = await caches.open(PAGE_CACHE);
    const cacheKey = canonicalPageRequest(request);

    try {
        const response = await fetch(request);
        await putSafely(cache, cacheKey, response);
        return response;
    } catch (error) {
        return (await cache.match(cacheKey)) || offlinePage();
    }
};

const networkFirstInertia = async (request) => {
    const cache = await caches.open(INERTIA_CACHE);

    try {
        const response = await fetch(request);
        await putSafely(cache, request, response);
        return response;
    } catch (error) {
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }

        return new Response(
            JSON.stringify({ message: 'Halaman ini belum tersimpan untuk penggunaan offline.' }),
            {
                status: 503,
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-store',
                    'X-PWA-Offline': 'true',
                },
            }
        );
    }
};

const staleWhileRevalidate = async (event) => {
    const { request } = event;
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    const networkPromise = fetch(request)
        .then(async (response) => {
            await putSafely(cache, request, response);
            return response;
        })
        .catch(() => null);

    if (cached) {
        // Pastikan proses pembaruan cache tetap hidup setelah cached response
        // dikembalikan kepada halaman.
        event.waitUntil(networkPromise.then(() => undefined));
        return cached;
    }

    return (await networkPromise) || Response.error();
};

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || request.headers.has('range')) {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request).catch(
                () =>
                    new Response(JSON.stringify({ message: 'Tidak ada koneksi. Silakan coba lagi saat online.' }), {
                        status: 503,
                        headers: {
                            'Content-Type': 'application/json',
                            'Cache-Control': 'no-store',
                            'X-PWA-Offline': 'true',
                        },
                    })
            )
        );
        return;
    }

    const inertiaRequest = request.headers.get('X-Inertia') === 'true';

    if (inertiaRequest) {
        event.respondWith(
            isPrivatePath(url.pathname)
                ? networkOnlyWithOfflineFallback(request)
                : networkFirstInertia(request)
        );
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            isPrivatePath(url.pathname)
                ? networkOnlyWithOfflineFallback(request)
                : networkFirstNavigation(request)
        );
        return;
    }

    if (['style', 'script', 'image', 'font', 'manifest'].includes(request.destination)) {
        event.respondWith(staleWhileRevalidate(event));
    }
});

self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data?.type === 'CACHE_PUBLIC_PAGES') {
        event.waitUntil(
            (async () => {
                const cache = await caches.open(PAGE_CACHE);

                await Promise.allSettled(
                    PUBLIC_PAGE_PATHS.map(async (path) => {
                        const request = new Request(path, { headers: { Accept: 'text/html' } });
                        const response = await fetch(request);
                        await putSafely(cache, canonicalPageRequest(request), response);
                    })
                );
            })()
        );
    }
});

self.addEventListener('sync', (event) => {
    if (event.tag === 'drg-offline-sync') {
        event.waitUntil(
            self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
                clients.forEach((client) => client.postMessage({ type: 'TRIGGER_OFFLINE_SYNC' }));
            })
        );
    }
});

self.addEventListener('push', (event) => {
    let data = {};

    try {
        data = event.data?.json() ?? {};
    } catch (error) {
        data = { notification: { body: event.data?.text() ?? '' } };
    }

    event.waitUntil(
        self.registration.showNotification(data.notification?.title ?? 'drgEkspedisi', {
            body: data.notification?.body ?? '',
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: data.data ?? {},
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(async (clients) => {
            const existing = clients.find((client) => new URL(client.url).origin === self.location.origin);

            if (existing) {
                await existing.focus();
                await existing.navigate(targetUrl);
                return;
            }

            await self.clients.openWindow(targetUrl);
        })
    );
});
