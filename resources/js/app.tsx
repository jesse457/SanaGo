import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

// Real-time is opt-in. Keep Echo/Pusher out of the entry bundle unless Reverb
// is actually configured; consumers fall back to polling when it is absent.
if (import.meta.env.VITE_REVERB_APP_KEY) {
    void import('@/echo')
        .then(({ initEcho }) => initEcho())
        .catch(() => undefined);
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        // The accent token, inlined because app.css is not parsed here.
        color: '#1c6d70',
    },
    setup({ el, App, props }) {
        if (!el) {
            return;
        }

        createRoot(el).render(<App {...props} />);
    },
});
