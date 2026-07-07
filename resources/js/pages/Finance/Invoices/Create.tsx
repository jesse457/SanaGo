import {
    PlusIcon,
    TrashIcon,
    DocumentTextIcon,
    HomeIcon,
    ChevronRightIcon,
    ArrowPathIcon,
    CheckIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useMemo, useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
}

interface LineItem {
    item_type: string;
    description: string;
    quantity: number;
    unit_price: number;
}

interface Props {
    patients: Patient[];
}

const ITEM_TYPES = [
    'consultation',
    'lab_test',
    'radiology',
    'surgery',
    'medication',
    'supply',
    'admission',
    'emergency',
    'other',
];

const ITEM_TYPE_OPTIONS = ITEM_TYPES.map((t) => ({
    value: t,
    label: t.replace('_', ' '),
}));

export default function InvoiceCreate({ patients }: Props) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: '',
        invoice_date: new Date().toISOString().slice(0, 10),
        due_date: '',
        notes: '',
        items: [
            {
                item_type: 'consultation',
                description: '',
                quantity: 1,
                unit_price: 0,
            },
        ] as LineItem[],
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const addItem = () => {
        setData('items', [
            ...data.items,
            {
                item_type: 'consultation',
                description: '',
                quantity: 1,
                unit_price: 0,
            },
        ]);
    };

    const removeItem = (idx: number) => {
        setData(
            'items',
            data.items.filter((_, i) => i !== idx),
        );
    };

    const updateItem = (
        idx: number,
        field: keyof LineItem,
        value: string | number,
    ) => {
        setData(
            'items',
            data.items.map((item, i) =>
                i === idx ? { ...item, [field]: value } : item,
            ),
        );
    };

    const grandTotal = useMemo(() => {
        return data.items.reduce(
            (sum, item) => sum + item.quantity * item.unit_price,
            0,
        );
    }, [data.items]);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('finance.invoices.store'), {
            onSuccess: () => {
                setSuccessMessage(t('invoiceCreatedSuccessfully'));
                reset();
                setData('invoice_date', new Date().toISOString().slice(0, 10));
                setData('items', [
                    {
                        item_type: 'consultation',
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                    },
                ]);
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('finance.invoices.create.title')}
            headerSubtitle={t('finance.invoices.create.subtitle')}
        >
            <Head title={t('finance.invoices.create.head_title')} />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('finance.invoices.create.finance')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('finance.invoices.create.create_invoice')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <h3 className="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    {t('common.success')}
                                </h3>
                                <div className="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                    {successMessage}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSuccessMessage(null)}
                            className="rounded-lg p-1.5 text-emerald-500 transition hover:text-emerald-700 dark:hover:text-emerald-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <div className="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div className="flex items-center gap-3">
                            <ExclamationTriangleIcon className="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('common.error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage ||
                                        t('common.someFieldsRequireAttention')}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setErrorMessage(null)}
                            className="rounded-lg p-1.5 text-red-500 transition hover:text-red-700 dark:hover:text-red-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Invoice Details Card */}
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="p-6">
                            <div className="space-y-4">
                                <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                    <DocumentTextIcon className="h-5 w-5" />
                                    {t('finance.invoices.create.invoice_details')}
                                </h3>
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div className="space-y-1">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                            {t('finance.invoices.create.patient')}{' '}
                                            <span className="text-red-500">*</span>
                                        </label>
                                        <select
                                            value={data.patient_id}
                                            onChange={(e) =>
                                                setData('patient_id', e.target.value)
                                            }
                                            className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.patient_id ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        >
                                            <option value="">
                                                {t('finance.invoices.create.select_patient')}
                                            </option>
                                            {patients.map((p) => (
                                                <option key={p.id} value={String(p.id)}>
                                                    {p.first_name} {p.last_name} ({p.patient_uid})
                                                </option>
                                            ))}
                                        </select>
                                        {errors.patient_id && (
                                            <p className="mt-1 text-xs text-red-500">{errors.patient_id}</p>
                                        )}
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                            {t('finance.invoices.create.invoice_date')}{' '}
                                            <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            value={data.invoice_date}
                                            onChange={(e) =>
                                                setData('invoice_date', e.target.value)
                                            }
                                            className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.invoice_date ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                        {errors.invoice_date && (
                                            <p className="mt-1 text-xs text-red-500">{errors.invoice_date}</p>
                                        )}
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                            {t('finance.invoices.create.due_date')}
                                        </label>
                                        <input
                                            type="date"
                                            value={data.due_date}
                                            onChange={(e) =>
                                                setData('due_date', e.target.value)
                                            }
                                            className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.due_date ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                        {errors.due_date && (
                                            <p className="mt-1 text-xs text-red-500">{errors.due_date}</p>
                                        )}
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                            {t('finance.invoices.create.notes')}
                                        </label>
                                        <input
                                            type="text"
                                            value={data.notes}
                                            onChange={(e) =>
                                                setData('notes', e.target.value)
                                            }
                                            placeholder={t('finance.invoices.create.notes_placeholder')}
                                            className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.notes ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                        {errors.notes && (
                                            <p className="mt-1 text-xs text-red-500">{errors.notes}</p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Line Items Card */}
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 p-6 dark:border-neutral-700 dark:bg-neutral-700/30">
                            <h3 className="text-sm font-bold text-gray-900 uppercase dark:text-neutral-100">
                                {t('finance.invoices.create.line_items')}
                            </h3>
                            <button
                                type="button"
                                onClick={addItem}
                                className="flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700"
                            >
                                <PlusIcon className="h-3.5 w-3.5" />{' '}
                                {t('finance.invoices.create.add_blank_item')}
                            </button>
                        </div>

                        {errors.items && (
                            <p className="px-6 pt-4 text-xs font-bold text-red-500">
                                {errors.items}
                            </p>
                        )}

                        <div className="space-y-3 p-6">
                            {data.items.map((item, idx) => (
                                <div
                                    key={idx}
                                    className="flex items-start gap-3 rounded-xl bg-gray-50 p-4 dark:bg-neutral-700/50"
                                >
                                    <div className="grid flex-1 grid-cols-2 gap-3 md:grid-cols-5">
                                        <div className="space-y-1">
                                            <select
                                                value={item.item_type}
                                                onChange={(e) =>
                                                    updateItem(idx, 'item_type', e.target.value)
                                                }
                                                className="w-full rounded-lg border border-gray-300 py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            >
                                                {ITEM_TYPE_OPTIONS.map((opt) => (
                                                    <option key={opt.value} value={opt.value}>
                                                        {opt.label}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="md:col-span-2 space-y-1">
                                            <input
                                                type="text"
                                                value={item.description}
                                                onChange={(e) =>
                                                    updateItem(idx, 'description', e.target.value)
                                                }
                                                placeholder={t('finance.invoices.create.description_placeholder')}
                                                className="w-full rounded-lg border border-gray-300 py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <input
                                                type="number"
                                                value={item.quantity}
                                                onChange={(e) =>
                                                    updateItem(idx, 'quantity', parseInt(e.target.value) || 0)
                                                }
                                                min="1"
                                                className="w-full rounded-lg border border-gray-300 py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <input
                                                type="number"
                                                value={item.unit_price}
                                                onChange={(e) =>
                                                    updateItem(idx, 'unit_price', parseFloat(e.target.value) || 0)
                                                }
                                                min="0"
                                                step="1"
                                                className="w-full rounded-lg border border-gray-300 py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => removeItem(idx)}
                                        disabled={data.items.length === 1}
                                        className="mt-6 rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 disabled:opacity-30 dark:hover:bg-red-900/20"
                                    >
                                        <TrashIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            ))}
                        </div>

                        <div className="border-t border-gray-100 px-6 pt-2 pb-6 dark:border-neutral-700">
                            <div className="flex justify-end">
                                <div className="text-right">
                                    <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('finance.invoices.create.grand_total')}
                                    </p>
                                    <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">
                                        {grandTotal.toLocaleString()} FCFA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Form Actions */}
                    <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                        <Link
                            href={route('finance.invoices.index')}
                            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.cancel')}
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {processing ? (
                                <span className="flex items-center gap-2">
                                    <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                    {t('common.creating')}...
                                </span>
                            ) : (
                                <span className="flex items-center gap-2">
                                    <CheckIcon className="h-4 w-4" />{' '}
                                    {t('finance.invoices.create.create_invoice_button')}
                                </span>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
