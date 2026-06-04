import React, { useState, useMemo, useCallback, useEffect } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import debounce from 'lodash/debounce';
import {
    HomeIcon, ChevronRightIcon, MagnifyingGlassIcon, PlusIcon,
    BeakerIcon, Squares2X2Icon, ListBulletIcon, ArrowRightIcon,
    PencilSquareIcon, TrashIcon, CurrencyDollarIcon, CheckBadgeIcon,
    InboxIcon, XMarkIcon, ArrowPathIcon
} from '@heroicons/react/24/outline';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface LabTest {
    id: number;
    code: string;
    test_name: string;
    price: number | string;
    units: string;
    is_active: boolean;
    description: string;
}

interface Props {
    tests: {
        data: LabTest[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    filters: { search?: string };
}

export default function LabTestManagement({ tests, filters }: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [selectedId, setSelectedId] = useState<number | null>(null);

    // Form logic for EDIT MODAL
    const { data, setData, put, processing, errors, reset, clearErrors } = useForm({
        test_name: '',
        code: '',
        price: 0,
        units: '',
        description: '',
        is_active: true,
    });

    // Handle Search
    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(route('tests.index'), { search: searchQuery }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(timeout);
    }, [searchQuery]);

    // Open Edit Modal
    const openEditModal = (test: LabTest) => {
        setSelectedId(test.id);
        setData({
            test_name: test.test_name,
            code: test.code,
            price: Number(test.price),
            units: test.units,
            description: test.description || '',
            is_active: !!test.is_active,
        });
        setIsEditModalOpen(true);
    };

    const handleUpdate = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('tests.update', selectedId!), {
            onSuccess: () => {
                setIsEditModalOpen(false);
                reset();
            },
            preserveScroll: true
        });
    };

    const handleDelete = (id: number) => {
        if (confirm(t('lab_tests.index.delete_confirm'))) {
            router.delete(route('tests.destroy', id), { preserveScroll: true });
        }
    };

    const stats = useMemo(() => ({
        total: tests.total,
        active: tests.data.filter(t => t.is_active).length,
        avgPrice: tests.data.reduce((acc, t) => acc + Number(t.price), 0) / (tests.data.length || 1),
    }), [tests]);

    return (
        <AppLayout headerTitle={t('lab_tests.index.title')} headerSubtitle={t('lab_tests.index.subtitle')}>
            <Head title={t('lab_tests.index.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer"><HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}</li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600">{t('lab_tests.index.test_definitions')}</li>
                    </ol>
                </nav>

                {/* Stats */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <StatCard label={t('lab_tests.index.total_tests')} value={stats.total} icon={<BeakerIcon />} color="blue" />
                    <StatCard label={t('lab_tests.index.active')} value={stats.active} icon={<CheckBadgeIcon />} color="emerald" />
                    <StatCard label={t('lab_tests.index.avg_price')} value={`$${stats.avgPrice.toFixed(2)}`} icon={<CurrencyDollarIcon />} color="amber" />
                </div>

                {/* Controls */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div className="relative flex-grow lg:max-w-md">
                        <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder={t('lab_tests.index.search_placeholder')}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('tests.create')}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition-all"
                        >
                            <PlusIcon className="w-4 h-4" /> {t('lab_tests.index.new_test')}
                        </Link>
                        <div className="flex bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-1">
                            <button onClick={() => setViewMode('grid')} className={`p-1.5 rounded-md ${viewMode === 'grid' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 dark:text-neutral-500'}`}><Squares2X2Icon className="w-5 h-5" /></button>
                            <button onClick={() => setViewMode('table')} className={`p-1.5 rounded-md ${viewMode === 'table' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 dark:text-neutral-500'}`}><ListBulletIcon className="w-5 h-5" /></button>
                        </div>
                    </div>
                </div>

                {/* Main Content Area */}
                <AnimatePresence mode="wait">
                    {tests.data.length === 0 ? (
                        /* --- EMPTY CARD --- */
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            className="flex flex-col items-center justify-center py-24 bg-white dark:bg-neutral-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-neutral-700 text-center"
                        >
                            <div className="h-20 w-20 bg-gray-50 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                                <InboxIcon className="w-10 h-10 text-gray-300" />
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">{t('lab_tests.index.no_tests')}</h3>
                            <p className="text-sm text-gray-500 dark:text-neutral-500 max-w-sm mt-2 mb-8">
                                {t('lab_tests.index.no_tests_description')}
                            </p>
                            <Link href={route('tests.create')} className="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-blue-700 transition-all">
                                {t('lab_tests.index.create_first_test')}
                            </Link>
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        <motion.div key="grid" className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {tests.data.map((test) => (
                                <div key={test.id} className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-blue-400 hover:shadow-md transition-all flex flex-col h-full relative group">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className={`h-12 w-12 rounded-lg flex items-center justify-center ${test.is_active ? 'bg-blue-50 text-blue-600' : 'bg-gray-50 text-gray-400 dark:text-neutral-500'}`}>
                                            <BeakerIcon className="w-6 h-6" />
                                        </div>
                                        <span className={`px-2 py-1 rounded-md text-[10px] font-black uppercase border ${test.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-gray-50 text-gray-500 dark:text-neutral-500 border-gray-100 dark:border-neutral-700'}`}>
                                            {test.is_active ? t('lab_tests.index.active_status') : t('lab_tests.index.inactive_status')}
                                        </span>
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 truncate">{test.test_name}</h3>
                                        <p className="text-xs font-mono text-blue-500 font-bold mb-3">{test.code}</p>
                                        <p className="text-sm text-gray-500 dark:text-neutral-500 line-clamp-2 h-10">{test.description || t('lab_tests.index.no_description')}</p>
                                    </div>
                                    <div className="pt-4 border-t border-gray-100 dark:border-neutral-700 flex items-center justify-between">
                                        <div className="text-lg font-bold text-gray-900 dark:text-neutral-100">${Number(test.price).toFixed(2)}</div>
                                        <div className="flex gap-2">
                                            <button onClick={() => openEditModal(test)} className="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all"><PencilSquareIcon className="w-4 h-4" /></button>
                                            <button onClick={() => handleDelete(test.id)} className="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-all"><TrashIcon className="w-4 h-4" /></button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    ) : (
                        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden shadow-sm">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-700 text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase">
                                    <tr>
                                        <th className="px-6 py-4 text-left">{t('lab_tests.index.test_name')}</th>
                                        <th className="px-6 py-4 text-left">{t('lab_tests.index.price')}</th>
                                        <th className="px-6 py-4 text-center">{t('lab_tests.index.status')}</th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {tests.data.map((test) => (
                                        <tr key={test.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                            <td className="px-6 py-4">
                                                <div className="font-bold text-sm text-gray-900 dark:text-neutral-100">{test.test_name}</div>
                                                <div className="text-[10px] font-mono text-gray-400 dark:text-neutral-500">{test.code}</div>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-bold text-gray-900 dark:text-neutral-100">${Number(test.price).toFixed(2)}</td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold border ${test.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-gray-50 text-gray-500 dark:text-neutral-500 border-gray-100 dark:border-neutral-700'}`}>
                                                    {test.is_active ? t('lab_tests.index.active_status') : t('lab_tests.index.inactive_status')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button onClick={() => openEditModal(test)} className="p-1 text-gray-400 dark:text-neutral-500 hover:text-blue-600 mr-2"><PencilSquareIcon className="w-5 h-5" /></button>
                                                <button onClick={() => handleDelete(test.id)} className="p-1 text-gray-400 dark:text-neutral-500 hover:text-red-600"><TrashIcon className="w-5 h-5" /></button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </AnimatePresence>

                {/* Pagination */}
                <div className="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-500">
                    <p>{t('lab_tests.index.showing_entries', { from: tests.from || 0, to: tests.to || 0, total: tests.total })}</p>
                    <div className="flex gap-1">
                        {tests.links.map((link, i) => (
                            <button
                                key={i}
                                disabled={!link.url}
                                onClick={() => router.get(link.url!, { search: searchQuery }, { preserveState: true })}
                                className={`px-3 py-1.5 border rounded-lg transition-all ${link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 dark:text-neutral-400'} ${!link.url ? 'opacity-30' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            </div>

            {/* --- EDIT MODAL --- */}
            {isEditModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onClick={() => setIsEditModalOpen(false)}></div>
                    <div className="relative bg-white dark:bg-neutral-800 rounded-xl w-full max-w-2xl shadow-xl overflow-hidden flex flex-col">
                        <div className="px-6 py-4 border-b border-gray-100 dark:border-neutral-700 flex justify-between items-center bg-gray-50 dark:bg-neutral-700">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-neutral-100">{t('lab_tests.index.edit_test')}</h3>
                            <button onClick={() => setIsEditModalOpen(false)} className="text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-400"><XMarkIcon className="w-5 h-5"/></button>
                        </div>
                        <form onSubmit={handleUpdate}>
                            <div className="p-6 grid grid-cols-2 gap-4">
                                <div className="col-span-2">
                                    <label className="block text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase mb-1">{t('lab_tests.index.test_name')}</label>
                                    <input type="text" value={data.test_name} onChange={e => setData('test_name', e.target.value)} className="w-full px-3 py-2 border rounded-lg text-sm" />
                                    {errors.test_name && <p className="text-red-500 text-[10px] mt-1">{errors.test_name}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase mb-1">{t('lab_tests.index.code')}</label>
                                    <input type="text" value={data.code} onChange={e => setData('code', e.target.value)} className="w-full px-3 py-2 border rounded-lg text-sm" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase mb-1">{t('lab_tests.index.price')}</label>
                                    <input type="number" step="0.01" value={data.price} onChange={e => setData('price', Number(e.target.value))} className="w-full px-3 py-2 border rounded-lg text-sm" />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase mb-1">{t('lab_tests.index.description')}</label>
                                    <textarea value={data.description} onChange={e => setData('description', e.target.value)} className="w-full px-3 py-2 border rounded-lg text-sm" rows={3} />
                                </div>
                            </div>
                            <div className="px-6 py-4 border-t bg-gray-50 dark:bg-neutral-700 flex justify-end gap-3">
                                <button type="button" onClick={() => setIsEditModalOpen(false)} className="px-4 py-2 text-sm font-bold text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border rounded-lg">{t('common.cancel')}</button>
                                <button type="submit" disabled={processing} className="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg flex items-center gap-2">
                                    {processing && <ArrowPathIcon className="w-4 h-4 animate-spin" />} {t('common.save_changes')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

function StatCard({ label, value, icon, color }: any) {
    const colorClasses: any = { blue: 'bg-blue-50 text-blue-600', emerald: 'bg-emerald-50 text-emerald-700', amber: 'bg-amber-50 text-amber-700' };
    return (
        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div className={`p-3 rounded-lg ${colorClasses[color]}`}>{React.cloneElement(icon as React.ReactElement, { className: "w-5 h-5" })}</div>
            <div>
                <p className="text-xl font-bold text-gray-900 dark:text-neutral-100 leading-none">{value}</p>
                <p className="text-xs font-semibold text-gray-500 dark:text-neutral-500 mt-1">{label}</p>
            </div>
        </div>
    );
}
