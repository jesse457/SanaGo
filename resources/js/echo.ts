/**
 * Laravel Echo bootstrap.
 *
 * Real-time is opt-in: it only initialises when `VITE_REVERB_APP_KEY` is
 * present at build time. Without it `window.Echo` stays `undefined`, and every
 * consumer (the notification bell, activity feeds) falls back to polling. That
 * keeps local/dev and Reverb-less deployments working with zero configuration.
 *
 * Imported once, for its side effect, from `resources/js/app.tsx`.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo?: Echo<'reverb'>;
    }
}

function truthy(value: string | undefined, fallback = ''): string {
    return (value ?? fallback).trim();
}

export function initEcho(): Echo<'reverb'> | undefined {
    const key = truthy(import.meta.env.VITE_REVERB_APP_KEY);

    if (!key) {
        // No key configured — leave window.Echo undefined so consumers poll.
        return undefined;
    }

    // Already initialised (Vite HMR re-evaluating this module).
    if (typeof window !== 'undefined' && window.Echo) {
        return window.Echo;
    }

    // Laravel Echo's Pusher broadcaster expects the global constructor.
    window.Pusher = Pusher;

    const scheme = truthy(import.meta.env.VITE_REVERB_SCHEME, 'https');
    const forceTLS = scheme === 'https';

    const echo = new Echo<'reverb'>({
        broadcaster: 'reverb',
        key,
        wsHost: truthy(
            import.meta.env.VITE_REVERB_HOST,
            window.location.hostname,
        ),
        wsPort: Number(truthy(import.meta.env.VITE_REVERB_PORT, '80')) || 80,
        wssPort: Number(truthy(import.meta.env.VITE_REVERB_PORT, '443')) || 443,
        forceTLS,
        enabledTransports: ['ws', 'wss'],
        // Private (user) channels authorise through the session cookie.
        authEndpoint: '/broadcasting/auth',
    });

    window.Echo = echo;

    return echo;
}

initEcho();

export default initEcho;
