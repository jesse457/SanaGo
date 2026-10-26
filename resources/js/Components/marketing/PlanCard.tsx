import { CheckIcon, MinusIcon } from '@heroicons/react/24/outline';
import Button from '@/Components/marketing/Button';
import { Badge } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { formatCurrency } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { BillingCycle, MarketingPlan } from '@/types/marketing';

interface PlanCardProps {
    plan: MarketingPlan;
    cycle: BillingCycle;
    currencySymbol: string;
    className?: string;
}

/**
 * PlanCard — one tier, priced from the console.
 *
 * The recommended plan is marked by an accent rule on its top edge and a soft
 * tint, never by being taller: the three tiers must still line up so a reader
 * can compare them across a row.
 */
export function PlanCard({
    plan,
    cycle,
    currencySymbol,
    className,
}: PlanCardProps) {
    const { t } = useTranslation();
    const isYearly = cycle === 'yearly';
    const price = isYearly ? plan.yearly_price : plan.monthly_price;
    const period = isYearly
        ? t('mk.pricing.per_year', '/ year')
        : t('mk.pricing.per_month', '/ month');
    const alternate = isYearly
        ? formatCurrency(plan.monthly_price, plan.currency, currencySymbol)
        : formatCurrency(plan.yearly_price, plan.currency, currencySymbol);

    const limits = [
        {
            label: t('mk.pricing.limit_users', 'Users'),
            value: plan.limits.users
                ? String(plan.limits.users)
                : t('mk.pricing.unlimited', 'Unlimited'),
        },
        {
            label: t('mk.pricing.limit_patients', 'Patient records'),
            value: plan.limits.patients
                ? new Intl.NumberFormat('fr-FR').format(plan.limits.patients)
                : t('mk.pricing.unlimited', 'Unlimited'),
        },
        {
            label: t('mk.pricing.limit_storage', 'Storage'),
            value: plan.limits.storage_gb
                ? `${plan.limits.storage_gb} GB`
                : t('mk.pricing.unlimited', 'Unlimited'),
        },
    ];

    return (
        <article
            className={cn(
                'relative flex h-full flex-col overflow-hidden rounded-xl border bg-surface',
                plan.is_featured
                    ? 'border-accent/35 shadow-surface'
                    : 'border-line',
                className,
            )}
        >
            {plan.is_featured && (
                <span
                    aria-hidden
                    className="absolute inset-x-0 top-0 h-[3px] bg-accent"
                />
            )}

            <div className="flex flex-1 flex-col p-6 sm:p-7">
                <div className="flex items-start justify-between gap-3">
                    <div>
                        <h3 className="text-[15px] font-semibold tracking-[-0.01em] text-ink">
                            {plan.name}
                        </h3>
                        {plan.is_featured && (
                            <p className="mt-1 font-mono text-[9.5px] tracking-[0.16em] text-accent-ink uppercase">
                                {t('mk.pricing.recommended', 'Recommended')}
                            </p>
                        )}
                    </div>
                    {plan.is_featured && (
                        <Badge tone="accent" variant="soft" dot>
                            {t('mk.pricing.active_badge', 'Most chosen')}
                        </Badge>
                    )}
                </div>

                <p className="mt-3 min-h-[42px] max-w-[34ch] text-[13px] leading-relaxed text-ink-muted">
                    {plan.description ??
                        t(
                            'mk.pricing.default_description',
                            'A complete hospital workspace for this tier.',
                        )}
                </p>

                <div className="mt-6 border-t border-line pt-5">
                    <div className="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                        <span
                            data-numeric
                            className="text-[clamp(1.55rem,2.5vw,1.95rem)] leading-none font-semibold tracking-[-0.035em] text-ink tabular-nums"
                        >
                            {formatCurrency(
                                price,
                                plan.currency,
                                currencySymbol,
                            )}
                        </span>
                        <span className="text-[11.5px] font-medium text-ink-subtle">
                            {period}
                        </span>
                    </div>
                    <p
                        data-numeric
                        className="mt-2 font-mono text-[10px] tracking-[0.06em] text-ink-subtle tabular-nums"
                    >
                        {isYearly
                            ? `${alternate} ${t('mk.pricing.per_month_short', 'monthly')}`
                            : `${alternate} ${t('mk.pricing.per_year_short', 'yearly')}`}
                    </p>
                </div>

                <dl className="mt-5 grid grid-cols-3 gap-px overflow-hidden rounded-md border border-line bg-line">
                    {limits.map((limit) => (
                        <div
                            key={limit.label}
                            className="bg-surface px-2.5 py-2.5"
                        >
                            <dt className="font-mono text-[8.5px] tracking-[0.12em] text-ink-subtle uppercase">
                                {limit.label}
                            </dt>
                            <dd
                                data-numeric
                                className="mt-1 truncate text-[11.5px] font-semibold text-ink tabular-nums"
                            >
                                {limit.value}
                            </dd>
                        </div>
                    ))}
                </dl>

                <ul className="mt-6 flex-1 space-y-2.5">
                    {plan.features.map((feature) => (
                        <li
                            key={feature.label}
                            className={cn(
                                'flex items-start gap-2 text-[12.5px] leading-snug',
                                feature.highlighted
                                    ? 'text-ink'
                                    : 'text-ink-muted',
                            )}
                        >
                            {feature.highlighted ? (
                                <CheckIcon className="mt-[1px] h-3.5 w-3.5 shrink-0 text-accent" />
                            ) : (
                                <MinusIcon className="mt-[1px] h-3.5 w-3.5 shrink-0 text-ink-subtle/60" />
                            )}
                            {feature.label}
                        </li>
                    ))}
                </ul>

                <div className="mt-7">
                    <Button
                        href="/contact"
                        variant={plan.is_featured ? 'primary' : 'secondary'}
                        className="w-full justify-center"
                    >
                        {plan.is_featured
                            ? t('mk.pricing.cta_featured', 'Start the trial')
                            : t('mk.pricing.cta', 'Talk to us')}
                    </Button>
                    <p className="mt-2.5 text-center font-mono text-[9px] tracking-[0.12em] text-ink-subtle uppercase">
                        {plan.trial_days}{' '}
                        {t('mk.pricing.trial_days', 'day trial included')}
                    </p>
                </div>
            </div>
        </article>
    );
}
