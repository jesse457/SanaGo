import { usePage } from '@inertiajs/react';
import type { MarketingContent } from '@/types/marketing';

/**
 * Fallback used when a page is rendered without the shared prop (an error
 * boundary, a test harness, a tenant-only deployment). It keeps the public
 * chrome renderable instead of crashing on `undefined`.
 */
const EMPTY: MarketingContent = {
    platform: {
        name: 'SanaGo',
        tagline: '',
        support_email: '',
        support_phone: null,
        currency: 'XAF',
        currency_symbol: 'FCFA',
        default_locale: 'en',
        supported_locales: ['en'],
        trial_days: 14,
        grace_days: 3,
    },
    plans: [],
    capabilities: [],
    stats: {
        hospitals: 0,
        capabilities: 0,
        plans: 0,
        trial_days: 14,
        locales: 1,
    },
    announcements: [],
    billing_cycles: ['monthly', 'yearly'],
};

/**
 * The console-owned content for the public site: plans, platform identity,
 * announcements and live counts.
 */
export function useMarketing(): MarketingContent {
    const { props } = usePage<{ marketing?: MarketingContent }>();
    const marketing = props.marketing;

    if (!marketing) {
        return EMPTY;
    }

    return {
        ...EMPTY,
        ...marketing,
        platform: { ...EMPTY.platform, ...marketing.platform },
        stats: { ...EMPTY.stats, ...marketing.stats },
    };
}

export type { MarketingContent };
