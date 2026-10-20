/**
 * Wire types for the landlord ("platform control") panel.
 *
 * These mirror exactly what the controllers return — see
 * `app/Http/Controllers/TenantController.php`,
 * `TenantSubscriptionController.php`, `SubscriptionController.php`,
 * `PlanController.php` and the `Platform*` controllers. Everything is
 * optional-friendly on purpose: a controller that stops sending a field must
 * degrade the panel, never crash it.
 */

/** Shared `platform` prop. NOTE: not shared yet by HandleInertiaRequests. */
export interface PlatformSharedProps {
    name?: string;
    tagline?: string | null;
    currency?: string;
    currency_symbol?: string;
    default_locale?: string;
    supported_locales?: string[];
    trial_days?: number;
    support_email?: string | null;
    support_phone?: string | null;
    realtime_enabled?: boolean;
    email_notifications_enabled?: boolean;
    maintenance_mode?: boolean;
    maintenance_message?: string | null;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    current_page?: number;
    last_page?: number;
    from?: number | null;
    to?: number | null;
    total?: number;
    per_page?: number;
}

export interface SubscriptionSummaryRow {
    plan_name: string;
    status: string;
    status_label: string;
    status_tone: string;
    end_date: string | null;
    days_remaining: number | null;
}

export interface TenantListRow {
    id: string;
    slug: string;
    name: string;
    domains: string[];
    is_active: boolean;
    suspended_at: string | null;
    archived_at: string | null;
    created_at: string | null;
    subscription: SubscriptionSummaryRow | null;
}

export interface TenantCounts {
    all: number;
    active: number;
    suspended: number;
    archived: number;
}

export interface TenantFilters {
    search: string;
    status: string;
}

/** POST /tenants payload (StoreTenantRequest only validates the first three). */
export interface TenantStorePayload {
    slug: string;
    name: string;
    domain: string;
    admin_name: string;
    admin_email: string;
    admin_password: string;
    plan_id: string;
    billing_cycle: 'monthly' | 'yearly';
    trial: boolean;
    demo_data: boolean;
}

export interface PlanOption {
    id: number;
    name: string;
    monthly_price: number;
    yearly_price: number;
    trial_days?: number;
    sort_order?: number;
    currency?: string;
    limits?: Record<string, number | null>;
}

export interface TenantSubscriptionPlan {
    id: number;
    name: string;
    features: Record<string, boolean>;
}

export interface TenantInvoice {
    id: number;
    number: string;
    total: number;
    currency: string;
    status: string;
    period_start: string | null;
    period_end: string | null;
    paid_at: string | null;
}

export interface TenantNote {
    id: number;
    note: string;
    user_name: string | null;
    created_at: string | null;
}

export interface TenantUser {
    id: number;
    name: string;
    email: string;
    roles: string[];
    is_active: boolean;
}

export interface TenantSubscription {
    id: number;
    plan_id: number | null;
    plan_name: string;
    plan: TenantSubscriptionPlan | null;
    billing_cycle: string;
    price: number;
    currency: string;
    start_date: string | null;
    end_date: string | null;
    status: string;
    status_label: string;
    status_tone: string;
    days_remaining: number | null;
    auto_renew: boolean;
    cancel_at_period_end: boolean;
    limits: Record<string, number | null>;
    limit_overrides: Record<string, number | null>;
    feature_overrides: Record<string, boolean>;
    features: Record<string, boolean>;
    notes: string | null;
}

export interface TenantUsage {
    users: number;
    patients: number;
    appointments: number;
    invoices: number;
    storage_gb?: number;
}

export interface TenantHealth {
    reachable: boolean;
    users: number;
    last_activity: string | null;
    migrations_ok: boolean;
}

export interface TenantDetail {
    id: string;
    slug: string;
    name: string;
    domains: string[];
    is_active: boolean;
    suspended_at: string | null;
    archived_at: string | null;
    archived_reason: string | null;
    trial_ends_at: string | null;
    settings_json?: Record<string, unknown> | null;
    created_at: string | null;
    updated_at: string | null;
    last_seen_at: string | null;
    subscription: TenantSubscription | null;
    usage: TenantUsage;
    health: TenantHealth;
    users: TenantUser[];
    invoices: TenantInvoice[];
    notes: TenantNote[];
}

/** GET tenants.subscription.preview JSON body. */
export interface ProrationPreview {
    days_remaining: number | null;
    total_days?: number;
    credit?: number;
    charge?: number;
    amount_due: number;
    direction: 'new' | 'upgrade' | 'downgrade' | 'lateral' | string;
}

export interface FeatureOption {
    value: string;
    label: string;
    description: string;
}

export interface PlanRecord {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    monthly_price: number;
    yearly_price: number;
    currency: string;
    is_active: boolean;
    is_featured: boolean;
    sort_order: number;
    max_users_per_tenant: number | null;
    max_patients_per_tenant: number | null;
    storage_gb: number | null;
    trial_days: number | null;
    grace_days?: number | null;
    api_rate_limit: number | null;
    features_json: { feature_label: string; is_highlighted: boolean }[];
    feature_flags: Record<string, boolean>;
    subscriptions_count?: number;
}

export interface SubscriptionListRow {
    id: number;
    plan_name: string;
    tenant_id: string;
    tenant_name: string;
    billing_cycle: string;
    price: number;
    currency: string;
    start_date: string | null;
    end_date: string | null;
    status: string;
    status_label: string;
    status_tone: string;
    days_remaining: number | null;
    auto_renew: boolean;
    cancel_at_period_end: boolean;
    created_at: string | null;
}

export interface RevenueSummary {
    mrr: number;
    arr: number;
    active: number;
    trialing: number;
    past_due: number;
    cancelled?: number;
    currency: string;
}

export interface SubscriptionDetail {
    id: number;
    plan_name: string;
    tenant_id: string;
    tenant_name: string;
    tenant_domain: string | null;
    billing_cycle: string;
    price: number;
    currency: string;
    start_date: string | null;
    end_date: string | null;
    status: string;
    status_label: string;
    status_tone: string;
    days_remaining: number | null;
    auto_renew: boolean;
    cancel_at_period_end: boolean;
    features_json: Record<string, boolean>;
    features: Record<string, boolean>;
    limits: Record<string, number | null>;
    notes: string | null;
    created_at: string | null;
    updated_at: string | null;
}

export interface PlatformSettings {
    platform_name?: string;
    platform_tagline?: string | null;
    currency?: string;
    currency_symbol?: string;
    default_locale?: string;
    supported_locales?: string[];
    trial_days?: number;
    grace_days?: number;
    support_email?: string;
    support_phone?: string | null;
    realtime_enabled?: boolean;
    email_notifications_enabled?: boolean;
    maintenance_mode?: boolean;
    maintenance_message?: string | null;
}

export interface BillingUsageCell {
    used: number;
    limit: number | null;
}

export interface BillingRow {
    tenant_id: string;
    tenant_name: string;
    plan_name: string;
    billing_cycle: string;
    price: number;
    currency: string;
    status: string;
    status_label: string;
    status_tone: string;
    end_date: string | null;
    users: BillingUsageCell;
    patients: BillingUsageCell;
    storage_gb: BillingUsageCell;
    outstanding: number;
}

export interface BillingInvoice {
    id: number;
    number: string;
    tenant_id: string;
    tenant_name: string;
    total: number;
    currency: string;
    status: string;
    period_start: string | null;
    period_end: string | null;
    due_at: string | null;
    paid_at: string | null;
}

export interface BillingTotals {
    outstanding: number;
    collected: number;
    tenants: number;
}

export interface AuditLogRow {
    id: number;
    action: string;
    description: string | null;
    actor_name: string | null;
    actor_email: string | null;
    tenant_id: string | null;
    subject_type: string | null;
    subject_id: string | null;
    properties: Record<string, unknown> | null;
    ip_address: string | null;
    created_at: string | null;
}

export interface HealthTenantRow {
    id: string;
    name: string;
    domain: string | null;
    is_active: boolean;
    reachable: boolean;
    migrations_ok: boolean;
    users: number;
    patients: number;
    plan_name: string | null;
    subscription_status: string | null;
    subscription_tone: string | null;
    end_date: string | null;
    days_remaining: number | null;
    last_seen_at: string | null;
}

export interface PlatformSystemStatus {
    database: { ok: boolean; message: string };
    queue: { driver: string; ok: boolean };
    broadcast_driver: string;
    mail_driver: string;
    pending_jobs: number | null;
}

export interface ExpiringSubscription {
    tenant_id: string;
    tenant_name: string;
    plan_name: string | null;
    end_date: string | null;
    days_remaining: number | null;
}

export interface PlatformAttention {
    expiring: ExpiringSubscription[];
    past_due: string[];
}

export interface DashboardMetrics {
    total_tenants: number;
    active_tenants: number;
    suspended_tenants: number;
    archived_tenants: number;
    active_subscriptions: number;
    trialing_subscriptions: number;
    past_due_subscriptions: number;
    mrr: number;
    arr: number;
    currency: string;
    pending_demo_requests: number;
    open_tickets: number;
}

export interface RecentTenantRow {
    id: string;
    name: string;
    domain: string | null;
    is_active: boolean;
    plan_name: string | null;
    status: string | null;
    created_at: string | null;
}

export interface RecentActivityRow {
    type: string;
    label: string;
    detail: string | null;
    tenant_id?: string | null;
    time: string | null;
    created_at?: string | null;
}
