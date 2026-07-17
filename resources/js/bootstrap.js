import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Sanctum SPA/CSRF cookie sudah ditangani otomatis oleh middleware
// `EnsureFrontendRequestsAreStateful` selama request datang dari domain
// yang sama (first-party, bukan mobile app kurir yang pakai token API).
