import {
    PlusIcon,
    TrashIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useMemo } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
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
    const { data, setData, post, processing, errors } = useForm({
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
        post(route('finance.invoices.store'));
    };

    return (
        <AppLayout
            headerTitle={t('finance.invoices.create.title')}
            headerSubtitle={t('finance.invoices.create.subtitle')}
        >
            <Head title={t('finance.invoices.create.head_title')} />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        {
                            label: t('finance.invoices.create.finance'),
                            href: route('finance.invoices.index'),
                        },
                        { label: t('finance.invoices.create.create_invoice') },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <PageSection
                        icon={DocumentTextIcon}
                        title={t('finance.invoices.create.invoice_details')}
                    >
                        <div className="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                            <FormSelect
                                name="patient_id"
                                label={t('finance.invoices.create.patient')}
                                value={data.patient_id}
                                onChange={(v) => setData('patient_id', v)}
                                errors={errors}
                                required
                                options={patients.map((p) => ({
                                    value: String(p.id),
                                    label: `${p.first_name} ${p.last_name} (${p.patient_uid})`,
                                }))}
                                placeholder={t(
                                    'finance.invoices.create.select_patient',
                                )}
                            />
                            <FormInput
                                name="invoice_date"
                                label={t(
                                    'finance.invoices.create.invoice_date',
                                )}
                                type="date"
                                value={data.invoice_date}
                                onChange={(v) => setData('invoice_date', v)}
                                errors={errors}
                                required
                            />
                            <FormInput
                                name="due_date"
                                label={t('finance.invoices.create.due_date')}
                                type="date"
                                value={data.due_date}
                                onChange={(v) => setData('due_date', v)}
                                errors={errors}
                            />
                            <FormInput
                                name="notes"
                                label={t('finance.invoices.create.notes')}
                                type="text"
                                value={data.notes}
                                onChange={(v) => setData('notes', v)}
                                placeholder={t(
                                    'finance.invoices.create.notes_placeholder',
                                )}
                            />
                        </div>
                    </PageSection>

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
                                        <FormSelect
                                            name={`item_${idx}_type`}
                                            value={item.item_type}
                                            onChange={(v) =>
                                                updateItem(idx, 'item_type', v)
                                            }
                                            options={ITEM_TYPE_OPTIONS}
                                        />
                                        <div className="md:col-span-2">
                                            <FormInput
                                                name={`item_${idx}_description`}
                                                value={item.description}
                                                onChange={(v) =>
                                                    updateItem(
                                                        idx,
                                                        'description',
                                                        v,
                                                    )
                                                }
                                                placeholder={t(
                                                    'finance.invoices.create.description_placeholder',
                                                )}
                                            />
                                        </div>
                                        <FormInput
                                            name={`item_${idx}_qty`}
                                            type="number"
                                            value={item.quantity}
                                            onChange={(v) =>
                                                updateItem(
                                                    idx,
                                                    'quantity',
                                                    parseInt(v) || 0,
                                                )
                                            }
                                            min="1"
                                        />
                                        <FormInput
                                            name={`item_${idx}_price`}
                                            type="number"
                                            value={item.unit_price}
                                            onChange={(v) =>
                                                updateItem(
                                                    idx,
                                                    'unit_price',
                                                    parseFloat(v) || 0,
                                                )
                                            }
                                            min="0"
                                            step="1"
                                        />
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
                                        {t(
                                            'finance.invoices.create.grand_total',
                                        )}
                                    </p>
                                    <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">
                                        {grandTotal.toLocaleString()} FCFA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-4">
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                        >
                            {processing
                                ? t('common.creating')
                                : t(
                                      'finance.invoices.create.create_invoice_button',
                                  )}
                        </button>
                        <Link
                            href={route('finance.invoices.index')}
                            className="text-sm text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                        >
                            {t('common.cancel')}
                        </Link>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
