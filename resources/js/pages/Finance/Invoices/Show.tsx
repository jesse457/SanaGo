import {
    DocumentTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowLeftIcon,
    CalendarDaysIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React from 'react';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface InvoiceItem {
    id: number;
    item_type: string;
    description: string;
    quantity: number;
    unit_price: number;
    total_price: number;
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
    patient: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid: string;
    } | null;
    items: InvoiceItem[];
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

export default function InvoiceShow({ invoice }: Props) {
    const { t } = useTranslation();
    const canPay = invoice.status !== 'paid' && invoice.status !== 'cancelled';

    return (
        <AppLayout
            headerTitle={t('finance.invoices.show.title', {
                number: invoice.invoice_number,
            })}
            headerSubtitle={t('finance.invoices.show.subtitle')}
        >
            <Head
                title={t('finance.invoices.show.head_title', {
                    number: invoice.invoice_number,
                })}
            />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        {
                            label: t('finance.invoices.show.finance'),
                            href: route('finance.invoices.index'),
                        },
                        { label: invoice.invoice_number },
                    ]}
                />

                <div className="flex items-center justify-between">
                    <Link
                        href={route('finance.invoices.index')}
                        className="flex items-center gap-1.5 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                    >
                        <ArrowLeftIcon className="h-4 w-4" />{' '}
                        {t('finance.invoices.show.back_to_invoices')}
                    </Link>
                    {canPay && (
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => {
                                    if (
                                        confirm(
                                            t(
                                                'finance.invoices.show.mark_paid_confirm',
                                            ),
                                        )
                                    ) {
                                        router.patch(
                                            route(
                                                'finance.invoices.status',
                                                invoice.id,
                                            ),
                                            { status: 'paid' },
                                        );
                                    }
                                }}
                                className="flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-emerald-700"
                            >
                                <CheckCircleIcon className="h-4 w-4" />{' '}
                                {t('finance.invoices.show.mark_as_paid')}
                            </button>
                            <button
                                onClick={() => {
                                    if (
                                        confirm(
                                            t(
                                                'finance.invoices.show.cancel_confirm',
                                            ),
                                        )
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
                                className="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 transition-colors hover:bg-red-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-red-900/20"
                            >
                                <XCircleIcon className="h-4 w-4" />{' '}
                                {t('finance.invoices.show.cancel_invoice')}
                            </button>
                        </div>
                    )}
                </div>

                <PageSection
                    icon={DocumentTextIcon}
                    title={`${t('finance.invoices.show.invoice_label')} ${invoice.invoice_number}`}
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
                                </div>
                                {invoice.patient && (
                                    <div>
                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                            {invoice.patient.first_name}{' '}
                                            {invoice.patient.last_name}
                                        </p>
                                        <p className="text-xs text-gray-400 dark:text-neutral-500">
                                            {invoice.patient.patient_uid}
                                        </p>
                                    </div>
                                )}
                            </div>
                            <div className="text-right">
                                <p className="text-3xl font-black text-gray-900 dark:text-neutral-100">
                                    {Number(
                                        invoice.total_amount,
                                    ).toLocaleString()}{' '}
                                    FCFA
                                </p>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <DetailItem
                                icon={CalendarDaysIcon}
                                label={t('finance.invoices.show.date')}
                                value={new Date(
                                    invoice.invoice_date,
                                ).toLocaleDateString()}
                            />
                            {invoice.due_date && (
                                <DetailItem
                                    icon={ClockIcon}
                                    label={t('finance.invoices.show.due')}
                                    value={new Date(
                                        invoice.due_date,
                                    ).toLocaleDateString()}
                                />
                            )}
                            {invoice.paid_at && (
                                <DetailItem
                                    icon={CheckCircleIcon}
                                    label={t('finance.invoices.show.paid')}
                                    value={new Date(
                                        invoice.paid_at,
                                    ).toLocaleDateString()}
                                />
                            )}
                        </div>
                        {invoice.notes && (
                            <div className="mt-4 rounded-lg bg-gray-50 p-3 dark:bg-neutral-700/50">
                                <p className="text-xs text-gray-500 dark:text-neutral-400">
                                    {invoice.notes}
                                </p>
                            </div>
                        )}
                    </div>
                </PageSection>

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-700/30">
                        <h3 className="text-sm font-bold text-gray-900 uppercase dark:text-neutral-100">
                            {t('finance.invoices.show.line_items')}
                        </h3>
                    </div>
                    <table className="w-full text-left">
                        <thead className="border-b border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <tr>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.show.item')}
                                </th>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.show.type')}
                                </th>
                                <th className="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.show.qty')}
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.show.unit_price')}
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.show.total')}
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {invoice.items.map((item) => (
                                <tr key={item.id}>
                                    <td className="px-6 py-4 text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {item.description}
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 capitalize dark:bg-neutral-700 dark:text-neutral-400">
                                            {item.item_type.replace('_', ' ')}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-400">
                                        {item.quantity}
                                    </td>
                                    <td className="px-6 py-4 text-right text-sm text-gray-500 dark:text-neutral-400">
                                        {Number(
                                            item.unit_price,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </td>
                                    <td className="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {Number(
                                            item.total_price,
                                        ).toLocaleString()}{' '}
                                        FCFA
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="p-6">
                        <div className="ml-auto max-w-sm space-y-2">
                            <div className="flex justify-between text-sm">
                                <span className="text-gray-500 dark:text-neutral-400">
                                    {t('finance.invoices.show.subtotal')}
                                </span>
                                <span className="font-bold text-gray-900 dark:text-neutral-100">
                                    {Number(invoice.subtotal).toLocaleString()}{' '}
                                    FCFA
                                </span>
                            </div>
                            <div className="flex justify-between text-sm">
                                <span className="text-gray-500 dark:text-neutral-400">
                                    {t('finance.invoices.show.tax')}
                                </span>
                                <span className="font-bold text-gray-900 dark:text-neutral-100">
                                    {Number(
                                        invoice.tax_amount,
                                    ).toLocaleString()}{' '}
                                    FCFA
                                </span>
                            </div>
                            <div className="flex justify-between border-t border-gray-100 pt-2 text-lg dark:border-neutral-700">
                                <span className="font-bold text-gray-900 dark:text-neutral-100">
                                    {t('finance.invoices.show.total_label')}
                                </span>
                                <span className="font-black text-gray-900 dark:text-neutral-100">
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
