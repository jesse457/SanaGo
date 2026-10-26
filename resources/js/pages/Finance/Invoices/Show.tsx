import {
    BanknotesIcon,
    CheckCircleIcon,
    PrinterIcon,
    ShieldCheckIcon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import {
    Alert,
    Button,
    Dialog,
    Input,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    Select,
    SegmentedControl,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { InvoiceStatusBadge } from '@/pages/Finance/components/FinanceStatusBadges';
import { formatDate, formatMoney } from '@/pages/Finance/components/format';

interface DocumentPatient {
    first_name: string;
    last_name: string;
    patient_uid: string;
    dob: string | null;
    gender: string | null;
    address: string;
}

interface DocumentItem {
    id: number;
    description: string;
    item_type: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    paid_amount: number;
    settled: boolean;
}

interface DocumentPayment {
    id: number;
    amount: number;
    method: string;
    paid_at: string | null;
}

interface DocumentClaim {
    id: number;
    status: string;
    claim_amount: number;
    payer: string | null;
    coverage: number;
}

interface InvoiceDocument {
    id: number;
    invoice_number: string;
    status: string;
    invoice_date: string | null;
    due_date: string | null;
    paid_at: string | null;
    subtotal: number;
    tax_amount: number;
    total_amount: number;
    total_paid: number;
    remaining_balance: number;
    notes: string | null;
    patient: DocumentPatient | null;
    items: DocumentItem[];
    payments: DocumentPayment[];
    claim: DocumentClaim | null;
}

/** Locales whose scripts flow right to left; the printed document follows. */
const RTL_LOCALES = new Set(['ar', 'he', 'fa', 'ur']);

type PaperFormat = 'a4' | 'receipt';

const A4_TABS = [
    { value: 'a4', label: 'A4 sheet' },
    { value: 'receipt', label: 'Till receipt' },
];

/**
 * An invoice as a document. The eye icon on the ledger lands here: the bill is
 * rendered as a printable sheet (A4 or till receipt) plus the controls beside
 * it. Printing captures only the `.print-document` block, never the app
 * chrome, and the sheet respects RTL for right-to-left locales.
 */
export default function InvoiceShow({ invoice }: { invoice: InvoiceDocument }) {
    const { t, locale } = useTranslation();
    const appName = usePage().props.name as string;
    const dir = RTL_LOCALES.has(locale) ? 'rtl' : 'ltr';

    const [format, setFormat] = useState<PaperFormat>('a4');
    const [showPaymentForm, setShowPaymentForm] = useState(false);
    const [confirm, setConfirm] = useState<'paid' | 'close' | null>(null);

    // Scoped print isolation: when the page is printing, only the document
    // shows. The class is added while the page is mounted and removed on exit.
    useEffect(() => {
        document.body.classList.add('print-document-printing');

        return () => document.body.classList.remove('print-document-printing');
    }, []);

    const {
        data: paymentData,
        setData: setPaymentData,
        post: postPayment,
        processing: paymentProcessing,
        errors: paymentErrors,
        reset: resetPayment,
    } = useForm({
        amount: Math.ceil(invoice.remaining_balance),
        payment_method: 'cash',
    });

    const collectible = ['draft', 'pending', 'overdue'].includes(
        invoice.status,
    );
    // Overdue debt is closed (written off) rather than cancelled ad hoc.
    const outstanding = invoice.remaining_balance > 0;

    const handlePrint = () => window.print();

    const handleRecordPayment = () => {
        postPayment(route('invoices.record-payment', invoice.id), {
            onSuccess: () => {
                setShowPaymentForm(false);
                resetPayment();
            },
        });
    };

    const changeStatus = (
        status: 'paid' | 'cancelled',
        onDone?: () => void,
    ) => {
        router.patch(
            route('finance.invoices.status', invoice.id),
            { status },
            { onFinish: () => onDone?.() },
        );
    };

    const labels = A4_TABS.map((tab) => ({
        value: tab.value,
        label: t(tab.label),
    }));

    const isA4 = format === 'a4';
    const headingClass = isA4
        ? 'text-[22px] font-bold tracking-tight'
        : 'text-[12px] font-bold tracking-tight';
    const labelClass = isA4
        ? 'text-[11px] font-semibold tracking-wide text-neutral-500'
        : 'text-[8.5px] font-semibold tracking-wide text-neutral-500';

    return (
        <AppLayout
            headerTitle={invoice.invoice_number}
            headerSubtitle={t('finance.invoices.index.subtitle')}
        >
            <Head title={invoice.invoice_number} />

            <div className="space-y-5">
                <PageHeader
                    title={invoice.invoice_number}
                    description={t('finance.invoices.index.subtitle')}
                    breadcrumbs={[
                        { label: t('finance.invoices.index.finance') },
                        { label: t('finance.invoices.index.invoices') },
                        { label: invoice.invoice_number },
                    ]}
                    actions={
                        <>
                            <SegmentedControl
                                size="sm"
                                value={format}
                                onChange={(value) =>
                                    setFormat(value as PaperFormat)
                                }
                                options={labels}
                            />
                            <Button
                                variant="primary"
                                size="sm"
                                icon={PrinterIcon}
                                onClick={handlePrint}
                            >
                                {t('Print invoice')}
                            </Button>
                        </>
                    }
                />

                {/* Document controls stay on screen only */}
                <div
                    className="print-hide flex flex-wrap items-center gap-2"
                    role="group"
                    aria-label={t('Invoice actions')}
                >
                    {collectible && (
                        <>
                            <Button
                                variant="accent"
                                size="sm"
                                icon={BanknotesIcon}
                                onClick={() => setShowPaymentForm(true)}
                            >
                                {t('Record a payment')}
                            </Button>
                            {invoice.status === 'overdue' ? (
                                <Button
                                    variant="danger"
                                    size="sm"
                                    onClick={() => setConfirm('close')}
                                >
                                    {t('Close invoice')}
                                </Button>
                            ) : (
                                <>
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        icon={CheckCircleIcon}
                                        onClick={() => setConfirm('paid')}
                                    >
                                        {t('Mark invoice paid')}
                                    </Button>
                                    <Button
                                        variant="danger"
                                        size="sm"
                                        onClick={() => setConfirm('close')}
                                    >
                                        {t('Cancel invoice')}
                                    </Button>
                                </>
                            )}
                        </>
                    )}
                </div>

                {collectible && showPaymentForm && (
                    <div className="print-hide">
                        <Alert tone="info" title={t('Record a payment')}>
                            <p className="text-[12.5px] text-accent-ink">
                                {t(
                                    'The payment is filed on the open cash shift of the cashier taking it.',
                                )}
                            </p>
                            <form
                                className="mt-4 grid gap-4 sm:grid-cols-[1fr_1fr_auto]"
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    handleRecordPayment();
                                }}
                            >
                                <div>
                                    <label className="mb-1 block text-xs font-semibold tracking-wide text-ink-muted uppercase">
                                        {t('Amount')}
                                    </label>
                                    <Input
                                        id="payment_amount"
                                        type="number"
                                        min={1}
                                        max={invoice.remaining_balance}
                                        value={paymentData.amount}
                                        onChange={(e) =>
                                            setPaymentData(
                                                'amount',
                                                Number(e.target.value),
                                            )
                                        }
                                        invalid={Boolean(paymentErrors.amount)}
                                    />
                                    {paymentErrors.amount && (
                                        <p className="mt-1 text-xs text-critical-ink">
                                            {paymentErrors.amount}
                                        </p>
                                    )}
                                </div>
                                <div>
                                    <label className="mb-1 block text-xs font-semibold tracking-wide text-ink-muted uppercase">
                                        {t('Method')}
                                    </label>
                                    <Select
                                        id="payment_method"
                                        value={paymentData.payment_method}
                                        onChange={(e) =>
                                            setPaymentData(
                                                'payment_method',
                                                e.target.value,
                                            )
                                        }
                                        invalid={Boolean(
                                            paymentErrors.payment_method,
                                        )}
                                    >
                                        <option value="cash">
                                            {t('Cash')}
                                        </option>
                                        <option value="mobile_money">
                                            {t('Mobile money')}
                                        </option>
                                        <option value="bank_transfer">
                                            {t('Bank transfer')}
                                        </option>
                                    </Select>
                                    {paymentErrors.payment_method && (
                                        <p className="mt-1 text-xs text-critical-ink">
                                            {paymentErrors.payment_method}
                                        </p>
                                    )}
                                </div>
                                <div className="flex items-end gap-2">
                                    <Button
                                        type="submit"
                                        variant="accent"
                                        loading={paymentProcessing}
                                    >
                                        {t('Save')}
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        onClick={() =>
                                            setShowPaymentForm(false)
                                        }
                                    >
                                        {t('Dismiss')}
                                    </Button>
                                </div>
                            </form>
                        </Alert>
                    </div>
                )}

                {collectible && !showPaymentForm && outstanding && (
                    <Alert
                        tone="warning"
                        className="print-hide"
                        title={t('Money is still owed on this bill')}
                    >
                        <p className="text-[12.5px] text-warning-ink">
                            {t(
                                'Remaining: :amount. Partial payments already made mark the bill overdue.',
                                {
                                    amount: formatMoney(
                                        invoice.remaining_balance,
                                    ),
                                },
                            )}
                        </p>
                    </Alert>
                )}

                {/* The printable document */}
                <div
                    dir={dir}
                    className={
                        'print-document mx-auto bg-white text-neutral-950 shadow-surface ring-1 ring-neutral-950/10 ' +
                        (isA4
                            ? 'w-full max-w-[210mm] px-14 py-12 text-[13px] leading-relaxed'
                            : 'w-[80mm] px-4 py-5 text-[10px] leading-snug')
                    }
                >
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <p
                                className={
                                    isA4
                                        ? 'text-[15px] font-bold tracking-tight'
                                        : 'text-[11px] font-bold tracking-tight'
                                }
                            >
                                {appName}
                            </p>
                            <p
                                className={
                                    isA4
                                        ? 'text-[11px] text-neutral-500'
                                        : 'text-[8.5px] text-neutral-500'
                                }
                            >
                                {t('Invoice')} {invoice.invoice_number}
                            </p>
                        </div>
                        <div className="text-end">
                            <p className={headingClass}>
                                {formatMoney(invoice.total_amount)}
                            </p>
                            <div className="mt-1 flex justify-end">
                                <BadgeSoft status={invoice.status} />
                            </div>
                        </div>
                    </div>

                    <div
                        className={
                            'mt-6 grid gap-6 sm:grid-cols-2 ' +
                            (isA4 ? '' : 'text-[10px]')
                        }
                    >
                        <div>
                            <p className={labelClass}>
                                {t('Billed to').toUpperCase()}
                            </p>
                            {invoice.patient ? (
                                <>
                                    <p className="mt-1 font-semibold">
                                        {invoice.patient.last_name}{' '}
                                        {invoice.patient.first_name}
                                    </p>
                                    <p
                                        className={
                                            isA4
                                                ? 'font-mono text-[11px] text-neutral-500'
                                                : 'font-mono text-[8.5px] text-neutral-500'
                                        }
                                    >
                                        {invoice.patient.patient_uid}
                                    </p>
                                    {invoice.patient.address && (
                                        <p className="mt-1 text-neutral-600">
                                            {invoice.patient.address}
                                        </p>
                                    )}
                                </>
                            ) : (
                                <p className="mt-1 text-neutral-500">—</p>
                            )}
                        </div>
                        <div className="sm:text-end">
                            <p className={labelClass}>
                                {t('Dates').toUpperCase()}
                            </p>
                            <p className={isA4 ? 'mt-1' : 'mt-0.5'}>
                                {t('Invoiced')}:{' '}
                                {invoice.invoice_date
                                    ? formatDate(invoice.invoice_date)
                                    : '—'}
                            </p>
                            {invoice.due_date && (
                                <p>
                                    {t('Due')}: {formatDate(invoice.due_date)}
                                </p>
                            )}
                            {invoice.paid_at && (
                                <p className="text-neutral-700">
                                    {t('Paid')}: {formatDate(invoice.paid_at)}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="mt-6 overflow-hidden rounded-md border border-neutral-200">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-neutral-200 bg-neutral-100 text-start font-semibold">
                                    <th className="px-3 py-1.5 text-start">
                                        {t('Description')}
                                    </th>
                                    <th className="px-3 py-1.5 text-end">
                                        {t('Quantity')}
                                    </th>
                                    <th className="px-3 py-1.5 text-end">
                                        {t('Unit price')}
                                    </th>
                                    <th className="px-3 py-1.5 text-end">
                                        {t('Total')}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {invoice.items.map((item) => (
                                    <tr
                                        key={item.id}
                                        className="border-b border-neutral-100 last:border-b-0"
                                    >
                                        <td className="px-3 py-1.5">
                                            {item.description}
                                        </td>
                                        <td className="px-3 py-1.5 text-end tabular-nums">
                                            {item.quantity}
                                        </td>
                                        <td className="px-3 py-1.5 text-end tabular-nums">
                                            {formatMoney(item.unit_price)}
                                        </td>
                                        <td className="px-3 py-1.5 text-end font-semibold tabular-nums">
                                            {formatMoney(item.total_price)}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {invoice.claim && (
                        <div className="mt-4 rounded-md bg-neutral-100 px-3 py-2">
                            <div className="flex items-start justify-between gap-3">
                                <span className="flex items-center gap-1 font-semibold text-neutral-700">
                                    <ShieldCheckIcon className="h-3.5 w-3.5 shrink-0 rtl:-scale-x-100" />
                                    {invoice.claim.payer ?? t('Payer')}
                                </span>
                                <span className="font-semibold text-neutral-700 tabular-nums">
                                    {formatMoney(invoice.claim.claim_amount)}
                                </span>
                            </div>
                            <p className="mt-1 text-neutral-500">
                                {t('Claim')} {invoice.claim.status} ·{' '}
                                {invoice.claim.coverage}%
                            </p>
                        </div>
                    )}

                    <div
                        className={
                            'mt-6 ml-auto space-y-1 ' +
                            (isA4 ? 'max-w-[240px]' : 'text-[10px]')
                        }
                    >
                        <TotalRow
                            label={t('Subtotal')}
                            value={formatMoney(invoice.subtotal)}
                        />
                        <TotalRow
                            label={t('Tax')}
                            value={formatMoney(invoice.tax_amount)}
                        />
                        <div className="flex justify-between border-t border-neutral-300 pt-1 font-bold">
                            <span>{t('Total')}</span>
                            <span className="tabular-nums">
                                {formatMoney(invoice.total_amount)}
                            </span>
                        </div>
                        <TotalRow
                            label={t('Paid')}
                            value={formatMoney(invoice.total_paid)}
                        />
                        <div
                            className={
                                'flex justify-between font-bold ' +
                                (outstanding ? 'text-red-700' : '')
                            }
                        >
                            <span>{t('Outstanding')}</span>
                            <span className="tabular-nums">
                                {formatMoney(invoice.remaining_balance)}
                            </span>
                        </div>
                    </div>

                    {invoice.payments.length > 0 && (
                        <div className="mt-6">
                            <p className={labelClass}>
                                {t('Payments').toUpperCase()}
                            </p>
                            <ul className="mt-1 space-y-0.5">
                                {invoice.payments.map((payment) => (
                                    <li
                                        key={payment.id}
                                        className="flex justify-between text-neutral-700"
                                    >
                                        <span>
                                            {payment.paid_at
                                                ? formatDate(payment.paid_at)
                                                : '—'}
                                            {' · '}
                                            <span className="capitalize">
                                                {payment.method.replace(
                                                    '_',
                                                    ' ',
                                                )}
                                            </span>
                                        </span>
                                        <span className="font-semibold tabular-nums">
                                            {formatMoney(payment.amount)}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}

                    {invoice.notes && (
                        <div className="mt-5 rounded-md bg-neutral-100 px-3 py-2 text-neutral-600">
                            <p className={labelClass}>
                                {t('Notes').toUpperCase()}
                            </p>
                            <p>{invoice.notes}</p>
                        </div>
                    )}

                    <p
                        className={
                            'mt-8 text-center text-neutral-400 ' +
                            (isA4 ? '' : 'text-[8.5px]')
                        }
                    >
                        {appName} · {new Date().toISOString().slice(0, 10)} ·{' '}
                        {t('Thank you for trusting our care.')}
                    </p>
                </div>

                {/* Settlement summary stays on screen */}
                <KeyValueGrid className="print-hide" columns={4} tile>
                    <KeyValue
                        label={t('Invoiced')}
                        value={formatMoney(invoice.total_amount)}
                    />
                    <KeyValue
                        label={t('Paid')}
                        value={formatMoney(invoice.total_paid)}
                    />
                    <KeyValue
                        label={t('Outstanding')}
                        value={
                            <span
                                className={
                                    outstanding
                                        ? 'text-critical-ink'
                                        : 'text-positive-ink'
                                }
                            >
                                {formatMoney(invoice.remaining_balance)}
                            </span>
                        }
                    />
                    <KeyValue
                        label={t('Status')}
                        value={<InvoiceStatusBadge status={invoice.status} />}
                    />
                </KeyValueGrid>

                <Dialog
                    open={confirm !== null}
                    onClose={() => setConfirm(null)}
                    title={
                        confirm === 'paid'
                            ? t('Mark invoice paid')
                            : t('Close invoice')
                    }
                    size="sm"
                    footer={
                        <>
                            <Button
                                variant="ghost"
                                onClick={() => setConfirm(null)}
                            >
                                {t('Dismiss')}
                            </Button>
                            <Button
                                variant={
                                    confirm === 'paid' ? 'accent' : 'danger'
                                }
                                onClick={() => {
                                    changeStatus(
                                        confirm === 'paid'
                                            ? 'paid'
                                            : 'cancelled',
                                        () => setConfirm(null),
                                    );
                                }}
                            >
                                {confirm === 'paid'
                                    ? t('Mark paid')
                                    : t('Close invoice')}
                            </Button>
                        </>
                    }
                >
                    <p className="text-[13px] text-ink-muted">
                        {confirm === 'paid'
                            ? t('Marks this bill as fully collected.')
                            : t(
                                  'The bill is written off: it stops being collectable at the desk and the money owed stops counting.',
                              )}
                    </p>
                </Dialog>
            </div>
        </AppLayout>
    );
}

function BadgeSoft({ status }: { status: string }) {
    return <InvoiceStatusBadge status={status} />;
}

function TotalRow({ label, value }: { label: string; value: string }) {
    return (
        <div className="flex justify-between">
            <span className="text-neutral-500">{label}</span>
            <span className="tabular-nums">{value}</span>
        </div>
    );
}
