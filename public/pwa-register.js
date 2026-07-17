(() => {
    const setConnectionState = () => {
        const online = navigator.onLine;
        document.documentElement.dataset.connection = online ? 'online' : 'offline';
        window.dispatchEvent(new CustomEvent('connectionchange', { detail: { online } }));
    };

    window.addEventListener('online', setConnectionState);
    window.addEventListener('offline', setConnectionState);
    setConnectionState();

    if (!('serviceWorker' in navigator)) {
        return;
    }

    const registerWorker = async () => {
        try {
            const registration = await navigator.serviceWorker.register('/service-worker.js', { scope: '/' });

            registration.addEventListener('updatefound', () => {
                const worker = registration.installing;
                if (!worker) return;

                worker.addEventListener('statechange', () => {
                    if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                        window.dispatchEvent(new CustomEvent('pwaupdateavailable', { detail: { registration } }));
                    }
                });
            });
        } catch (error) {
            console.error('Service worker gagal didaftarkan:', error);
        }
    };

    if (document.readyState === 'complete') {
        registerWorker();
    } else {
        window.addEventListener('load', registerWorker, { once: true });
    }

    let refreshing = false;
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (refreshing) return;
        refreshing = true;
        window.location.reload();
    });

    window.applyPwaUpdate = async () => {
        const registration = await navigator.serviceWorker.getRegistration('/');
        registration?.waiting?.postMessage({ type: 'SKIP_WAITING' });
    };
})();
