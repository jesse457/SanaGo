import { PlusIcon, TrashIcon, HomeIcon, ChevronRightIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useMemo, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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

export default function InvoiceCreate({ patients }: Props) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        patient_id: '' as string | number,
        invoice_date: new Date().toISOString().slice(0, 10),
        due_date: '',
        notes: '',
        items: [
            { item_type: 'consultation', description: '', quantity: 1, unit_price: 0 },
        ] as LineItem[],
    });

    const addItem = () => {
        setData('items', [...data.items, { item_type: 'consultation', description: '', quantity: 1, unit_price: 0 }]);
    };

    const removeItem = (idx: number) => {
        setData('items', data.items.filter((_, i) => i !== idx));
    };

    const updateItem = (idx: number, field: keyof LineItem, value: string | number) => {
        setData('items', data.items.map((item, i) =>
            i === idx ? { ...item, [field]: value } : item
        ));
    };

    const grandTotal = useMemo(() => {
        return data.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
    }, [data.items]);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('finance.invoices.store'));
    };

    return (
        <AppLayout headerTitle={t('finance.invoices.create.title')} headerSubtitle={t('finance.invoices.create.subtitle')}>
            <Head title={t('finance.invoices.create.head_title')} />

            <div className="max-w-5xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="hover:text-blue-600 cursor-pointer">{t('finance.invoices.create.finance')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600 font-medium">{t('finance.invoices.create.create_invoice')}</li>
                    </ol>
                </nav>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 space-y-4">
                        <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase">{t('finance.invoices.create.invoice_details')}</h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase ml-1">{t('finance.invoices.create.patient')} *</label>
                                <select
                                    value={data.patient_id}
                                    onChange={e => setData('patient_id', e.target.value)}
                                    className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold dark:text-neutral-100"
                                    required
                                >
                                    <option value="">{t('finance.invoices.create.select_patient')}</option>
                                    {patients.map(p => (
                                        <option key={p.id} value={p.id}>
                                            {p.first_name} {p.last_name} ({p.patient_uid})
                                        </option>
                                    ))}
                                </select>
                                {errors.patient_id && <p className="text-xs font-bold text-red-500 mt-1">{errors.patient_id}</p>}
                            </div>
                            <div>
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase ml-1">{t('finance.invoices.create.invoice_date')} *</label>
                                <input
                                    type="date"
                                    value={data.invoice_date}
                                    onChange={e => setData('invoice_date', e.target.value)}
                                    className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold dark:text-neutral-100"
                                    required
                                />
                                {errors.invoice_date && <p className="text-xs font-bold text-red-500 mt-1">{errors.invoice_date}</p>}
                            </div>
                            <div>
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase ml-1">{t('finance.invoices.create.due_date')}</label>
                                <input
                                    type="date"
                                    value={data.due_date}
                                    onChange={e => setData('due_date', e.target.value)}
                                    className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold dark:text-neutral-100"
                                />
                                {errors.due_date && <p className="text-xs font-bold text-red-500 mt-1">{errors.due_date}</p>}
                            </div>
                            <div>
                                <label className="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase ml-1">{t('finance.invoices.create.notes')}</label>
                                <input
                                    type="text"
                                    value={data.notes}
                                    onChange={e => setData('notes', e.target.value)}
                                    className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm dark:text-neutral-100"
                                    placeholder={t('finance.invoices.create.notes_placeholder')}
                                />
                            </div>
                        </div>
                    </div>

<div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase">{t('finance.invoices.create.line_items')}</h3>
                            <button
                                type="button"
                                onClick={addItem}
                                className="flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700"
                            >
                                <PlusIcon className="w-3.5 h-3.5" /> {t('finance.invoices.create.add_blank_item')}
                            </button>
                        </div>

                        {errors.items && <p className="text-xs font-bold text-red-500">{errors.items}</p>}

                        <div className="space-y-3">
                            {data.items.map((item, idx) => (
                                <div key={idx} className="flex items-start gap-3 p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                                    <div className="flex-1 grid grid-cols-2 md:grid-cols-5 gap-3">
                                        <div>
                                            <label className="text-[10px] font-semibold text-gray-400 uppercase">{t('finance.invoices.create.type')}</label>
                                            <select
                                                value={item.item_type}
                                                onChange={e => updateItem(idx, 'item_type', e.target.value)}
                                                className="w-full mt-0.5 px-2 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-600 rounded-lg text-xs font-bold dark:text-neutral-100"
                                            >
                                                {ITEM_TYPES.map(t => (
                                                    <option key={t} value={t}>{t.replace('_', ' ')}</option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="md:col-span-2">
                                            <label className="text-[10px] font-semibold text-gray-400 uppercase">{t('finance.invoices.create.description')}</label>
                                            <input
                                                type="text"
                                                value={item.description}
                                                onChange={e => updateItem(idx, 'description', e.target.value)}
                                                className="w-full mt-0.5 px-2 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-600 rounded-lg text-xs font-bold dark:text-neutral-100"
                                                placeholder={t('finance.invoices.create.description_placeholder')}
                                            />
                                        </div>
                                        <div>
                                            <label className="text-[10px] font-semibold text-gray-400 uppercase">{t('finance.invoices.create.qty')}</label>
                                            <input
                                                type="number"
                                                value={item.quantity}
                                                onChange={e => updateItem(idx, 'quantity', parseInt(e.target.value) || 0)}
                                                className="w-full mt-0.5 px-2 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-600 rounded-lg text-xs font-bold dark:text-neutral-100"
                                                min="1"
                                            />
                                        </div>
                                        <div>
                                            <label className="text-[10px] font-semibold text-gray-400 uppercase">{t('finance.invoices.create.unit_price')}</label>
                                            <input
                                                type="number"
                                                value={item.unit_price}
                                                onChange={e => updateItem(idx, 'unit_price', parseFloat(e.target.value) || 0)}
                                                className="w-full mt-0.5 px-2 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-600 rounded-lg text-xs font-bold dark:text-neutral-100"
                                                min="0"
                                                step="1"
                                            />
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => removeItem(idx)}
                                        disabled={data.items.length === 1}
                                        className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors disabled:opacity-30 mt-5"
                                    >
                                        <TrashIcon className="w-4 h-4" />
                                    </button>
                                </div>
                            ))}
                        </div>

                        <div className="flex justify-end pt-4 border-t border-gray-100 dark:border-neutral-700">
                            <div className="text-right">
                                <p className="text-xs text-gray-500 dark:text-neutral-400 font-semibold uppercase">{t('finance.invoices.create.grand_total')}</p>
                                <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">{grandTotal.toLocaleString()} FCFA</p>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-4">
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 disabled:opacity-50 transition-colors"
                        >
                            {processing ? t('common.creating') : t('finance.invoices.create.create_invoice_button')}
                        </button>
                        <a href={route('finance.invoices.index') as string} className="text-sm text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200">
                            {t('common.cancel')}
                        </a>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
