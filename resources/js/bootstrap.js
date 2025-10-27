import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
  wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 8080),
  wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 8080),
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME === 'https'),
  enabledTransports: ['ws', 'wss'],
});
