import { CheckIcon, MinusIcon } from '@heroicons/react/24/outline';
import { Fragment } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';
import type { MarketingCapability, MarketingPlan } from '@/types/marketing';

interface CapabilityMatrixProps {
    plans: MarketingPlan[];
    capabilities: MarketingCapability[];
    className?: string;
}

/**
 * CapabilityMatrix — the honest comparison.
 *
 * Rows are the platform's real capability keys (`App\Enums\PlanFeature`), and
 * each cell is the plan's own `feature_flags_json`. If an operator flips a
 * module on for a tier in the console, this table changes on the next request.
 * No hand-maintained marketing grid to drift out of sync.
 *
 * Rows are grouped by the capability's own category hint; the first column is
 * pinned on narrow screens so the reader never loses the row label.
 */
export function CapabilityMatrix({
    plans,
    capabilities,
    className,
}: CapabilityMatrixProps) {
    const { t } = useTranslation();

    if (plans.length === 0 || capabilities.length === 0) {
        return null;
    }

    const groups = new Map<string, MarketingCapability[]>();

    capabilities.forEach((capability) => {
        const group = capability.description || 'Module access.';
        const bucket = groups.get(group) ?? [];
        bucket.push(capability);
        groups.set(group, bucket);
    });

    return (
        <div
            className={cn(
                'overflow-x-auto rounded-xl border border-line bg-surface',
                className,
            )}
        >
            <table className="w-full min-w-[640px] border-collapse text-left">
                <caption className="sr-only">
                    {t(
                        'mk.matrix.caption',
                        'Which modules each published plan unlocks',
                    )}
                </caption>
                <thead>
                    <tr className="border-b border-line bg-surface-muted">
                        <th
                            scope="col"
                            className="sticky left-0 z-10 w-[46%] bg-surface-muted px-4 py-3 text-left font-mono text-[9.5px] font-medium tracking-[0.16em] text-ink-subtle uppercase"
                        >
                            {t('mk.matrix.module', 'Module')}
                        </th>
                        {plans.map((plan) => (
                            <th
                                key={plan.id}
                                scope="col"
                                className="px-4 py-3 text-center font-mono text-[9.5px] font-medium tracking-[0.14em] text-ink-subtle uppercase"
                            >
                                <span className="block text-ink">
                                    {plan.name}
                                </span>
                                {plan.is_featured && (
                                    <span className="mt-0.5 block text-[8.5px] text-accent-ink">
                                        {t(
                                            'mk.pricing.recommended',
                                            'Recommended',
                                        )}
                                    </span>
                                )}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {[...groups.entries()].map(([group, rows]) => (
                        <Fragment key={group}>
                            <tr className="bg-surface">
                                <th
                                    scope="colgroup"
                                    colSpan={plans.length + 1}
                                    className="sticky left-0 border-y border-line bg-surface-muted px-4 py-1.5 text-left font-mono text-[9px] font-medium tracking-[0.16em] text-ink-subtle uppercase"
                                >
                                    {group}
                                </th>
                            </tr>
                            {rows.map((capability) => (
                                <tr
                                    key={capability.key}
                                    className="border-b border-line transition-colors duration-150 last:border-b-0 hover:bg-surface-hover"
                                >
                                    <th
                                        scope="row"
                                        className="sticky left-0 z-10 bg-surface px-4 py-2.5 text-left text-[12.5px] font-normal text-ink"
                                    >
                                        {capability.label}
                                    </th>
                                    {plans.map((plan) => {
                                        const enabled =
                                            plan.capabilities[
                                                capability.key
                                            ] === true;

                                        return (
                                            <td
                                                key={plan.id}
                                                className="px-4 py-2.5 text-center"
                                            >
                                                <span className="sr-only">
                                                    {enabled
                                                        ? t(
                                                              'mk.matrix.included',
                                                              'Included',
                                                          )
                                                        : t(
                                                              'mk.matrix.excluded',
                                                              'Not included',
                                                          )}
                                                </span>
                                                {enabled ? (
                                                    <CheckIcon
                                                        aria-hidden
                                                        className="mx-auto h-3.5 w-3.5 text-accent"
                                                        strokeWidth={2.5}
                                                    />
                                                ) : (
                                                    <MinusIcon
                                                        aria-hidden
                                                        className="mx-auto h-3.5 w-3.5 text-ink-subtle/45"
                                                    />
                                                )}
                                            </td>
                                        );
                                    })}
                                </tr>
                            ))}
                        </Fragment>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
