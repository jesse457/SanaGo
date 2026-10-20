import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            fonts: [
                /*
                 * Inter is the UI face: the approved free substitute for X's
                 * proprietary Chirp. The four weights map to the roles the
                 * design system actually uses — 400 body, 500 labels/UI,
                 * 600 headings and buttons, 700 emphasis.
                 */
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    subsets: ['latin', 'latin-ext'],
                    display: 'swap',
                }),
            ],
        }),
        inertia(),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
});
