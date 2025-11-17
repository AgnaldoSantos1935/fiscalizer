import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// CSRF header from global variable set in Blade layout
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = (window.CSRFToken ?? '');
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

try {
    const PUSHER_KEY = import.meta.env.VITE_PUSHER_APP_KEY;
    if (PUSHER_KEY) {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: PUSHER_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
            wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
            wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
            wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    } else {
        console.warn('[Echo] VITE_PUSHER_APP_KEY ausente; Echo não inicializado.');
    }
} catch (err) {
    console.warn('[Echo] falha ao iniciar:', err);
}
