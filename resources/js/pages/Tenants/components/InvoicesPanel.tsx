import {
    BanknotesIcon,
    CheckCircleIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Dialog,
    EmptyState,
    Field,
    Input,
    SectionCard,
    Select,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { cn } from '@/lib/utils';
import {
    formatCurrency,
    formatDate,
    formatDateTime,
    toneFromBackend,
} from '@/pages/Platform/components/format';
import type { TenantInvoice } from '@/pages/Platform/components/types';

interface InvoicesPanelProps {
    tenantId: string;
    tenantName: string;
    invoices: TenantInvoice[];
    canManage: boolean;
}

/** Invoice statuses an operator can settle from the panel. */
function isSettleable(status: string): boolean {
    return status !== 'paid' && status !== 'void' && status !== 'refunded';
}

const PAYMENT_METHODS = [
    { value: 'cash', label: 'Cash' },
    { value: 'mobile_money', label: 'Mobile money (MTN / Orange)' },
    { value: 'bank_transfer', label: 'Bank transfer' },
    { value: 'cheque', label: 'Cheque' },
    { value: 'card', label: 'Card' },
    { value: 'other', label: 'Other' },
];

/**
 * Invoices — the platform-side billing history for one hospital, with a
 * "mark paid" action that records the payment method and reference.
 */
export function InvoicesPanel({
    tenantId,
    tenantName,
    invoices,
    canManage,
}: InvoicesPanelProps) {
    const [target, setTarget] = useState<TenantInvoice | null>(null);
    const [method, setMethod] = useState('mobile_money');
    const [reference, setReference] = useState('');
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const outstanding = useMemo(
        () =>
            invoices
                .filter((invoice) => isSettleable(invoice.status))
                .reduce((total, invoice) => total + invoice.total, 0),
        [invoices],
    );

    const currency = invoices[0]?.currency ?? 'XAF';

    const openPay = (invoice: TenantInvoice) => {
        setErrors({});
        setMethod('mobile_money');
        setReference('');
        setTarget(invoice);
    };

    const submitPay = () => {
        if (!target) {
            return;
        }

        setProcessing(true);
        setErrors({});

        router.post(
            `/tenants/${tenantId}/subscription/invoices/${target.id}/pay`,
            { payment_method: method, reference: reference || null },
            {
                preserveScroll: true,
                onSuccess: () => setTarget(null),
                onError: (backendErrors: Record<string, string>) =>
                    setErrors(backendErrors),
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <>
            <SectionCard
                title="Invoices"
                description={`Platform billing history for ${tenantName}.`}
                icon={DocumentTextIcon}
                actions={
                    outstanding > 0 ? (
                        <Badge tone="warning" dot>
                            {formatCurrency(outstanding, currency)} outstanding
                        </Badge>
                    ) : null
                }
            >
                {invoices.length === 0 ? (
                    <EmptyState
                        panel={false}
                        icon={DocumentTextIcon}
                        title="No invoices yet"
                        description="Invoices appear here once the subscription period is billed."
                    />
                ) : (
                    <Table bare scroll>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>Number</TableHeaderCell>
                                <TableHeaderCell>Period</TableHeaderCell>
                                <TableHeaderCell numeric>Total</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell>Paid</TableHeaderCell>
                                <TableHeaderCell align="right">
                                    Actions
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {invoices.map((invoice) => (
                                <TableRow key={invoice.id}>
                                    <TableCell>
                                        <span className="font-mono text-[12.5px] font-medium text-ink">
                                            {invoice.number}
                                        </span>
                                    </TableCell>
                                    <TableCell muted className="tabular-nums">
                                        {formatDate(invoice.period_start)} →{' '}
                                        {formatDate(invoice.period_end)}
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
                                                invoice.status === 'paid'
                                                    ? 'positive'
                                                    : invoice.status ===
                                                        'past_due'
                                                      ? 'warning'
                                                      : toneFromBackend(
                                                            invoice.status,
                                                            'muted',
                                                        )
                                            }
                                            dot
                                        >
                                            {invoice.status.replace(/_/g, ' ')}
                                        </Badge>
                                    </TableCell>
                                    <TableCell muted>
                                        {invoice.paid_at
                                            ? formatDateTime(invoice.paid_at)
                                            : '—'}
                                    </TableCell>
                                    <TableCell align="right">
                                        {canManage &&
                                        isSettleable(invoice.status) ? (
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                icon={CheckCircleIcon}
                                                onClick={() => openPay(invoice)}
                                            >
                                                Mark paid
                                            </Button>
                                        ) : (
                                            <span className="text-[12px] text-ink-subtle">
                                                {isSettleable(invoice.status)
                                                    ? '—'
                                                    : 'Settled'}
                                            </span>
                                        )}
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}
            </SectionCard>

            <Dialog
                open={target !== null}
                onClose={processing ? () => undefined : () => setTarget(null)}
                title={`Mark invoice ${target?.number ?? ''} paid`}
                description="Records the settlement. This does not charge a card — use it for cash, mobile money and bank transfers."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setTarget(null)}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="primary"
                            icon={BanknotesIcon}
                            loading={processing}
                            onClick={submitPay}
                        >
                            Mark paid
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    {target && (
                        <div className="flex items-center justify-between rounded-lg border border-line bg-surface-muted px-3.5 py-3">
                            <span className="text-[12.5px] text-ink-muted">
                                Amount to settle
                            </span>
                            <span
                                className={cn(
                                    'text-[15px] font-semibold text-ink tabular-nums',
                                )}
                            >
                                {formatCurrency(target.total, target.currency)}
                            </span>
                        </div>
                    )}

                    <Field
                        label="Payment method"
                        htmlFor="payment_method"
                        required
                        error={errors.payment_method}
                    >
                        <Select
                            id="payment_method"
                            value={method}
                            onChange={(event) => setMethod(event.target.value)}
                            invalid={Boolean(errors.payment_method)}
                        >
                            {PAYMENT_METHODS.map((option) => (
                                <option key={option.value} value={option.value}>
                                    {option.label}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    <Field
                        label="Reference (optional)"
                        htmlFor="payment_reference"
                        error={errors.reference}
                        hint="Transaction ID, cheque number or receipt reference."
                    >
                        <Input
                            id="payment_reference"
                            value={reference}
                            onChange={(event) =>
                                setReference(event.target.value)
                            }
                            placeholder="MOMO-2026-000123"
                            maxLength={100}
                            invalid={Boolean(errors.reference)}
                        />
                    </Field>

                    {Object.keys(errors).length > 0 &&
                        !errors.payment_method &&
                        !errors.reference && (
                            <Alert tone="critical" title="Could not mark paid">
                                {Object.values(errors).join(' ')}
                            </Alert>
                        )}
                </div>
            </Dialog>
        </>
    );
}
