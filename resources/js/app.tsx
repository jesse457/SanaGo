import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
// Side-effect import: initialises `window.Echo` when VITE_REVERB_APP_KEY is
// set, and leaves it undefined (polling fallback) when it is not.
import '@/echo';

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
