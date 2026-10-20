import {
    ArrowPathRoundedSquareIcon,
    ArrowTopRightOnSquareIcon,
    BanknotesIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClockIcon,
    CreditCardIcon,
    DocumentTextIcon,
    ExclamationTriangleIcon,
    LifebuoyIcon,
    PowerIcon,
    Squares2X2Icon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import type { ComponentType, SVGProps } from 'react';
import {
    Alert,
    Badge,
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    ProgressBar,
    SectionCard,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

/**
 * Props sent by `TenantController@billing` (built on `PlatformContext`):
 *
 *   subscription (summary or null), tenant_name, features (key → bool),
 *   limits (key → int|null, null = unlimited), usage (key → int), invoices.
 */
interface SubscriptionSummary {
    id: number;
    plan_id?: number | null;
    plan_name: string | null;
    status: string;
    status_label: string;
    status_tone: string;
    billing_cycle: string;
    price: number;
    currency: string;
    start_date: string | null;
    end_date: string | null;
    trial_ends_at: string | null;
    days_remaining: number | null;
    auto_renew: boolean;
    cancel_at_period_end: boolean;
    is_on_trial: boolean;
    grants_access: boolean;
}

interface InvoiceRow {
    id: number;
    number: string;
    total: number;
    currency: string;
    status: string;
    period_start: string | null;
    period_end: string | null;
    paid_at: string | null;
}

interface BillingProps {
    subscription: SubscriptionSummary | null;
    tenant_name: string;
    features: Record<string, boolean>;
    limits: Record<string, number | null>;
    usage: Record<string, number>;
    invoices: InvoiceRow[];
}

/** Backend tones speak `info`; the Badge vocabulary calls that `accent`. */
const TONE_MAP: Record<string, BadgeTone> = {
    positive: 'positive',
    warning: 'warning',
    critical: 'critical',
    neutral: 'neutral',
    info: 'accent',
    accent: 'accent',
    muted: 'muted',
};

const INVOICE_TONES: Record<string, BadgeTone> = {
    draft: 'muted',
    pending: 'warning',
    paid: 'positive',
    overdue: 'critical',
    void: 'muted',
};

const FEATURE_ICONS: Record<string, ComponentType<SVGProps<SVGSVGElement>>> = {
    patients: UsersIcon,
    appointments: CalendarDaysIcon,
    consultations: DocumentTextIcon,
    admissions: Squares2X2Icon,
    vitals: CheckCircleIcon,
    laboratory: DocumentTextIcon,
    radiology: DocumentTextIcon,
    pharmacy: Squares2X2Icon,
    inventory: Squares2X2Icon,
    surgery: Squares2X2Icon,
    emergency: ExclamationTriangleIcon,
    billing: BanknotesIcon,
    cash_register: BanknotesIcon,
    insurance_claims: BanknotesIcon,
    certificates: DocumentTextIcon,
    reports: Squares2X2Icon,
    audit_logs: DocumentTextIcon,
    sms_notifications: CheckCircleIcon,
    api_access: ArrowPathRoundedSquareIcon,
    custom_branding: Squares2X2Icon,
};

const METER_ORDER = ['users', 'patients', 'storage_gb'] as const;

function badgeTone(tone?: string | null): BadgeTone {
    return TONE_MAP[tone ?? 'neutral'] ?? 'neutral';
}

function formatDate(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatMoney(value?: number | string | null, currency = 'XAF'): string {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const amount = typeof value === 'string' ? Number(value) : value;

    if (Number.isNaN(amount)) {
        return String(value);
    }

    const formatted = new Intl.NumberFormat(undefined, {
        maximumFractionDigits: 0,
    }).format(amount);

    return currency === 'XAF'
        ? `${formatted} FCFA`
        : `${formatted} ${currency}`;
}

function humanise(value?: string | null): string {
    if (!value) {
        return '—';
    }

    return value
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, (character) => character.toUpperCase());
}

/**
 * Billing/Index — the tenant-facing subscription portal: plan summary, trial
 * and access banners, usage-vs-limit meters, the feature matrix and the
 * invoice history. There is no self-service checkout; upgrades route to
 * support.
 */
export default function BillingIndex({
    subscription,
    tenant_name,
    features = {},
    limits = {},
    usage = {},
    invoices = [],
}: BillingProps) {
    const { t } = useTranslation();

    const planName =
        subscription?.plan_name ?? t('page.billing_page.no_subscription');
    const cycleLabel =
        subscription?.billing_cycle === 'yearly'
            ? t('page.billing_page.cycle_yearly')
            : t('page.billing_page.cycle_monthly');
    const cycleSuffix =
        subscription?.billing_cycle === 'yearly'
            ? t('page.billing_page.per_year')
            : t('page.billing_page.per_month');

    const featureEntries = Object.entries(features);
    const enabledCount = featureEntries.filter(([, enabled]) => enabled).length;
    const daysRemaining = subscription?.days_remaining ?? 0;

    return (
        <AppLayout
            headerTitle={t('page.billing_page.head_title')}
            headerSubtitle={tenant_name}
        >
            <Head title={t('page.billing_page.head_title')} />

            <div className="mx-auto flex max-w-6xl flex-col gap-6 pb-12">
                <PageHeader
                    eyebrow={tenant_name || t('page.billing_page.eyebrow')}
                    title={t('page.billing_page.title')}
                    description={t('page.billing_page.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        { label: t('page.billing_page.head_title') },
                    ]}
                    actions={
                        <Button
                            icon={LifebuoyIcon}
                            href={route('support-tickets.index')}
                        >
                            {t('page.billing_page.contact_support_cta')}
                        </Button>
                    }
                />

                {!subscription ? (
                    <EmptyState
                        icon={CreditCardIcon}
                        title={t('page.billing_page.no_subscription')}
                        description={t(
                            'page.billing_page.no_subscription_desc',
                        )}
                        action={{
                            label: t('page.billing_page.contact_support_cta'),
                            href: route('support-tickets.index'),
                            icon: LifebuoyIcon,
                        }}
                    />
                ) : (
                    <>
                        {subscription.is_on_trial && (
                            <Alert
                                tone="info"
                                title={t(
                                    'page.billing_page.trial_banner_title',
                                )}
                            >
                                {t('page.billing_page.trial_banner_desc', {
                                    date: formatDate(
                                        subscription.trial_ends_at ??
                                            subscription.end_date,
                                    ),
                                    days: daysRemaining,
                                })}
                            </Alert>
                        )}

                        {!subscription.grants_access && (
                            <Alert
                                tone="critical"
                                title={t('page.billing_page.no_access_banner')}
                            />
                        )}

                        {subscription.cancel_at_period_end &&
                            subscription.grants_access && (
                                <Alert tone="warning">
                                    {t(
                                        'page.billing_page.cancel_at_period_end',
                                        {
                                            date: formatDate(
                                                subscription.end_date,
                                            ),
                                        },
                                    )}
                                </Alert>
                            )}

                        <StatGrid columns={4}>
                            <StatCard
                                label={t('page.billing_page.plan')}
                                value={planName}
                                icon={CreditCardIcon}
                                tone="accent"
                            />
                            <StatCard
                                label={t('page.billing_page.price')}
                                value={formatMoney(
                                    subscription.price,
                                    subscription.currency,
                                )}
                                icon={BanknotesIcon}
                                hint={cycleSuffix}
                                tone="positive"
                            />
                            <StatCard
                                label={t('page.billing_page.status')}
                                value={subscription.status_label}
                                icon={
                                    subscription.grants_access
                                        ? CheckCircleIcon
                                        : PowerIcon
                                }
                                tone={
                                    subscription.grants_access
                                        ? 'positive'
                                        : 'critical'
                                }
                            />
                            <StatCard
                                label={t('page.billing_page.days_remaining')}
                                value={daysRemaining}
                                icon={ClockIcon}
                                hint={`${cycleLabel} · ${t('page.billing_page.days')}`}
                                tone={
                                    daysRemaining <= 7
                                        ? 'critical'
                                        : daysRemaining <= 30
                                          ? 'warning'
                                          : 'positive'
                                }
                            />
                        </StatGrid>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div className="flex flex-col gap-6 lg:col-span-2">
                                <SectionCard
                                    title={t('page.billing_page.plan_details')}
                                    icon={CreditCardIcon}
                                    actions={
                                        <Badge
                                            tone={badgeTone(
                                                subscription.status_tone,
                                            )}
                                            dot
                                        >
                                            {subscription.status_label}
                                        </Badge>
                                    }
                                >
                                    <KeyValueGrid columns={2}>
                                        <KeyValue
                                            label={t('page.billing_page.plan')}
                                            value={planName}
                                        />
                                        <KeyValue
                                            label={t(
                                                'page.billing_page.billing_cycle',
                                            )}
                                            value={cycleLabel}
                                        />
                                        <KeyValue
                                            label={t(
                                                'page.billing_page.start_date',
                                            )}
                                            value={formatDate(
                                                subscription.start_date,
                                            )}
                                            icon={CalendarDaysIcon}
                                        />
                                        <KeyValue
                                            label={t(
                                                'page.billing_page.end_date',
                                            )}
                                            value={formatDate(
                                                subscription.end_date,
                                            )}
                                            icon={CalendarDaysIcon}
                                        />
                                        {subscription.trial_ends_at && (
                                            <KeyValue
                                                label={t(
                                                    'page.billing_page.trial_ends_at',
                                                )}
                                                value={formatDate(
                                                    subscription.trial_ends_at,
                                                )}
                                                icon={ClockIcon}
                                            />
                                        )}
                                        <KeyValue
                                            label={t(
                                                'page.billing_page.auto_renew',
                                            )}
                                            value={
                                                subscription.auto_renew
                                                    ? t('page.billing_page.yes')
                                                    : t('page.billing_page.no')
                                            }
                                            icon={ArrowPathRoundedSquareIcon}
                                        />
                                    </KeyValueGrid>
                                </SectionCard>

                                <SectionCard
                                    title={t('page.billing_page.usage_title')}
                                    description={t(
                                        'page.billing_page.usage_description',
                                    )}
                                    icon={UsersIcon}
                                >
                                    <div className="flex flex-col gap-5">
                                        {METER_ORDER.map((key) => {
                                            const limit = limits[key] ?? null;
                                            const used = usage[key] ?? 0;
                                            const unlimited = limit === null;
                                            const percent = unlimited
                                                ? 0
                                                : Math.min(
                                                      Math.round(
                                                          (used /
                                                              Math.max(
                                                                  limit,
                                                                  1,
                                                              )) *
                                                              100,
                                                      ),
                                                      100,
                                                  );
                                            const label =
                                                key === 'users'
                                                    ? t(
                                                          'page.billing_page.usage_users',
                                                      )
                                                    : key === 'patients'
                                                      ? t(
                                                            'page.billing_page.usage_patients',
                                                        )
                                                      : t(
                                                            'page.billing_page.usage_storage',
                                                        );

                                            return (
                                                <div key={key}>
                                                    <div className="mb-1.5 flex items-baseline justify-between gap-3">
                                                        <span className="text-[12.5px] font-medium text-ink">
                                                            {label}
                                                        </span>
                                                        <span className="text-[12px] text-ink-subtle tabular-nums">
                                                            {unlimited
                                                                ? `${used} · ${t('page.billing_page.usage_unlimited')}`
                                                                : t(
                                                                      'page.billing_page.usage_of',
                                                                      {
                                                                          used,
                                                                          limit,
                                                                      },
                                                                  )}
                                                        </span>
                                                    </div>
                                                    <ProgressBar
                                                        value={
                                                            unlimited ? 0 : used
                                                        }
                                                        max={
                                                            unlimited
                                                                ? 1
                                                                : Math.max(
                                                                      limit,
                                                                      1,
                                                                  )
                                                        }
                                                        tone={
                                                            unlimited
                                                                ? 'positive'
                                                                : percent >= 90
                                                                  ? 'critical'
                                                                  : percent >=
                                                                      70
                                                                    ? 'warning'
                                                                    : 'accent'
                                                        }
                                                        label={label}
                                                    />
                                                </div>
                                            );
                                        })}
                                    </div>
                                </SectionCard>

                                <SectionCard
                                    title={t(
                                        'page.billing_page.invoices_title',
                                    )}
                                    description={t(
                                        'page.billing_page.invoices_description',
                                    )}
                                    icon={DocumentTextIcon}
                                    contentClassName="px-0 py-0 sm:px-0"
                                >
                                    {invoices.length === 0 ? (
                                        <div className="px-5 py-6 sm:px-6">
                                            <EmptyState
                                                panel={false}
                                                icon={DocumentTextIcon}
                                                title={t(
                                                    'page.billing_page.no_invoices',
                                                )}
                                                description={t(
                                                    'page.billing_page.no_invoices_desc',
                                                )}
                                            />
                                        </div>
                                    ) : (
                                        <Table bare scroll>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHeaderCell>
                                                        {t(
                                                            'page.billing_page.invoice_number',
                                                        )}
                                                    </TableHeaderCell>
                                                    <TableHeaderCell>
                                                        {t(
                                                            'page.billing_page.invoice_period',
                                                        )}
                                                    </TableHeaderCell>
                                                    <TableHeaderCell numeric>
                                                        {t(
                                                            'page.billing_page.invoice_total',
                                                        )}
                                                    </TableHeaderCell>
                                                    <TableHeaderCell>
                                                        {t(
                                                            'page.billing_page.invoice_status',
                                                        )}
                                                    </TableHeaderCell>
                                                    <TableHeaderCell>
                                                        {t(
                                                            'page.billing_page.invoice_paid_at',
                                                        )}
                                                    </TableHeaderCell>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {invoices.map((invoice) => (
                                                    <TableRow key={invoice.id}>
                                                        <TableCell className="font-mono text-[12.5px]">
                                                            {invoice.number}
                                                        </TableCell>
                                                        <TableCell muted>
                                                            {formatDate(
                                                                invoice.period_start,
                                                            )}
                                                            {invoice.period_end &&
                                                                ` → ${formatDate(invoice.period_end)}`}
                                                        </TableCell>
                                                        <TableCell numeric>
                                                            {formatMoney(
                                                                invoice.total,
                                                                invoice.currency,
                                                            )}
                                                        </TableCell>
                                                        <TableCell>
                                                            <Badge
                                                                tone={
                                                                    INVOICE_TONES[
                                                                        invoice
                                                                            .status
                                                                    ] ??
                                                                    'neutral'
                                                                }
                                                                dot
                                                            >
                                                                {humanise(
                                                                    invoice.status,
                                                                )}
                                                            </Badge>
                                                        </TableCell>
                                                        <TableCell muted>
                                                            {formatDate(
                                                                invoice.paid_at,
                                                            )}
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    )}
                                </SectionCard>
                            </div>

                            <div className="flex flex-col gap-6">
                                <SectionCard
                                    title={t(
                                        'page.billing_page.features_title',
                                    )}
                                    description={t(
                                        'page.billing_page.features_description',
                                    )}
                                    icon={Squares2X2Icon}
                                    actions={
                                        <Badge tone="accent" variant="outline">
                                            {enabledCount}/
                                            {featureEntries.length}
                                        </Badge>
                                    }
                                >
                                    {featureEntries.length === 0 ? (
                                        <p className="text-[13px] text-ink-subtle">
                                            {t('page.billing_page.no_features')}
                                        </p>
                                    ) : (
                                        <ul className="grid grid-cols-1 gap-1.5">
                                            {featureEntries.map(
                                                ([key, enabled]) => {
                                                    const Icon =
                                                        FEATURE_ICONS[key] ??
                                                        Squares2X2Icon;

                                                    return (
                                                        <li
                                                            key={key}
                                                            className={`flex items-center gap-2.5 rounded-md px-2 py-1.5 ${
                                                                enabled
                                                                    ? 'bg-surface-muted/60'
                                                                    : 'opacity-55'
                                                            }`}
                                                        >
                                                            <span
                                                                className={`flex h-6 w-6 shrink-0 items-center justify-center rounded-[7px] ${
                                                                    enabled
                                                                        ? 'bg-positive-soft text-positive-ink'
                                                                        : 'bg-surface-inset text-ink-subtle'
                                                                }`}
                                                            >
                                                                {enabled ? (
                                                                    <CheckCircleIcon className="h-3.5 w-3.5" />
                                                                ) : (
                                                                    <Icon className="h-3.5 w-3.5" />
                                                                )}
                                                            </span>
                                                            <span
                                                                className={`min-w-0 flex-1 truncate text-[12.5px] ${
                                                                    enabled
                                                                        ? 'font-medium text-ink'
                                                                        : 'text-ink-subtle'
                                                                }`}
                                                            >
                                                                {t(
                                                                    `page.billing_page.features.${key}`,
                                                                    humanise(
                                                                        key,
                                                                    ),
                                                                )}
                                                            </span>
                                                            <span className="shrink-0 text-[10px] tracking-[0.1em] text-ink-subtle uppercase">
                                                                {enabled
                                                                    ? t(
                                                                          'page.billing_page.features_enabled',
                                                                      )
                                                                    : t(
                                                                          'page.billing_page.features_disabled',
                                                                      )}
                                                            </span>
                                                        </li>
                                                    );
                                                },
                                            )}
                                        </ul>
                                    )}
                                </SectionCard>

                                <Card>
                                    <CardHeader>
                                        <CardTitle>
                                            {t(
                                                'page.billing_page.contact_support_title',
                                            )}
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="flex flex-col gap-3">
                                        <p className="text-[13px] leading-relaxed text-ink-muted">
                                            {t(
                                                'page.billing_page.contact_support_desc',
                                            )}
                                        </p>
                                        <Button
                                            variant="secondary"
                                            icon={LifebuoyIcon}
                                            trailingIcon={
                                                ArrowTopRightOnSquareIcon
                                            }
                                            href={route(
                                                'support-tickets.index',
                                            )}
                                            fullWidth
                                        >
                                            {t(
                                                'page.billing_page.contact_support_cta',
                                            )}
                                        </Button>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
