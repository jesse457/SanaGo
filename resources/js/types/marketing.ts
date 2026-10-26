/**
 * Public-site read model.
 *
 * These types mirror `App\Support\MarketingContent` exactly. Everything here
 * originates in the super-admin console — plans, platform settings, published
 * announcements and live counts — so the marketing pages never recast the
 * product in hard-coded fiction.
 */

export interface MarketingPlatform {
    name: string;
    tagline: string;
    support_email: string;
    support_phone: string | null;
    currency: string;
    currency_symbol: string;
    default_locale: string;
    supported_locales: string[];
    trial_days: number;
    grace_days: number;
}

export interface MarketingPlanFeature {
    label: string;
    highlighted: boolean;
}

export interface MarketingPlanLimits {
    users: number | null;
    patients: number | null;
    storage_gb: number | null;
}

export interface MarketingPlan {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    monthly_price: number;
    yearly_price: number;
    currency: string;
    is_featured: boolean;
    trial_days: number;
    grace_days: number;
    limits: MarketingPlanLimits;
    api_rate_limit: number | null;
    features: MarketingPlanFeature[];
    /** Capability key → whether this plan unlocks it. */
    capabilities: Record<string, boolean>;
}

export interface MarketingCapability {
    key: string;
    label: string;
    description: string;
}

export interface MarketingStats {
    hospitals: number;
    capabilities: number;
    plans: number;
    trial_days: number;
    locales: number;
}

export interface MarketingAnnouncement {
    id: number;
    title: string;
    body: string;
    type: string;
    published_at: string | null;
}

export interface MarketingContent {
    platform: MarketingPlatform;
    plans: MarketingPlan[];
    capabilities: MarketingCapability[];
    stats: MarketingStats;
    announcements: MarketingAnnouncement[];
    billing_cycles: string[];
}

/** Billing cycles the price toggle exposes. */
export type BillingCycle = 'monthly' | 'yearly';
