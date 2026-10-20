import {
    ArrowPathIcon,
    BanknotesIcon,
    BuildingOfficeIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ChatBubbleLeftEllipsisIcon,
    CreditCardIcon,
    DocumentTextIcon,
    GlobeAltIcon,
    KeyIcon,
    PaperAirplaneIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    EmptyState,
    Field,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Textarea,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { cn } from '@/lib/utils';
import {
    formatCurrency,
    formatDate,
    formatDateTime,
    humaniseKey,
} from '@/pages/Platform/components/format';
import type {
    SubscriptionDetail,
    TenantInvoice,
} from '@/pages/Platform/components/types';
import { SubscriptionStatusBadge } from '@/pages/Platform/components/UsageMeter';

interface SubscriptionsShowProps {
    subscription: SubscriptionDetail;
    invoices: TenantInvoice[];
}

export default function SubscriptionsShow({
    subscription,
    invoices,
}: SubscriptionsShowProps) {
    const { data, setData, put, processing, errors, isDirty } = useForm({
        notes: subscription.notes ?? '',
    });

    const saveNotes = (event: FormEvent) => {
        event.preventDefault();
        put(`/subscriptions/${subscription.id}`, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    const featureEntries = Object.entries(subscription.features ?? {});
    const limitEntries = Object.entries(subscription.limits ?? {});

    return (
        <SuperAdminLayout
            headerTitle={`${subscription.plan_name} — ${subscription.tenant_name}`}
            headerSubtitle="Subscription detail"
        >
            <Head
                title={`${subscription.plan_name} — ${subscription.tenant_name}`}
            />
            <div className="space-y-5 pb-12">
                <PageHeader
                    eyebrow="Subscription"
                    title={`${subscription.plan_name} for ${subscription.tenant_name}`}
                    description="The commercial contract for this hospital. Plan changes are made from the hospital page so proration and notifications always run."
                    icon={CreditCardIcon}
                    breadcrumbs={[
                        { label: 'Billing' },
                        { label: 'Subscriptions', href: '/subscriptions' },
                        { label: subscription.plan_name },
                    ]}
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                icon={BuildingOfficeIcon}
                                href={`/tenants/${subscription.tenant_id}`}
                            >
                                Open hospital
                            </Button>
                            <Button variant="secondary" href="/subscriptions">
                                Back to list
                            </Button>
                        </>
                    }
                    meta={
                        <>
                            <SubscriptionStatusBadge
                                label={subscription.status_label}
                                tone={subscription.status_tone}
                                daysRemaining={subscription.days_remaining}
                            />
                            <span className="tabular-nums">
                                {formatCurrency(
                                    subscription.price,
                                    subscription.currency,
                                )}{' '}
                                / {subscription.billing_cycle}
                            </span>
                        </>
                    }
                />

                <div className="grid gap-5 xl:grid-cols-3">
                    <div className="space-y-5 xl:col-span-2">
                        <SectionCard
                            title="Contract"
                            description="Term, price and renewal state."
                            icon={CreditCardIcon}
                        >
                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label="Hospital"
                                    value={subscription.tenant_name}
                                    icon={BuildingOfficeIcon}
                                />
                                <KeyValue
                                    label="Domain"
                                    value={subscription.tenant_domain ?? '—'}
                                    icon={GlobeAltIcon}
                                    mono
                                />
                                <KeyValue
                                    label="Price"
                                    value={formatCurrency(
                                        subscription.price,
                                        subscription.currency,
                                    )}
                                    icon={BanknotesIcon}
                                />
                                <KeyValue
                                    label="Billing cycle"
                                    value={
                                        subscription.billing_cycle === 'yearly'
                                            ? 'Yearly'
                                            : 'Monthly'
                                    }
                                    icon={ArrowPathIcon}
                                />
                                <KeyValue
                                    label="Period start"
                                    value={formatDate(subscription.start_date)}
                                    icon={CalendarDaysIcon}
                                />
                                <KeyValue
                                    label="Period end"
                                    value={formatDate(subscription.end_date)}
                                    icon={CalendarDaysIcon}
                                />
                                <KeyValue
                                    label="Auto renew"
                                    value={
                                        subscription.auto_renew ? 'On' : 'Off'
                                    }
                                    icon={ArrowPathIcon}
                                />
                                <KeyValue
                                    label="Cancels at period end"
                                    value={
                                        subscription.cancel_at_period_end
                                            ? 'Yes'
                                            : 'No'
                                    }
                                    icon={ArrowPathIcon}
                                />
                                <KeyValue
                                    label="Created"
                                    value={formatDateTime(
                                        subscription.created_at,
                                    )}
                                    icon={CalendarDaysIcon}
                                />
                                <KeyValue
                                    label="Last updated"
                                    value={formatDateTime(
                                        subscription.updated_at,
                                    )}
                                    icon={CalendarDaysIcon}
                                />
                            </KeyValueGrid>

                            {subscription.cancel_at_period_end && (
                                <Alert
                                    tone="warning"
                                    title="Ends at period end"
                                    className="mt-4"
                                >
                                    Access stops on{' '}
                                    {formatDate(subscription.end_date)}. Resume
                                    it from the hospital page if the customer
                                    stays.
                                </Alert>
                            )}
                        </SectionCard>

                        <SectionCard
                            title="Invoices"
                            description={`${invoices.length} invoice${invoices.length === 1 ? '' : 's'} for this contract.`}
                            icon={DocumentTextIcon}
                        >
                            {invoices.length === 0 ? (
                                <EmptyState
                                    panel={false}
                                    icon={DocumentTextIcon}
                                    title="No invoices yet"
                                    description="Invoices appear once the first billing period is raised."
                                />
                            ) : (
                                <Table bare scroll>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHeaderCell>
                                                Number
                                            </TableHeaderCell>
                                            <TableHeaderCell>
                                                Period
                                            </TableHeaderCell>
                                            <TableHeaderCell numeric>
                                                Total
                                            </TableHeaderCell>
                                            <TableHeaderCell>
                                                Status
                                            </TableHeaderCell>
                                            <TableHeaderCell>
                                                Paid
                                            </TableHeaderCell>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {invoices.map((invoice) => (
                                            <TableRow key={invoice.id}>
                                                <TableCell>
                                                    <span className="font-mono text-[12.5px]">
                                                        {invoice.number}
                                                    </span>
                                                </TableCell>
                                                <TableCell
                                                    muted
                                                    className="tabular-nums"
                                                >
                                                    {formatDate(
                                                        invoice.period_start,
                                                    )}{' '}
                                                    →{' '}
                                                    {formatDate(
                                                        invoice.period_end,
                                                    )}
                                                </TableCell>
                                                <TableCell numeric>
                                                    {formatCurrency(
                                                        invoice.total,
                                                        invoice.currency,
                                                    )}
                                                </TableCell>
                                                <TableCell>
                                                    <Badge
                                                        tone={
                                                            invoice.status ===
                                                            'paid'
                                                                ? 'positive'
                                                                : invoice.status ===
                                                                    'past_due'
                                                                  ? 'warning'
                                                                  : 'muted'
                                                        }
                                                        dot
                                                    >
                                                        {invoice.status.replace(
                                                            /_/g,
                                                            ' ',
                                                        )}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell muted>
                                                    {invoice.paid_at
                                                        ? formatDateTime(
                                                              invoice.paid_at,
                                                          )
                                                        : '—'}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </SectionCard>
                    </div>

                    <div className="space-y-5">
                        <SectionCard
                            title="Capabilities"
                            description="Effective feature flags for this subscription."
                            icon={KeyIcon}
                        >
                            {featureEntries.length === 0 ? (
                                <p className="text-[12.5px] text-ink-subtle">
                                    No feature flags on this subscription.
                                </p>
                            ) : (
                                <ul className="space-y-1.5">
                                    {featureEntries.map(([key, enabled]) => (
                                        <li
                                            key={key}
                                            className={cn(
                                                'flex items-center gap-2 text-[12.5px]',
                                                enabled
                                                    ? 'text-ink'
                                                    : 'text-ink-subtle line-through',
                                            )}
                                        >
                                            <CheckCircleIcon
                                                className={cn(
                                                    'h-3.5 w-3.5 shrink-0',
                                                    enabled
                                                        ? 'text-positive-ink'
                                                        : 'text-ink-subtle',
                                                )}
                                            />
                                            {humaniseKey(key)}
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </SectionCard>

                        <SectionCard
                            title="Limits"
                            description="Effective quotas, plan defaults included."
                            icon={KeyIcon}
                        >
                            {limitEntries.length === 0 ? (
                                <p className="text-[12.5px] text-ink-subtle">
                                    No quotas defined.
                                </p>
                            ) : (
                                <dl className="space-y-2">
                                    {limitEntries.map(([key, value]) => (
                                        <div
                                            key={key}
                                            className="flex items-center justify-between gap-3"
                                        >
                                            <dt className="text-[12.5px] text-ink-muted">
                                                {humaniseKey(key)}
                                            </dt>
                                            <dd className="text-[12.5px] font-medium text-ink tabular-nums">
                                                {value === null
                                                    ? 'Unlimited'
                                                    : value}
                                            </dd>
                                        </div>
                                    ))}
                                </dl>
                            )}
                        </SectionCard>

                        <SectionCard
                            title="Internal note"
                            description="Saved on the subscription record."
                            icon={ChatBubbleLeftEllipsisIcon}
                        >
                            <form onSubmit={saveNotes} className="space-y-3">
                                <Field
                                    label="Note"
                                    htmlFor="subscription-notes"
                                    error={errors.notes}
                                >
                                    <Textarea
                                        id="subscription-notes"
                                        rows={4}
                                        maxLength={2000}
                                        value={data.notes}
                                        placeholder="Agreed discount, renewal conversation…"
                                        onChange={(event) =>
                                            setData('notes', event.target.value)
                                        }
                                        invalid={Boolean(errors.notes)}
                                    />
                                </Field>

                                <div className="flex items-center justify-between gap-2">
                                    <span className="text-[11.5px] text-ink-subtle tabular-nums">
                                        {data.notes.length} / 2000
                                    </span>
                                    <Button
                                        type="submit"
                                        size="sm"
                                        icon={PaperAirplaneIcon}
                                        loading={processing}
                                        disabled={!isDirty}
                                    >
                                        Save note
                                    </Button>
                                </div>
                            </form>
                        </SectionCard>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
