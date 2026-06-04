import {
    PlusIcon, MagnifyingGlassIcon, TrashIcon, PencilSquareIcon,
    XMarkIcon, CubeIcon, ExclamationTriangleIcon, ClockIcon,
    ArchiveBoxIcon, InboxIcon, ArchiveBoxXMarkIcon,
    ArrowPathIcon, ChevronRightIcon, HomeIcon, BeakerIcon
} from '@heroicons/react/24/outline';
import { Head, router, useForm, Link } from '@inertiajs/react';
import React, { useState, useMemo, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface DrugBatch {
    id: number;
    batch_number: string;
    expiry_date: string;
    current_quantity: number;
    purchase_price: number;
    status: string;
}

interface Drug {
    id: number;
    name: string;
    image_url: string | null;
    dosage_units: string;
    min_stock_level: number;
    description: string;
    total_stock: number;
    batches: DrugBatch[];
}

interface Props {
    drugs: Drug[];
    filters: { search?: string; tab?: string };
}

export default function DrugInventory({ drugs = [], filters }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [activeTab, setActiveTab] = useState(filters.tab || 'all');
    const [editModalOpen, setEditModalOpen] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [depleteModalOpen, setDepleteModalOpen] = useState(false);

    const [selectedItem, setSelectedItem] = useState<Drug | null>(null);
    const [selectedBatch, setSelectedBatch] = useState<DrugBatch | null>(null);

    const drugForm = useForm({
        name: '',
        dosage_units: '',
        min_stock_level: 0,
        description: '',
    });

    const batchForm = useForm({
        batch_number: '',
        expiry_date: '',
        quantity: '',
        purchase_price: ''
    });

    // Compute dynamic dashboard metrics
    const stats = useMemo(() => {
        const totalMedications = drugs.length;
        const lowStockCount = drugs.filter(d => d.total_stock <= d.min_stock_level && d.total_stock > 0).length;
        const outOfStockCount = drugs.filter(d => d.total_stock === 0).length;

        const activeBatches = drugs.flatMap(d => d.batches || []);
        const expiringWarningCount = activeBatches.filter(b => {
            if (b.current_quantity === 0 || b.status === 'depleted') return false;
            const daysLeft = (new Date(b.expiry_date).getTime() - new Date().getTime()) / (1000 * 3600 * 24);
            return daysLeft <= 30;
        }).length;

        return { totalMedications, lowStockCount, outOfStockCount, expiringWarningCount };
    }, [drugs]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(route('drugs.index'),
                { search: searchQuery, tab: activeTab },
                { preserveState: true, replace: true, preserveScroll: true }
            );
        }, 400);
        return () => clearTimeout(timeout);
    }, [searchQuery, activeTab]);

    const openEditModal = (drug: Drug) => {
        setSelectedItem(drug);
        drugForm.setData({
            name: drug.name,
            dosage_units: drug.dosage_units,
            min_stock_level: drug.min_stock_level,
            description: drug.description || '',
        });
        setEditModalOpen(true);
    };

    const handleUpdateDrug = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedItem) return;
        drugForm.put(route('drugs.update', selectedItem.id), {
            onSuccess: () => setEditModalOpen(false)
        });
    };

    const handleAddBatch = () => {
        if (!selectedItem) return;
        batchForm.put(route('drugs.update', selectedItem.id), {
            onSuccess: () => batchForm.reset(),
            preserveScroll: true
        });
    };

    const handleDelete = () => {
        if (!selectedItem) return;
        router.delete(route('drugs.destroy', selectedItem.id), {
            onSuccess: () => setDeleteModalOpen(false)
        });
    };

    const handleDeplete = () => {
        if (!selectedBatch) return;
        router.put(route('batches.deplete', selectedBatch.id), {}, {
            onSuccess: () => {
                setDepleteModalOpen(false);
                setSelectedBatch(null);
            },
            preserveScroll: true
        });
    };

    const allBatches = useMemo(() => {
        return (drugs || []).flatMap(d => (d.batches || []).map(b => ({
            ...b, drugName: d.name, drugDosage: d.dosage_units, drugId: d.id
        }))).sort((a, b) => new Date(a.expiry_date).getTime() - new Date(b.expiry_date).getTime());
    }, [drugs]);

    const getStockStatus = (drug: Drug) => {
        if (drug.total_stock === 0) return { label: t('outOfStock'), class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-950' };
        if (drug.total_stock <= drug.min_stock_level) return { label: t('lowStock'), class: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-950' };
        return { label: t('inStock'), class: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-950' };
    };

    const getBatchStatus = (batch: DrugBatch) => {
        if (batch.current_quantity === 0 || batch.status === 'depleted') return { label: t('depleted'), class: 'bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400 border-gray-200 dark:border-neutral-600' };
        const expDate = new Date(batch.expiry_date);
        const daysLeft = (expDate.getTime() - new Date().getTime()) / (1000 * 3600 * 24);
        if (daysLeft < 0) return { label: t('expired'), class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-950' };
        if (daysLeft <= 30) return { label: t('warning'), class: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-950' };
        return { label: t('valid'), class: 'bg-slate-50 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 border-slate-200 dark:border-neutral-700' };
    };

    const formatDate = (dateStr?: string) => dateStr ? new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '—';

    return (
        <AppLayout headerTitle={t('manageDrugs')} headerSubtitle={t('inventoryManagementBatchTracking')}>
            <Head title={t('drugInventory')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">
                {/* 1. Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 transition-colors cursor-pointer">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('home')}
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-bold">{t('drugInventory')}</li>
                    </ol>
                </nav>

                {/* 2. Top Metric Blocks */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label={t('totalFormulations')} value={stats.totalMedications} icon={<BeakerIcon />} color="blue" />
                    <StatCard label={t('lowStockItems')} value={stats.lowStockCount} icon={<ExclamationTriangleIcon />} color="amber" />
                    <StatCard label={t('outOfStockAlerts')} value={stats.outOfStockCount} icon={<ArchiveBoxXMarkIcon />} color="rose" />
                    <StatCard label={t('expiringWarningBatches')} value={stats.expiringWarningCount} icon={<ClockIcon />} color="rose" />
                </div>

                {/* 3. Controls / Filtering Layout */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div className="flex p-1 bg-gray-100 dark:bg-neutral-850 rounded-xl w-fit">
                        <TabButton active={activeTab === 'all'} onClick={() => setActiveTab('all')} label={t('allDrugs')} icon={<CubeIcon className="w-4 h-4"/>} />
                        <TabButton active={activeTab === 'low-stock'} onClick={() => setActiveTab('low-stock')} label={t('lowStock')} icon={<ExclamationTriangleIcon className="w-4 h-4"/>} />
                        <TabButton active={activeTab === 'expiring'} onClick={() => setActiveTab('expiring')} label={t('expiring')} icon={<ClockIcon className="w-4 h-4"/>} />
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t('searchDrugs')}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="pl-9 pr-4 py-2 w-48 sm:w-64 bg-white dark:bg-neutral-800 border border-gray-250 dark:border-neutral-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all text-gray-900 dark:text-neutral-100"
                            />
                        </div>
                        <Link href={route('drugs.create')} className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition-all whitespace-nowrap">
                            <PlusIcon className="w-4 h-4" /> {t('addDrug')}
                        </Link>
                    </div>
                </div>

                {/* 4. Main Medication Workspace */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    {drugs.length > 0 ? (
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 dark:bg-neutral-700 text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wider">
                                <tr>
                                    <th className="px-6 py-4 text-left">{t('medication')}</th>
                                    <th className="px-6 py-4 text-right">{t('stock')}</th>
                                    <th className="px-6 py-4 text-center">{t('status')}</th>
                                    <th className="px-6 py-4 text-left">{t('earliestExpiry')}</th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                {drugs.map((drug) => {
                                    const status = getStockStatus(drug);
                                    const earliestBatch = drug.batches?.find(b => b.current_quantity > 0);
                                    return (
                                        <tr key={drug.id} className="hover:bg-gray-50 dark:hover:bg-neutral-750 transition-colors group">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 flex-shrink-0 rounded-lg overflow-hidden border border-gray-200 dark:border-neutral-700">
                                                        {drug.image_url ? (
                                                            <img src={drug.image_url} alt="" className="h-full w-full object-cover" />
                                                        ) : (
                                                            <div className="h-full w-full bg-blue-50 dark:bg-neutral-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-sm">
                                                                {drug.name.charAt(0)}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">{drug.name}</div>
                                                        <div className="text-xs text-gray-500 dark:text-neutral-400">{drug.dosage_units}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className={`px-6 py-4 whitespace-nowrap text-right font-mono text-sm font-bold ${drug.total_stock === 0 ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-neutral-100'}`}>{drug.total_stock}</td>
                                            <td className="px-6 py-4 whitespace-nowrap text-center">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${status.class}`}>{status.label}</span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                {earliestBatch ? (
                                                    <div className="flex flex-col">
                                                        <span className={new Date(earliestBatch.expiry_date) < new Date() ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-900 dark:text-neutral-100'}>{formatDate(earliestBatch.expiry_date)}</span>
                                                        <span className="text-xs text-gray-455 dark:text-neutral-500">{earliestBatch.batch_number}</span>
                                                    </div>
                                                ) : <span className="text-gray-400 dark:text-neutral-500 italic text-xs">{t('noActiveStock')}</span>}
                                            </td>
                                            <td className="px-6 py-4 text-right whitespace-nowrap">
                                                <div className="flex justify-end gap-1.5">
                                                    <button onClick={() => openEditModal(drug)} className="p-2 text-gray-450 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg transition-colors"><PencilSquareIcon className="w-4 h-4"/></button>
                                                    <button onClick={() => { setSelectedItem(drug); setDeleteModalOpen(true); }} className="p-2 text-gray-450 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors"><TrashIcon className="w-4 h-4"/></button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    ) : (
                        <div className="flex flex-col items-center justify-center py-20 px-4">
                            <InboxIcon className="w-12 h-12 text-gray-300 dark:text-neutral-600 mb-4" />
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">{t('noDrugsFound')}</h3>
                            <p className="text-gray-500 dark:text-neutral-400 mt-1 mb-6 text-sm">{searchQuery ? t('tryRefiningSearch') : t('beginCreatingFormulation')}</p>
                            <Link href={route('drugs.create')} className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm">
                                <PlusIcon className="w-4 h-4" /> {t('addMedication')}
                            </Link>
                        </div>
                    )}
                </div>

                {/* Detailed Batch Workspace */}
                {allBatches.length > 0 && (
                    <div className="mt-8 bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700 flex items-center justify-between">
                            <h3 className="text-xs font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wider">{t('detailedBatchTracking')}</h3>
                            <ArchiveBoxIcon className="w-5 h-5 text-gray-400" />
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead className="bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wider">
                                    <tr>
                                        <th className="px-6 py-3">{t('medication')}</th>
                                        <th className="px-6 py-3">{t('batchNumberShort')}</th>
                                        <th className="px-6 py-3">{t('expiry')}</th>
                                        <th className="px-6 py-3 text-right">{t('qty')}</th>
                                        <th className="px-6 py-3 text-center">{t('status')}</th>
                                        <th className="px-6 py-3 text-right">{t('actions')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-150 dark:divide-neutral-700">
                                    {allBatches.map((batch, idx) => {
                                        const bStatus = getBatchStatus(batch);
                                        const isDepleted = batch.current_quantity === 0 || batch.status === 'depleted';
                                        return (
                                            <tr key={`${batch.id}-${idx}`} className={`hover:bg-gray-50 dark:hover:bg-neutral-750 transition-all ${isDepleted ? 'bg-gray-50/50 dark:bg-neutral-800/10' : ''}`}>
                                                <td className="px-6 py-3">
                                                    <div className={`text-sm font-bold ${isDepleted ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-neutral-100'}`}>{batch.drugName}</div>
                                                    <div className="text-xs text-gray-500 dark:text-neutral-400">{batch.drugDosage}</div>
                                                </td>
                                                <td className="px-6 py-3 text-sm text-gray-600 dark:text-neutral-400 font-mono">{batch.batch_number}</td>
                                                <td className="px-6 py-3 text-sm text-gray-900 dark:text-neutral-100">{formatDate(batch.expiry_date)}</td>
                                                <td className={`px-6 py-3 text-right font-mono font-bold text-sm ${isDepleted ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-neutral-100'}`}>{batch.current_quantity}</td>
                                                <td className="px-6 py-3 text-center">
                                                    <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${bStatus.class}`}>{bStatus.label}</span>
                                                </td>
                                                <td className="px-6 py-3 text-right">
                                                    {!isDepleted && (
                                                        <button
                                                            onClick={() => { setSelectedBatch(batch as any); setDepleteModalOpen(true); }}
                                                            className="text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 p-2 hover:bg-orange-50 dark:hover:bg-orange-950/40 rounded-lg transition-all inline-block"
                                                            title={t('depleteStock')}
                                                        >
                                                            <ArchiveBoxXMarkIcon className="w-5 h-5" />
                                                        </button>
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>

            {/* Modal Animations & Overlay Elements */}
            <AnimatePresence>
                {/* 1. Edit / Batch Addition Modal */}
                {editModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                            onClick={() => setEditModalOpen(false)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95, y: 15 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 15 }}
                            className="relative bg-white dark:bg-neutral-800 rounded-xl w-full max-w-2xl shadow-xl border border-gray-200 dark:border-neutral-700 overflow-hidden flex flex-col max-h-[90vh] z-10"
                        >
                            <div className="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700 flex justify-between items-center">
                                <h3 className="text-base font-bold uppercase tracking-wider text-gray-900 dark:text-neutral-100">{t('edit')} {selectedItem?.name}</h3>
                                <button onClick={() => setEditModalOpen(false)} className="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300"><XMarkIcon className="w-5 h-5"/></button>
                            </div>
                            <div className="p-6 overflow-y-auto space-y-8">
                                <div className="space-y-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-1">{t('drugName')}</label>
                                            <input type="text" value={drugForm.data.name} onChange={e => drugForm.setData('name', e.target.value)} className="w-full px-3 py-2 border border-gray-250 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:bg-neutral-700 dark:text-neutral-100" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-1">{t('dosageUnits')}</label>
                                            <input type="text" value={drugForm.data.dosage_units} onChange={e => drugForm.setData('dosage_units', e.target.value)} className="w-full px-3 py-2 border border-gray-250 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:bg-neutral-700 dark:text-neutral-100" />
                                        </div>
                                    </div>
                                    <div className="w-1/2">
                                        <label className="block text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-1">{t('minStockLevel')}</label>
                                        <input type="number" value={drugForm.data.min_stock_level} onChange={e => drugForm.setData('min_stock_level', parseInt(e.target.value))} className="w-full px-3 py-2 border border-gray-250 dark:border-neutral-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:bg-neutral-700 dark:text-neutral-100" />
                                    </div>
                                    <button onClick={handleUpdateDrug} disabled={drugForm.processing} className="px-4 py-2 bg-blue-600 text-white text-xs font-black uppercase tracking-wider rounded-lg shadow-md hover:bg-blue-700 transition-all disabled:opacity-50">
                                        {drugForm.processing ? t('updating') : t('updateInformation')}
                                    </button>
                                </div>

                                <div className="border-t dark:border-neutral-750 pt-6">
                                    <h4 className="text-xs font-black uppercase tracking-widest mb-4 flex items-center gap-2 text-emerald-600 dark:text-emerald-450">
                                        <PlusIcon className="w-4 h-4"/> {t('addNewStockBatch')}
                                    </h4>
                                    <div className="grid grid-cols-12 gap-3 bg-emerald-50/50 dark:bg-emerald-950/10 p-4 rounded-xl border border-emerald-100 dark:border-emerald-950">
                                        <div className="col-span-3">
                                            <label className="text-[10px] uppercase font-bold text-emerald-800 dark:text-emerald-450">{t('batchNumberShort')}</label>
                                            <input type="text" placeholder={t('ref001')} value={batchForm.data.batch_number} onChange={e => batchForm.setData('batch_number', e.target.value)} className="w-full text-xs p-2 border border-emerald-200 dark:border-emerald-800 rounded outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-neutral-100" />
                                        </div>
                                        <div className="col-span-3">
                                            <label className="text-[10px] uppercase font-bold text-emerald-800 dark:text-emerald-450">{t('expiry')}</label>
                                            <input type="date" value={batchForm.data.expiry_date} onChange={e => batchForm.setData('expiry_date', e.target.value)} className="w-full text-xs p-2 border border-emerald-200 dark:border-emerald-800 rounded outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-neutral-100" />
                                        </div>
                                        <div className="col-span-2">
                                            <label className="text-[10px] uppercase font-bold text-emerald-800 dark:text-emerald-455">{t('qty')}</label>
                                            <input type="number" placeholder="0" value={batchForm.data.quantity} onChange={e => batchForm.setData('quantity', e.target.value)} className="w-full text-xs p-2 border border-emerald-200 dark:border-emerald-800 rounded outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-neutral-100" />
                                        </div>
                                        <div className="col-span-2">
                                            <label className="text-[10px] uppercase font-bold text-emerald-800 dark:text-emerald-455">{t('price')}</label>
                                            <input type="number" placeholder="0.00" value={batchForm.data.purchase_price} onChange={e => batchForm.setData('purchase_price', e.target.value)} className="w-full text-xs p-2 border border-emerald-200 dark:border-emerald-800 rounded outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-neutral-700 dark:text-neutral-100" />
                                        </div>
                                        <div className="col-span-2 flex items-end">
                                            <button type="button" onClick={handleAddBatch} disabled={batchForm.processing} className="w-full bg-emerald-600 text-white py-2 text-xs font-bold rounded hover:bg-emerald-700 disabled:opacity-50 transition-colors">{t('add')}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </motion.div>
                    </div>
                )}

                {/* 2. Depletion Confirmation Modal */}
                {depleteModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                            onClick={() => setDepleteModalOpen(false)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95 }}
                            animate={{ opacity: 1, scale: 1 }}
                            exit={{ opacity: 0, scale: 0.95 }}
                            className="relative bg-white dark:bg-neutral-800 rounded-xl w-full max-w-md shadow-xl border border-gray-200 dark:border-neutral-700 p-6 text-center z-10"
                        >
                            <div className="mx-auto w-12 h-12 bg-orange-100 dark:bg-orange-950/20 rounded-full flex items-center justify-center mb-4 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-900">
                                <ArchiveBoxXMarkIcon className="w-6 h-6" />
                            </div>
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">{t('markBatchDepleted')}</h3>
                            <p className="text-sm text-gray-500 dark:text-neutral-400 mt-2">
                                {t('setsStockToZero')} <span className="font-mono font-bold text-gray-900 dark:text-neutral-100">{selectedBatch?.batch_number}</span>. {t('cannotBeUndone')}
                            </p>
                            <div className="flex gap-3 mt-6">
                                <button onClick={() => setDepleteModalOpen(false)} className="flex-1 px-4 py-2 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm font-bold hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors dark:text-neutral-300">{t('cancel')}</button>
                                <button onClick={handleDeplete} className="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-bold hover:bg-orange-700 transition-colors">{t('confirmDepletion')}</button>
                            </div>
                        </motion.div>
                    </div>
                )}

                {/* 3. Delete Medication Modal */}
                {deleteModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                            onClick={() => setDeleteModalOpen(false)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95 }}
                            animate={{ opacity: 1, scale: 1 }}
                            exit={{ opacity: 0, scale: 0.95 }}
                            className="relative bg-white dark:bg-neutral-800 rounded-xl w-full max-w-md shadow-xl border border-gray-200 dark:border-neutral-700 p-6 text-center z-10"
                        >
                            <div className="mx-auto w-12 h-12 bg-red-100 dark:bg-red-950/20 rounded-full flex items-center justify-center mb-4 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900">
                                <ExclamationTriangleIcon className="w-6 h-6" />
                            </div>
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">{t('deleteMedication')}</h3>
                            <p className="text-sm text-gray-500 dark:text-neutral-400 mt-2">{t('allDataBatchLogsRemoved')} <b className="dark:text-neutral-100">{selectedItem?.name}</b></p>
                            <div className="flex gap-3 mt-6">
                                <button onClick={() => setDeleteModalOpen(false)} className="flex-1 px-4 py-2 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm font-bold hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors dark:text-neutral-300">{t('cancel')}</button>
                                <button onClick={handleDelete} className="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-colors">{t('deleteMedication')}</button>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </AppLayout>
    );
}

function TabButton({ active, onClick, label, icon }: any) {
    return (
        <button
            onClick={onClick}
            className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all ${
                active
                    ? 'bg-white dark:bg-neutral-700 text-blue-600 dark:text-blue-400 shadow-sm'
                    : 'text-gray-500 hover:text-gray-950 dark:hover:text-neutral-300'
            }`}
        >
            {icon} {label}
        </button>
    );
}

function StatCard({ label, value, icon, color }: any) {
    const colorClasses: any = {
        blue: 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/10 dark:text-blue-400 dark:border-blue-950',
        amber: 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/10 dark:text-amber-400 dark:border-amber-950',
        rose: 'bg-red-50 text-red-650 border-red-100 dark:bg-red-900/10 dark:text-red-400 dark:border-red-950'
    };
    return (
        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div className={`p-3 rounded-lg border ${colorClasses[color]}`}>
                {React.cloneElement(icon as React.ReactElement, { className: "w-5 h-5" })}
            </div>
            <div>
                <p className="text-lg font-bold text-gray-955 dark:text-neutral-55 leading-none">{value}</p>
                <p className="text-xs font-semibold text-gray-550 dark:text-neutral-500 mt-1.5">{label}</p>
            </div>
        </div>
    );
}
