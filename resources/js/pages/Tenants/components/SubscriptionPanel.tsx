import {
    ArrowPathIcon,
    ArrowTrendingUpIcon,
    ArrowsRightLeftIcon,
    CalendarDaysIcon,
    CheckIcon,
    CreditCardIcon,
    PauseCircleIcon,
    PlayCircleIcon,
    PlusCircleIcon,
    StopCircleIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import type { FormDataConvertible } from '@inertiajs/core';
import { router } from '@inertiajs/react';
import { useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Checkbox,
    Dialog,
    Field,
    Input,
    Select,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { formatCurrency, formatDate } from '@/pages/Platform/components/format';
import type {
    PlanOption,
    ProrationPreview,
    TenantSubscription,
} from '@/pages/Platform/components/types';
import { SubscriptionStatusBadge } from '@/pages/Platform/components/UsageMeter';

interface SubscriptionPanelProps {
    tenantId: string;
    tenantName: string;
    subscription: TenantSubscription | null;
    plans: PlanOption[];
    canManage: boolean;
}

type ActionDialog = 'assign' | 'change-plan' | 'extend' | 'cancel' | 'expire';

/**
 * The subscription control surface for one hospital.
 *
 * Every action posts to a `tenants.subscription.*` route. The change-plan flow
 * first asks `tenants.subscription.preview` (a JSON endpoint) so the operator
 * confirms against a real proration figure instead of a guess.
 */
export function SubscriptionPanel({
    tenantId,
    tenantName,
    subscription,
    plans,
    canManage,
}: SubscriptionPanelProps) {
    const [dialog, setDialog] = useState<ActionDialog | null>(null);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    // Assign / change-plan
    const [planId, setPlanId] = useState(
        String(subscription?.plan_id ?? plans[0]?.id ?? ''),
    );
    const [cycle, setCycle] = useState(
        subscription?.billing_cycle === 'yearly' ? 'yearly' : 'monthly',
    );
    const [price, setPrice] = useState('');
    const [trial, setTrial] = useState(false);
    const [immediate, setImmediate] = useState(true);
    const [notes, setNotes] = useState('');

    // Extend / cancel
    const [days, setDays] = useState('30');
    const [cancelImmediately, setCancelImmediately] = useState(false);
    const [cancelReason, setCancelReason] = useState('');

    // Proration preview
    const [preview, setPreview] = useState<ProrationPreview | null>(null);
    const [previewLoading, setPreviewLoading] = useState(false);
    const [previewError, setPreviewError] = useState<string | null>(null);

    const selectedPlan = plans.find((plan) => String(plan.id) === planId);
    const currency = subscription?.currency ?? 'XAF';

    const open = (kind: ActionDialog) => {
        setErrors({});
        setPreview(null);
        setPreviewError(null);

        if (kind === 'change-plan' && subscription) {
            setPlanId(String(subscription.plan_id ?? ''));
            setCycle(subscription.billing_cycle);
        }

        if (kind === 'assign') {
            setPlanId(String(plans[0]?.id ?? ''));
            setCycle('monthly');
            setPrice('');
            setTrial(false);
            setNotes('');
        }

        setDialog(kind);
    };

    const close = () => {
        setDialog(null);
        setErrors({});
        setPreview(null);
        setPreviewError(null);
    };

    const post = (
        url: string,
        payload: Record<string, FormDataConvertible>,
        closeOnSuccess = true,
    ) => {
        setProcessing(true);
        setErrors({});

        router.post(url, payload, {
            preserveScroll: true,
            onSuccess: () => {
                if (closeOnSuccess) {
                    close();
                }
            },
            onError: (backendErrors: Record<string, string>) =>
                setErrors(backendErrors),
            onFinish: () => setProcessing(false),
        });
    };

    const loadPreview = async () => {
        if (planId === '') {
            return;
        }

        setPreviewLoading(true);
        setPreviewError(null);

        try {
            const response = await fetch(
                `/tenants/${tenantId}/subscription/preview?` +
                    new URLSearchParams({
                        plan_id: planId,
                        billing_cycle: cycle,
                    }).toString(),
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                },
            );

            if (!response.ok) {
                throw new Error(`Preview failed (HTTP ${response.status})`);
            }

            setPreview((await response.json()) as ProrationPreview);
        } catch (error) {
            setPreviewError(
                error instanceof Error
                    ? error.message
                    : 'Could not load the proration preview.',
            );
        } finally {
            setPreviewLoading(false);
        }
    };

    const simplePost = (action: 'renew' | 'resume' | 'expire') =>
        post(`/tenants/${tenantId}/subscription/${action}`, {});

    // ── No subscription yet ──
    if (!subscription) {
        return (
            <>
                <SectionCard
                    title="Subscription"
                    description="No plan assigned — this hospital cannot use quota-gated modules."
                    icon={CreditCardIcon}
                >
                    <div className="space-y-4">
                        <Alert tone="warning" title="No subscription assigned">
                            Assign a plan to start billing. A custom price and a
                            trial are both supported.
                        </Alert>

                        {plans.length === 0 ? (
                            <p className="text-[13px] text-ink-muted">
                                No plans exist yet.{' '}
                                <a
                                    href="/plans/create"
                                    className="font-medium text-accent-ink underline-offset-2 hover:underline"
                                >
                                    Create a plan
                                </a>{' '}
                                before assigning one.
                            </p>
                        ) : (
                            canManage && (
                                <Button
                                    icon={PlusCircleIcon}
                                    onClick={() => open('assign')}
                                >
                                    Assign a plan
                                </Button>
                            )
                        )}
                    </div>
                </SectionCard>

                <Dialog
                    open={dialog === 'assign'}
                    onClose={processing ? () => undefined : close}
                    title={`Assign a plan to ${tenantName}`}
                    description="Starts a subscription period and, unless you start on a trial, raises the first invoice."
                    size="lg"
                    footer={
                        <>
                            <Button
                                variant="secondary"
                                onClick={close}
                                disabled={processing}
                            >
                                Cancel
                            </Button>
                            <Button
                                variant="primary"
                                icon={PlusCircleIcon}
                                loading={processing}
                                onClick={() =>
                                    post(`/tenants/${tenantId}/subscription`, {
                                        plan_id: planId,
                                        billing_cycle: cycle,
                                        trial,
                                        price: price === '' ? null : price,
                                        notes: notes === '' ? null : notes,
                                    })
                                }
                            >
                                Assign plan
                            </Button>
                        </>
                    }
                >
                    <div className="grid gap-4 sm:grid-cols-2">
                        <Field
                            label="Plan"
                            htmlFor="assign-plan"
                            required
                            error={errors.plan_id}
                        >
                            <Select
                                id="assign-plan"
                                value={planId}
                                onChange={(event) =>
                                    setPlanId(event.target.value)
                                }
                            >
                                {plans.map((plan) => (
                                    <option key={plan.id} value={plan.id}>
                                        {plan.name}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label="Billing cycle"
                            htmlFor="assign-cycle"
                            error={errors.billing_cycle}
                        >
                            <Select
                                id="assign-cycle"
                                value={cycle}
                                onChange={(event) =>
                                    setCycle(event.target.value)
                                }
                            >
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </Select>
                        </Field>

                        <Field
                            label="Custom price (optional)"
                            htmlFor="assign-price"
                            error={errors.price}
                            hint={
                                selectedPlan
                                    ? `Leave empty for the list price: ${formatCurrency(
                                          cycle === 'yearly'
                                              ? selectedPlan.yearly_price
                                              : selectedPlan.monthly_price,
                                          selectedPlan.currency,
                                      )}`
                                    : undefined
                            }
                        >
                            <Input
                                id="assign-price"
                                type="number"
                                min="0"
                                step="0.01"
                                value={price}
                                placeholder="List price"
                                onChange={(event) =>
                                    setPrice(event.target.value)
                                }
                                invalid={Boolean(errors.price)}
                            />
                        </Field>

                        <div className="flex items-start pt-5">
                            <Checkbox
                                checked={trial}
                                onChange={(event) =>
                                    setTrial(event.target.checked)
                                }
                                label="Start on a trial"
                                description="No invoice until the trial ends."
                            />
                        </div>

                        <Field
                            label="Internal note"
                            htmlFor="assign-notes"
                            error={errors.notes}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                id="assign-notes"
                                rows={2}
                                value={notes}
                                placeholder="Negotiated discount, pilot agreement…"
                                onChange={(event) =>
                                    setNotes(event.target.value)
                                }
                                maxLength={2000}
                            />
                        </Field>
                    </div>
                </Dialog>
            </>
        );
    }

    // ── Existing subscription ──
    return (
        <>
            <SectionCard
                title="Subscription"
                description={`${subscription.plan_name} · billed ${subscription.billing_cycle}`}
                icon={CreditCardIcon}
                actions={
                    <SubscriptionStatusBadge
                        label={subscription.status_label}
                        tone={subscription.status_tone}
                        daysRemaining={subscription.days_remaining}
                    />
                }
            >
                <div className="space-y-5">
                    <dl className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <Figure
                            label="Price"
                            value={formatCurrency(
                                subscription.price,
                                subscription.currency,
                            )}
                            hint={`per ${subscription.billing_cycle === 'yearly' ? 'year' : 'month'}`}
                            icon={CreditCardIcon}
                        />
                        <Figure
                            label="Current period"
                            value={`${formatDate(subscription.start_date)} → ${formatDate(subscription.end_date)}`}
                            hint={
                                subscription.days_remaining === null
                                    ? 'No end date'
                                    : subscription.days_remaining >= 0
                                      ? `${subscription.days_remaining} days remaining`
                                      : `${Math.abs(subscription.days_remaining)} days overdue`
                            }
                            icon={CalendarDaysIcon}
                        />
                        <Figure
                            label="Auto renew"
                            value={subscription.auto_renew ? 'On' : 'Off'}
                            hint={
                                subscription.cancel_at_period_end
                                    ? 'Cancels at period end'
                                    : 'Runs until cancelled'
                            }
                            icon={ArrowPathIcon}
                        />
                        <Figure
                            label="Features"
                            value={`${
                                Object.values(subscription.features).filter(
                                    Boolean,
                                ).length
                            } enabled`}
                            hint={
                                subscription.plan
                                    ? `${subscription.plan.name} plan`
                                    : 'Plan record missing'
                            }
                            icon={CheckIcon}
                        />
                    </dl>

                    {subscription.cancel_at_period_end && (
                        <Alert tone="warning" title="Ends at period end">
                            This subscription will not renew — the hospital
                            loses access on {formatDate(subscription.end_date)}{' '}
                            unless you resume it.
                        </Alert>
                    )}

                    {subscription.notes && (
                        <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3">
                            <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Note on this subscription
                            </p>
                            <p className="mt-1 text-[12.5px] leading-relaxed text-ink-muted">
                                {subscription.notes}
                            </p>
                        </div>
                    )}

                    {canManage && (
                        <div className="flex flex-wrap gap-2 border-t border-line pt-4">
                            <Button
                                variant="secondary"
                                size="sm"
                                icon={ArrowsRightLeftIcon}
                                onClick={() => open('change-plan')}
                            >
                                Change plan
                            </Button>
                            <Button
                                variant="secondary"
                                size="sm"
                                icon={ArrowPathIcon}
                                loading={processing}
                                onClick={() => simplePost('renew')}
                            >
                                Renew
                            </Button>
                            <Button
                                variant="secondary"
                                size="sm"
                                icon={CalendarDaysIcon}
                                onClick={() => open('extend')}
                            >
                                Extend
                            </Button>
                            {subscription.cancel_at_period_end ? (
                                <Button
                                    variant="secondary"
                                    size="sm"
                                    icon={PlayCircleIcon}
                                    loading={processing}
                                    onClick={() => simplePost('resume')}
                                >
                                    Resume
                                </Button>
                            ) : (
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    icon={StopCircleIcon}
                                    onClick={() => open('cancel')}
                                >
                                    Cancel
                                </Button>
                            )}
                            <Button
                                variant="ghost"
                                size="sm"
                                icon={XCircleIcon}
                                onClick={() => open('expire')}
                            >
                                Expire now
                            </Button>
                        </div>
                    )}
                </div>
            </SectionCard>

            {/* ── Change plan + proration preview ── */}
            <Dialog
                open={dialog === 'change-plan'}
                onClose={processing ? () => undefined : close}
                title={`Change the plan for ${tenantName}`}
                description="Load the proration preview before confirming — it shows what the remaining period is worth."
                size="lg"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={close}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="primary"
                            loading={processing}
                            onClick={() =>
                                post(`/tenants/${tenantId}/subscription/plan`, {
                                    plan_id: planId,
                                    billing_cycle: cycle,
                                    immediate,
                                })
                            }
                        >
                            Confirm change
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <div className="grid gap-4 sm:grid-cols-2">
                        <Field
                            label="New plan"
                            htmlFor="change-plan"
                            required
                            error={errors.plan_id}
                        >
                            <Select
                                id="change-plan"
                                value={planId}
                                onChange={(event) => {
                                    setPlanId(event.target.value);
                                    setPreview(null);
                                }}
                            >
                                {plans.map((plan) => (
                                    <option key={plan.id} value={plan.id}>
                                        {plan.name}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label="Billing cycle"
                            htmlFor="change-cycle"
                            error={errors.billing_cycle}
                        >
                            <Select
                                id="change-cycle"
                                value={cycle}
                                onChange={(event) => {
                                    setCycle(event.target.value);
                                    setPreview(null);
                                }}
                            >
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </Select>
                        </Field>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <Button
                            variant="secondary"
                            size="sm"
                            icon={ArrowTrendingUpIcon}
                            loading={previewLoading}
                            onClick={loadPreview}
                        >
                            {previewLoading
                                ? 'Calculating…'
                                : 'Load proration preview'}
                        </Button>
                        {selectedPlan && (
                            <span className="text-[12px] text-ink-subtle">
                                List price:{' '}
                                {formatCurrency(
                                    cycle === 'yearly'
                                        ? selectedPlan.yearly_price
                                        : selectedPlan.monthly_price,
                                    selectedPlan.currency,
                                )}
                            </span>
                        )}
                    </div>

                    {previewError && (
                        <Alert tone="critical" title="Preview unavailable">
                            {previewError}
                        </Alert>
                    )}

                    {preview && (
                        <div className="rounded-lg border border-accent/25 bg-accent-soft px-3.5 py-3">
                            <div className="flex flex-wrap items-center gap-2">
                                <p className="text-[10.5px] font-semibold tracking-[0.1em] text-accent-ink uppercase">
                                    Proration preview
                                </p>
                                <Badge tone="accent" variant="outline">
                                    {preview.direction}
                                </Badge>
                            </div>
                            <dl className="mt-2 grid gap-3 sm:grid-cols-3">
                                <div>
                                    <dt className="text-[11px] text-ink-subtle">
                                        Amount due
                                    </dt>
                                    <dd className="text-[15px] font-semibold text-ink tabular-nums">
                                        {formatCurrency(
                                            preview.amount_due,
                                            currency,
                                        )}
                                    </dd>
                                </div>
                                {preview.credit !== undefined && (
                                    <div>
                                        <dt className="text-[11px] text-ink-subtle">
                                            Unused-time credit
                                        </dt>
                                        <dd className="text-[13px] font-medium text-ink-muted tabular-nums">
                                            {formatCurrency(
                                                preview.credit,
                                                currency,
                                            )}
                                        </dd>
                                    </div>
                                )}
                                {preview.days_remaining !== null && (
                                    <div>
                                        <dt className="text-[11px] text-ink-subtle">
                                            Days remaining
                                        </dt>
                                        <dd className="text-[13px] font-medium text-ink-muted tabular-nums">
                                            {preview.days_remaining}
                                            {preview.total_days
                                                ? ` / ${preview.total_days}`
                                                : ''}
                                        </dd>
                                    </div>
                                )}
                            </dl>
                        </div>
                    )}

                    <Checkbox
                        checked={immediate}
                        onChange={(event) => setImmediate(event.target.checked)}
                        label="Apply immediately"
                        description="Uncheck to schedule the change at the end of the current period."
                    />
                </div>
            </Dialog>

            {/* ── Extend ── */}
            <Dialog
                open={dialog === 'extend'}
                onClose={processing ? () => undefined : close}
                title={`Extend ${tenantName}'s subscription`}
                description="Adds days to the current period without raising an invoice."
                size="sm"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={close}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="primary"
                            loading={processing}
                            onClick={() =>
                                post(
                                    `/tenants/${tenantId}/subscription/extend`,
                                    { days },
                                )
                            }
                        >
                            Extend
                        </Button>
                    </>
                }
            >
                <Field
                    label="Days to add"
                    htmlFor="extend-days"
                    required
                    error={errors.days}
                    hint="Between 1 and 365 days."
                >
                    <Input
                        id="extend-days"
                        type="number"
                        min="1"
                        max="365"
                        value={days}
                        onChange={(event) => setDays(event.target.value)}
                        invalid={Boolean(errors.days)}
                    />
                </Field>
            </Dialog>

            {/* ── Cancel ── */}
            <Dialog
                open={dialog === 'cancel'}
                onClose={processing ? () => undefined : close}
                title={`Cancel ${tenantName}'s subscription`}
                description="Cancelling at period end keeps access until the paid period is over."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={close}
                            disabled={processing}
                        >
                            Keep subscription
                        </Button>
                        <Button
                            variant="danger"
                            icon={PauseCircleIcon}
                            loading={processing}
                            onClick={() =>
                                post(
                                    `/tenants/${tenantId}/subscription/cancel`,
                                    {
                                        immediately: cancelImmediately,
                                        reason:
                                            cancelReason === ''
                                                ? null
                                                : cancelReason,
                                    },
                                )
                            }
                        >
                            Cancel subscription
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <Checkbox
                        checked={cancelImmediately}
                        onChange={(event) =>
                            setCancelImmediately(event.target.checked)
                        }
                        label="Cancel immediately"
                        description="Cuts access now instead of at the end of the paid period."
                    />

                    <Field
                        label="Reason (optional)"
                        htmlFor="cancel-reason"
                        error={errors.reason}
                    >
                        <Textarea
                            id="cancel-reason"
                            rows={2}
                            value={cancelReason}
                            placeholder="Hospital closed, moved to another vendor…"
                            onChange={(event) =>
                                setCancelReason(event.target.value)
                            }
                            maxLength={500}
                        />
                    </Field>
                </div>
            </Dialog>

            {/* ── Expire (dangerous) ── */}
            <Dialog
                open={dialog === 'expire'}
                onClose={processing ? () => undefined : close}
                title={`Expire ${tenantName}'s subscription now?`}
                description="Ends the subscription immediately; nothing is refunded and the hospital loses access."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={close}
                            disabled={processing}
                        >
                            Keep subscription
                        </Button>
                        <Button
                            variant="danger"
                            icon={XCircleIcon}
                            loading={processing}
                            onClick={() => simplePost('expire')}
                        >
                            Expire now
                        </Button>
                    </>
                }
            >
                <Alert tone="critical" title="This bypasses the paid period">
                    Prefer cancelling at period end unless the contract has to
                    stop today. The action is recorded in the platform audit
                    log.
                </Alert>
            </Dialog>
        </>
    );
}

/** Compact label / value / hint tile used by the subscription summary. */
function Figure({
    label,
    value,
    hint,
    icon: Icon,
}: {
    label: string;
    value: string;
    hint?: string;
    icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
}) {
    return (
        <div className="min-w-0 rounded-lg border border-line bg-surface-muted px-3.5 py-3">
            <dt className="flex items-center gap-1.5 text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                <Icon className="h-3.5 w-3.5 shrink-0" />
                {label}
            </dt>
            <dd
                className="mt-1 truncate text-[13.5px] font-semibold text-ink tabular-nums"
                title={value}
            >
                {value}
            </dd>
            {hint && (
                <p className="mt-0.5 truncate text-[11.5px] text-ink-subtle">
                    {hint}
                </p>
            )}
        </div>
    );
}
