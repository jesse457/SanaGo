import {
    AdjustmentsHorizontalIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useMemo } from 'react';
import {
    Alert,
    Badge,
    Button,
    Checkbox,
    Field,
    Input,
    SectionCard,
} from '@/Components/ui';
import { cn } from '@/lib/utils';
import { humaniseKey } from '@/pages/Platform/components/format';
import type {
    TenantSubscription,
    TenantUsage,
} from '@/pages/Platform/components/types';
import { UsageMeter } from '@/pages/Platform/components/UsageMeter';

interface LimitsPanelProps {
    tenantId: string;
    subscription: TenantSubscription;
    usage: TenantUsage;
    canManage: boolean;
}

interface OverridesForm {
    limits: Record<string, string>;
    features: Record<string, boolean>;
}

/** Quotas the backend accepts in `limits[…]` (see the overrides validation). */
const LIMIT_KEYS = [
    { key: 'users', label: 'User seats', unit: 'users', usageKey: 'users' },
    {
        key: 'patients',
        label: 'Patient records',
        unit: 'patients',
        usageKey: 'patients',
    },
    {
        key: 'storage_gb',
        label: 'Storage',
        unit: 'GB',
        usageKey: 'storage_gb',
    },
] as const;

/**
 * Limits & features
 * -----------------
 * Left column: what the hospital actually has (effective limits vs live
 * usage). Right column: per-tenant overrides, editable through
 * `PUT tenants.subscription.overrides`.
 *
 * Effective `limits` already merge the plan defaults with `limits_json`; the
 * form starts from the *overrides* so clearing a box restores the plan this
 * hospital is on rather than inventing a quota.
 */
export function LimitsPanel({
    tenantId,
    subscription,
    usage,
    canManage,
}: LimitsPanelProps) {
    // Feature keys come from the plan's flags — that is the authoritative set.
    const featureKeys = useMemo(() => {
        const keys = new Set<string>([
            ...Object.keys(subscription.plan?.features ?? {}),
            ...Object.keys(subscription.features ?? {}),
        ]);

        return Array.from(keys).sort();
    }, [subscription.plan?.features, subscription.features]);

    const { data, setData, put, processing, errors, isDirty, reset } =
        useForm<OverridesForm>({
            limits: {
                users:
                    subscription.limit_overrides?.users !== null &&
                    subscription.limit_overrides?.users !== undefined
                        ? String(subscription.limit_overrides.users)
                        : '',
                patients:
                    subscription.limit_overrides?.patients !== null &&
                    subscription.limit_overrides?.patients !== undefined
                        ? String(subscription.limit_overrides.patients)
                        : '',
                storage_gb:
                    subscription.limit_overrides?.storage_gb !== null &&
                    subscription.limit_overrides?.storage_gb !== undefined
                        ? String(subscription.limit_overrides.storage_gb)
                        : '',
            },
            features: { ...(subscription.features ?? {}) },
        });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        const limits: Record<string, number> = {};

        for (const key of Object.keys(data.limits)) {
            const raw = data.limits[key];

            if (raw !== '' && !Number.isNaN(Number(raw))) {
                limits[key] = Number(raw);
            }
        }

        put(`/tenants/${tenantId}/subscription/overrides`, {
            preserveScroll: true,
        });
    };

    const overriddenLimitCount = Object.values(
        subscription.limit_overrides ?? {},
    ).filter((value) => value !== null && value !== undefined).length;

    return (
        <SectionCard
            title="Limits & features"
            description="Effective quotas against live usage, plus per-hospital overrides on top of the plan."
            icon={AdjustmentsHorizontalIcon}
            actions={
                overriddenLimitCount > 0 ? (
                    <Badge tone="accent">
                        {overriddenLimitCount} quota override
                        {overriddenLimitCount === 1 ? '' : 's'}
                    </Badge>
                ) : null
            }
        >
            <div className="grid gap-6 lg:grid-cols-2">
                {/* ── Effective usage ── */}
                <div className="space-y-4">
                    <p className="text-[10.5px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                        Effective usage
                    </p>

                    <dl className="space-y-3.5">
                        {LIMIT_KEYS.map((limit) => (
                            <UsageMeter
                                key={limit.key}
                                label={limit.label}
                                used={usage[limit.usageKey] ?? 0}
                                limit={subscription.limits?.[limit.key] ?? null}
                                unit={limit.unit}
                            />
                        ))}
                    </dl>

                    <div className="grid grid-cols-2 gap-3 border-t border-line pt-3.5">
                        <div>
                            <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Appointments
                            </dt>
                            <dd className="text-[13px] font-medium text-ink tabular-nums">
                                {usage.appointments}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Invoices
                            </dt>
                            <dd className="text-[13px] font-medium text-ink tabular-nums">
                                {usage.invoices}
                            </dd>
                        </div>
                    </div>
                </div>

                {/* ── Overrides ── */}
                <form onSubmit={submit} className="space-y-4">
                    <p className="text-[10.5px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                        Overrides
                    </p>

                    {!canManage && (
                        <Alert tone="info" title="Read-only">
                            You need the `manage-subscriptions` permission to
                            change quotas and features.
                        </Alert>
                    )}

                    <div className="grid gap-3 sm:grid-cols-3">
                        {LIMIT_KEYS.map((limit) => (
                            <Field
                                key={limit.key}
                                label={limit.label}
                                htmlFor={`limit-${limit.key}`}
                                error={errors[`limits.${limit.key}`]}
                                hint={
                                    subscription.limits?.[limit.key] === null
                                        ? 'Unlimited'
                                        : `Plan: ${
                                              subscription.limits?.[
                                                  limit.key
                                              ] ?? '—'
                                          } ${limit.unit}`
                                }
                            >
                                <Input
                                    id={`limit-${limit.key}`}
                                    type="number"
                                    min="1"
                                    inputMode="numeric"
                                    value={data.limits[limit.key] ?? ''}
                                    placeholder="Plan default"
                                    disabled={!canManage}
                                    onChange={(event) =>
                                        setData('limits', {
                                            ...data.limits,
                                            [limit.key]: event.target.value,
                                        })
                                    }
                                    invalid={Boolean(
                                        errors[`limits.${limit.key}`],
                                    )}
                                    className="tabular-nums"
                                />
                            </Field>
                        ))}
                    </div>

                    <div className="space-y-2">
                        <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            Features
                        </p>

                        {featureKeys.length === 0 ? (
                            <p className="rounded-lg border border-dashed border-line bg-surface-muted px-3.5 py-3 text-[12.5px] text-ink-subtle">
                                This subscription's plan defines no feature
                                flags, so there is nothing to override.
                            </p>
                        ) : (
                            <div className="grid max-h-64 gap-x-4 gap-y-2 overflow-y-auto rounded-lg border border-line bg-surface-muted p-3 sm:grid-cols-2">
                                {featureKeys.map((key) => {
                                    const overridden =
                                        subscription.feature_overrides?.[
                                            key
                                        ] !== undefined;

                                    return (
                                        <div
                                            key={key}
                                            className="flex items-start gap-2"
                                        >
                                            <Checkbox
                                                id={`feature-${key}`}
                                                checked={
                                                    data.features[key] ?? false
                                                }
                                                disabled={!canManage}
                                                onChange={(event) =>
                                                    setData('features', {
                                                        ...data.features,
                                                        [key]: event.target
                                                            .checked,
                                                    })
                                                }
                                                label={
                                                    <span className="flex items-center gap-1.5">
                                                        {humaniseKey(key)}
                                                        {overridden && (
                                                            <span
                                                                className="h-1.5 w-1.5 rounded-full bg-accent"
                                                                title="Overridden for this hospital"
                                                            />
                                                        )}
                                                    </span>
                                                }
                                            />
                                        </div>
                                    );
                                })}
                            </div>
                        )}

                        <p className="text-[11.5px] leading-relaxed text-ink-subtle">
                            A dot marks a feature overridden for this hospital.
                            Toggling writes the full effective set — the plan's
                            flags are the baseline.
                        </p>
                    </div>

                    {Object.keys(errors).length > 0 && (
                        <Alert tone="critical" title="Could not save">
                            {Object.values(errors).join(' ')}
                        </Alert>
                    )}

                    {canManage && (
                        <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-3">
                            <Button
                                variant="ghost"
                                size="sm"
                                icon={ArrowPathIcon}
                                disabled={!isDirty || processing}
                                onClick={() => reset()}
                            >
                                Reset
                            </Button>
                            <Button
                                type="submit"
                                size="sm"
                                loading={processing}
                                disabled={!isDirty}
                            >
                                Save overrides
                            </Button>
                        </div>
                    )}
                </form>
            </div>

            {/* Feature summary: what the hospital can actually open today. */}
            <div className="mt-5 border-t border-line pt-4">
                <p className="mb-2 text-[10.5px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                    Enabled today
                </p>
                <div className="flex flex-wrap gap-1.5">
                    {featureKeys.length === 0 ? (
                        <span className="text-[12.5px] text-ink-subtle">
                            No feature flags defined by the plan.
                        </span>
                    ) : (
                        featureKeys.map((key) => {
                            const enabled = subscription.features?.[key];

                            return (
                                <Badge
                                    key={key}
                                    tone={enabled ? 'positive' : 'muted'}
                                    variant="outline"
                                    className={cn(
                                        !enabled && 'line-through opacity-60',
                                    )}
                                    dot
                                >
                                    <span className="flex items-center gap-1">
                                        {enabled ? (
                                            <CheckCircleIcon className="h-3 w-3" />
                                        ) : (
                                            <XCircleIcon className="h-3 w-3" />
                                        )}
                                        {humaniseKey(key)}
                                    </span>
                                </Badge>
                            );
                        })
                    )}
                </div>
            </div>
        </SectionCard>
    );
}
