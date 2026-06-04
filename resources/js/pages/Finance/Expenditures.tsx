import { PlusIcon, HomeIcon, ChevronRightIcon, TrashIcon, PencilSquareIcon } from '@heroicons/react/24/outline';
import { Head, useForm, router } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Modal from '@/Components/Modal';
import { useTranslation } from '@/hooks/useTranslation';

interface Expenditure {
    id: number;
    category: string;
    description: string;
    amount: number;
    expenditure_date: string;
    vendor?: string;
    recorded_by?: { id: number; name: string };
}

interface Props { expenditures: { data: Expenditure[]; links: any[]; current_page: number; last_page: number } }

export default function ExpendituresIndex({ expenditures }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState('all');
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState<Expenditure | null>(null);

    const { data, setData, post, put, processing, reset } = useForm({
        category: 'supplies',
        description: '',
        amount: '',
        expenditure_date: new Date().toISOString().slice(0, 10),
        vendor: '',
        reference_number: '',
        notes: '',
    });

    const filtered = useMemo(() => {
        let r = expenditures.data;
        if (filter !== 'all') r = r.filter(e => e.category === filter);
        if (search) r = r.filter(e => e.description.toLowerCase().includes(search.toLowerCase()));
        return r;
    }, [expenditures.data, filter, search]);

    const openCreate = () => { setEditing(null); reset(); setModalOpen(true); };
    const openEdit = (e: Expenditure) => {
        setEditing(e);
        setData({ category: e.category, description: e.description, amount: String(e.amount), expenditure_date: e.expenditure_date, vendor: e.vendor || '', reference_number: '', notes: '' });
        setModalOpen(true);
    };

    const handleSubmit = () => {
        if (editing) {
            put(route('expenditures.update', editing.id), { onSuccess: () => setModalOpen(false) });
        } else {
            post(route('expenditures.store'), { onSuccess: () => setModalOpen(false) });
        }
    };

    const categories = ['all', 'salaries', 'supplies', 'utilities', 'maintenance', 'equipment', 'pharmaceuticals', 'administrative', 'other'];

    return (
        <AppLayout headerTitle={t('finance.expenditures.title')} headerSubtitle={t('finance.expenditures.subtitle')}>
            <Head title={t('finance.expenditures.head_title')} />
            <div className="max-w-7xl mx-auto space-y-6">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" />{t('common.home')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600 font-medium">{t('finance.expenditures.breadcrumb')}</li>
                    </ol>
                </nav>

                <div className="flex items-center justify-between gap-4">
                    <div className="flex p-1 bg-gray-100 dark:bg-neutral-800 rounded-lg flex-wrap">
                        {categories.map(c => (
                            <button key={c} onClick={() => setFilter(c)} className={`px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all ${filter === c ? 'bg-white dark:bg-neutral-700 text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'}`}>{t(`finance.expenditures.filter_${c}`)}</button>
                        ))}
                    </div>
                    <button onClick={openCreate} className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm">
                        <PlusIcon className="w-4 h-4" /> {t('finance.expenditures.add_expense')}
                    </button>
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                            <tr>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">{t('finance.expenditures.description')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">{t('finance.expenditures.category')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">{t('finance.expenditures.amount')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">{t('finance.expenditures.date')}</th>
                                <th className="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {filtered.map(e => (
                                <tr key={e.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30">
                                    <td className="px-6 py-4">
                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{e.description}</p>
                                        {e.vendor && <p className="text-xs text-gray-400">{e.vendor}</p>}
                                    </td>
                                    <td className="px-6 py-4"><span className="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300">{e.category}</span></td>
                                    <td className="px-6 py-4 text-sm font-bold text-red-600">{e.amount.toLocaleString()} FCFA</td>
                                    <td className="px-6 py-4 text-sm text-gray-500">{e.expenditure_date}</td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex justify-end gap-2">
                                            <button onClick={() => openEdit(e)} className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg"><PencilSquareIcon className="w-4 h-4" /></button>
                                            <button onClick={() => { if (confirm(t('finance.expenditures.delete_confirm'))) router.delete(route('expenditures.destroy', e.id)); }} className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon className="w-4 h-4" /></button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editing ? t('finance.expenditures.edit_expenditure') : t('finance.expenditures.add_expenditure')}>
                <form onSubmit={e => { e.preventDefault(); handleSubmit(); }} className="space-y-4">
                    <div>
                        <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('finance.expenditures.category')}</label>
                        <select value={data.category} onChange={e => setData('category', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold">
                            {categories.filter(c => c !== 'all').map(c => <option key={c} value={c}>{t(`finance.expenditures.${c}`)}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('finance.expenditures.description')}</label>
                        <input type="text" value={data.description} onChange={e => setData('description', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" required />
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('finance.expenditures.amount_fcfa')}</label>
                            <input type="number" step="0.01" value={data.amount} onChange={e => setData('amount', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" required />
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('finance.expenditures.date')}</label>
                            <input type="date" value={data.expenditure_date} onChange={e => setData('expenditure_date', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" required />
                        </div>
                    </div>
                    <div>
                        <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('finance.expenditures.vendor')}</label>
                        <input type="text" value={data.vendor} onChange={e => setData('vendor', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" />
                    </div>
                    <div className="flex gap-4 pt-2">
                        <button type="button" onClick={() => setModalOpen(false)} className="flex-1 py-3 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl font-bold text-gray-500">{t('common.cancel')}</button>
                        <button type="submit" disabled={processing} className="flex-[2] py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">{processing ? t('common.saving') : editing ? t('common.update') : t('finance.expenditures.record_expense')}</button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
