import type { route as routeFn } from 'ziggy-js';
import type { Auth } from '@/types/auth';

declare global {
    // This allows you to use route() as a global function
    var route: typeof routeFn;
}

// This is necessary if you are using window.route
interface Window {
    route: typeof routeFn;
}
declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            locale: string;
            translations: Record<string, any>;
            flash?: {
                success?: string;
                error?: string;
            };
            [key: string]: unknown;
        };
    }
}
