import {
    DocumentTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowLeftIcon,
    CalendarDaysIcon,
    ClockIcon,
    ShieldCheckIcon,
    CreditCardIcon,
    BanknotesIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import AppLayout from '@/Layouts/AppLayout';

interface InvoiceItem {
    id: number;
    item_type: string;
    description: string;
    quantity: number;
    unit_price: number;
    total_price: number;
}

interface CashPayment {
    id: number;
    amount_paid: number;
    payment_method: string;
    created_at: string;
}

interface Claim {
    id: number;
    claim_amount: number;
    status: string;
    payer?: {
        id: number;
        name: string;
        coverage_percentage: number;
    } | null;
}

interface InvoiceDetail {
    id: number;
    invoice_number: string;
    invoice_date: string;
    due_date: string | null;
    subtotal: number;
    tax_amount: number;
    total_amount: number;
    status: string;
    paid_at: string | null;
    notes: string | null;
    total_paid: number;
    remaining_balance: number;
    is_fully_paid: boolean;
    patient: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid: string;
    } | null;
    items: InvoiceItem[];
    cash_payments: CashPayment[];
    claim: Claim | null;
}

interface Props {
    invoice: InvoiceDetail;
}

const STATUS_BADGE_MAP: Record<string, 'gray' | 'amber' | 'emerald' | 'red'> = {
    draft: 'gray',
    pending: 'amber',
    paid: 'emerald',
    overdue: 'red',
    cancelled: 'gray',
};

const CLAIM_STATUS_MAP: Record<string, 'gray' | 'amber' | 'emerald' | 'red'> = {
    draft: 'gray',
    submitted: 'amber',
    approved: 'emerald',
    rejected: 'red',
    paid: 'emerald',
};

export default function InvoiceShow({ invoice }: Props) {
    const canPay = invoice.status !== 'paid' && invoice.status !== 'cancelled';
    const [showPaymentForm, setShowPaymentForm] = useState(false);

    const {
        data: paymentData,
        setData: setPaymentData,
        post: postPayment,
        processing: paymentProcessing,
        errors: paymentErrors,
        reset: resetPayment,
    } = useForm({
        amount: invoice.remaining_balance,
        payment_method: 'cash',
    });

    const handleRecordPayment = (e: React.FormEvent) => {
        e.preventDefault();
        postPayment(route('invoices.record-payment', invoice.id), {
            onSuccess: () => {
                setShowPaymentForm(false);
                resetPayment();
            },
        });
    };

    const insuranceCovered = invoice.claim?.claim_amount
        ? Number(invoice.claim.claim_amount)
        : 0;

    return (
        <AppLayout
            headerTitle={`Facture ${invoice.invoice_number}`}
            headerSubtitle="Détail de la facture"
        >
            <Head title={`Facture ${invoice.invoice_number}`} />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: 'Accueil', href: route('dashboard') },
                        {
                            label: 'Finance',
                            href: route('finance.invoices.index'),
                        },
                        { label: invoice.invoice_number },
                    ]}
                />

                <div className="flex items-center justify-between">
                    <Link
                        href={route('finance.invoices.index')}
                        className="flex items-center gap-1.5 text-sm font-medium text-ink-subtle transition-colors hover:text-ink"
                    >
                        <ArrowLeftIcon className="h-4 w-4" /> Retour
                    </Link>
                    <div className="flex items-center gap-2">
                        {canPay && (
                            <>
                                <button
                                    onClick={() =>
                                        setShowPaymentForm(!showPaymentForm)
                                    }
                                    className="flex items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-bold text-ink-inverse transition-colors hover:bg-accent-hover"
                                >
                                    <BanknotesIcon className="h-4 w-4" />{' '}
                                    Enregistrer un paiement
                                </button>
                                <button
                                    onClick={() => {
                                        if (confirm('Marquer comme payée ?')) {
                                            router.patch(
                                                route(
                                                    'finance.invoices.status',
                                                    invoice.id,
                                                ),
                                                { status: 'paid' },
                                            );
                                        }
                                    }}
                                    className="flex items-center gap-1.5 rounded-lg bg-positive px-4 py-2 text-sm font-bold text-ink-inverse transition-colors hover:bg-positive"
                                >
                                    <CheckCircleIcon className="h-4 w-4" />{' '}
                                    Marquer payée
                                </button>
                                <button
                                    onClick={() => {
                                        if (
                                            confirm('Annuler cette facture ?')
                                        ) {
                                            router.patch(
                                                route(
                                                    'finance.invoices.status',
                                                    invoice.id,
                                                ),
                                                { status: 'cancelled' },
                                            );
                                        }
                                    }}
                                    className="flex items-center gap-1.5 rounded-lg border border-line bg-surface px-4 py-2 text-sm font-bold text-ink-muted transition-colors hover:bg-critical-soft"
                                >
                                    <XCircleIcon className="h-4 w-4" /> Annuler
                                </button>
                            </>
                        )}
                    </div>
                </div>

                {/* Record Payment Drawer */}
                {showPaymentForm && (
                    <div className="rounded-xl border border-accent/30 bg-accent-soft p-6">
                        <h3 className="mb-4 text-sm font-bold text-accent-ink">
                            Enregistrer un paiement partiel
                        </h3>
                        <form
                            onSubmit={handleRecordPayment}
                            className="flex items-end gap-4"
                        >
                            <div className="flex-1">
                                <label className="mb-1 block text-xs font-semibold text-ink-muted uppercase">
                                    Montant (XAF)
                                </label>
                                <input
                                    type="number"
                                    value={paymentData.amount}
                                    onChange={(e) =>
                                        setPaymentData(
                                            'amount',
                                            Number(e.target.value),
                                        )
                                    }
                                    min={1}
                                    max={invoice.remaining_balance}
                                    className="w-full rounded-lg border border-line-strong bg-surface px-3 py-2 text-ink focus:ring-2 focus:ring-blue-500"
                                />
                                {paymentErrors.amount && (
                                    <p className="mt-1 text-xs text-red-500">
                                        {paymentErrors.amount}
                                    </p>
                                )}
                            </div>
                            <div>
                                <label className="mb-1 block text-xs font-semibold text-ink-muted uppercase">
                                    Mode
                                </label>
                                <select
                                    value={paymentData.payment_method}
                                    onChange={(e) =>
                                        setPaymentData(
                                            'payment_method',
                                            e.target.value,
                                        )
                                    }
                                    className="rounded-lg border border-line-strong bg-surface px-3 py-2 text-ink focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="cash">Espèces</option>
                                    <option value="mobile_money">
                                        Mobile Money
                                    </option>
                                    <option value="bank_transfer">
                                        Virement
                                    </option>
                                </select>
                            </div>
                            <div className="flex gap-2">
                                <button
                                    type="submit"
                                    disabled={paymentProcessing}
                                    className="rounded-lg bg-accent px-4 py-2 text-sm font-bold text-ink-inverse hover:bg-accent-hover disabled:opacity-50"
                                >
                                    {paymentProcessing
                                        ? 'En cours...'
                                        : 'Enregistrer'}
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setShowPaymentForm(false)}
                                    className="rounded-lg border border-line-strong bg-surface px-4 py-2 text-sm font-medium text-ink-muted hover:bg-surface-muted"
                                >
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                {/* Main Invoice Card */}
                <PageSection
                    icon={DocumentTextIcon}
                    title={`Facture ${invoice.invoice_number}`}
                >
                    <div className="p-6">
                        <div className="mb-6 flex items-start justify-between">
                            <div>
                                <div className="mb-2 flex items-center gap-3">
                                    <StatusBadge
                                        label={invoice.status}
                                        color={
                                            STATUS_BADGE_MAP[invoice.status] ||
                                            'gray'
                                        }
                                    />
                                    {invoice.is_fully_paid &&
                                        invoice.status !== 'paid' && (
                                            <span className="text-xs text-positive-ink">
                                                (Payé intégralement)
                                            </span>
                                        )}
                                </div>
                                {invoice.patient && (
                                    <div>
                                        <p className="text-sm font-bold text-ink">
                                            {invoice.patient.first_name}{' '}
                                            {invoice.patient.last_name}
                                        </p>
                                        <p className="text-xs text-ink-subtle">
                                            {invoice.patient.patient_uid}
                                        </p>
                                    </div>
                                )}
                            </div>
                            <div className="text-right">
                                <p className="text-3xl font-black text-ink">
                                    {Number(
                                        invoice.total_amount,
                                    ).toLocaleString()}{' '}
                                    FCFA
                                </p>
                            </div>
                        </div>

                        {/* Insurance & Payment Summary */}
                        <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <DetailItem
                                icon={CalendarDaysIcon}
                                label="Date"
                                value={new Date(
                                    invoice.invoice_date,
                                ).toLocaleDateString()}
                            />
                            {invoice.due_date && (
                                <DetailItem
                                    icon={ClockIcon}
                                    label="Échéance"
                                    value={new Date(
                                        invoice.due_date,
                                    ).toLocaleDateString()}
                                />
                            )}
                            {invoice.paid_at && (
                                <DetailItem
                                    icon={CheckCircleIcon}
                                    label="Payée le"
                                    value={new Date(
                                        invoice.paid_at,
                                    ).toLocaleDateString()}
                                />
                            )}
                        </div>

                        {/* Insurance & Payment Bars */}
                        <div className="space-y-3 rounded-lg bg-surface-muted p-4">
                            {/* Total Amount */}
                            <div>
                                <div className="mb-1 flex justify-between text-sm">
                                    <span className="text-ink-subtle">
                                        Total
                                    </span>
                                    <span className="font-bold text-ink">
                                        {Number(
                                            invoice.total_amount,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </span>
                                </div>
                            </div>

                            {/* Insurance Covered */}
                            {invoice.claim && (
                                <div>
                                    <div className="mb-1 flex justify-between text-sm">
                                        <span className="flex items-center gap-1 text-accent">
                                            <ShieldCheckIcon className="h-4 w-4" />{' '}
                                            Assurance (
                                            {invoice.claim.payer?.name ?? 'N/A'}
                                            )
                                        </span>
                                        <span className="font-bold text-accent">
                                            {insuranceCovered.toLocaleString()}{' '}
                                            FCFA
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <StatusBadge
                                            label={invoice.claim.status}
                                            color={
                                                CLAIM_STATUS_MAP[
                                                    invoice.claim.status
                                                ] || 'gray'
                                            }
                                        />
                                        {invoice.claim.payer
                                            ?.coverage_percentage && (
                                            <span className="text-xs text-ink-subtle">
                                                {
                                                    invoice.claim.payer
                                                        .coverage_percentage
                                                }
                                                % de couverture
                                            </span>
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Patient Responsibility */}
                            <div className="border-t border-line pt-2">
                                <div className="flex justify-between text-sm">
                                    <span className="text-ink-muted">
                                        Reste à charge patient
                                    </span>
                                    <span className="font-bold text-ink">
                                        {Number(
                                            invoice.remaining_balance,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </span>
                                </div>
                            </div>

                            {/* Payments Received */}
                            <div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-positive-ink">
                                        Déjà payé
                                    </span>
                                    <span className="font-bold text-positive-ink">
                                        {Number(
                                            invoice.total_paid,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </span>
                                </div>
                            </div>

                            {/* Progress Bar */}
                            <div className="h-2 overflow-hidden rounded-full bg-surface-inset">
                                <div
                                    className="h-full rounded-full bg-positive transition-all"
                                    style={{
                                        width: `${Math.min(100, (invoice.total_paid / invoice.total_amount) * 100)}%`,
                                    }}
                                />
                            </div>
                        </div>

                        {invoice.notes && (
                            <div className="mt-4 rounded-lg bg-surface-muted p-3">
                                <p className="text-xs text-ink-subtle">
                                    {invoice.notes}
                                </p>
                            </div>
                        )}
                    </div>
                </PageSection>

                {/* Line Items */}
                <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
                    <div className="border-b border-line bg-surface-muted px-6 py-4">
                        <h3 className="text-sm font-bold text-ink uppercase">
                            Lignes de facturation
                        </h3>
                    </div>
                    <Table bare containerClassName="pt-1">
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>Service</TableHeaderCell>
                                <TableHeaderCell>Type</TableHeaderCell>
                                <TableHeaderCell numeric>Qté</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Prix unitaire
                                </TableHeaderCell>
                                <TableHeaderCell numeric>Total</TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {invoice.items.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-[13px] font-semibold text-ink">
                                        {item.description}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded bg-surface-inset px-2 py-0.5 text-xs font-medium text-ink-subtle capitalize">
                                            {item.item_type.replace('_', ' ')}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric muted>
                                        {item.quantity}
                                    </TableCell>
                                    <TableCell numeric muted>
                                        {Number(
                                            item.unit_price,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </TableCell>
                                    <TableCell
                                        numeric
                                        className="font-semibold text-ink"
                                    >
                                        {Number(
                                            item.total_price,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                {/* Payments History */}
                {invoice.cash_payments && invoice.cash_payments.length > 0 && (
                    <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
                        <div className="border-b border-line bg-surface-muted px-6 py-4">
                            <h3 className="flex items-center gap-2 text-sm font-bold text-ink uppercase">
                                <CreditCardIcon className="h-4 w-4" /> Paiements
                                reçus
                            </h3>
                        </div>
                        <Table bare containerClassName="pt-1">
                            <TableHeader>
                                <TableRow className="hover:bg-transparent">
                                    <TableHeaderCell>Date</TableHeaderCell>
                                    <TableHeaderCell>Mode</TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        Montant
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {invoice.cash_payments.map((payment) => (
                                    <TableRow key={payment.id}>
                                        <TableCell muted>
                                            {new Date(
                                                payment.created_at,
                                            ).toLocaleDateString()}
                                        </TableCell>
                                        <TableCell>
                                            <span className="rounded bg-surface-inset px-2 py-0.5 text-xs font-medium text-ink-subtle capitalize">
                                                {payment.payment_method.replace(
                                                    '_',
                                                    ' ',
                                                )}
                                            </span>
                                        </TableCell>
                                        <TableCell
                                            numeric
                                            className="font-semibold text-positive-ink"
                                        >
                                            {Number(
                                                payment.amount_paid,
                                            ).toLocaleString()}{' '}
                                            FCFA
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}

                {/* Totals */}
                <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
                    <div className="p-6">
                        <div className="ml-auto max-w-sm space-y-2">
                            <div className="flex justify-between text-sm">
                                <span className="text-ink-subtle">
                                    Sous-total
                                </span>
                                <span className="font-bold text-ink">
                                    {Number(invoice.subtotal).toLocaleString()}{' '}
                                    FCFA
                                </span>
                            </div>
                            <div className="flex justify-between text-sm">
                                <span className="text-ink-subtle">Taxe</span>
                                <span className="font-bold text-ink">
                                    {Number(
                                        invoice.tax_amount,
                                    ).toLocaleString()}{' '}
                                    FCFA
                                </span>
                            </div>
                            <div className="flex justify-between border-t border-line pt-2 text-lg">
                                <span className="font-bold text-ink">
                                    Total
                                </span>
                                <span className="font-black text-ink">
                                    {Number(
                                        invoice.total_amount,
                                    ).toLocaleString()}{' '}
                                    FCFA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
