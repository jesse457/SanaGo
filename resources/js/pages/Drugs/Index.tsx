import {
    PlusIcon,
    MagnifyingGlassIcon,
    TrashIcon,
    PencilSquareIcon,
    XMarkIcon,
    CubeIcon,
    ExclamationTriangleIcon,
    ClockIcon,
    ArchiveBoxIcon,
    InboxIcon,
    ArchiveBoxXMarkIcon,
    ArrowPathIcon,
    ChevronRightIcon,
    HomeIcon,
    BeakerIcon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState, useMemo, useEffect } from 'react';
import StatCard from '@/Components/StatCard';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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
        purchase_price: '',
    });

    // Compute dynamic dashboard metrics
    const stats = useMemo(() => {
        const totalMedications = drugs.length;
        const lowStockCount = drugs.filter(
            (d) => d.total_stock <= d.min_stock_level && d.total_stock > 0,
        ).length;
        const outOfStockCount = drugs.filter((d) => d.total_stock === 0).length;

        const activeBatches = drugs.flatMap((d) => d.batches || []);
        const expiringWarningCount = activeBatches.filter((b) => {
            if (b.current_quantity === 0 || b.status === 'depleted') {
                return false;
            }

            const daysLeft =
                (new Date(b.expiry_date).getTime() - new Date().getTime()) /
                (1000 * 3600 * 24);

            return daysLeft <= 30;
        }).length;

        return {
            totalMedications,
            lowStockCount,
            outOfStockCount,
            expiringWarningCount,
        };
    }, [drugs]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(
                route('drugs.index'),
                { search: searchQuery, tab: activeTab },
                { preserveState: true, replace: true, preserveScroll: true },
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

        if (!selectedItem) {
            return;
        }

        drugForm.put(route('drugs.update', selectedItem.id), {
            onSuccess: () => setEditModalOpen(false),
        });
    };

    const handleAddBatch = () => {
        if (!selectedItem) {
            return;
        }

        batchForm.put(route('drugs.update', selectedItem.id), {
            onSuccess: () => batchForm.reset(),
            preserveScroll: true,
        });
    };

    const handleDelete = () => {
        if (!selectedItem) {
            return;
        }

        router.delete(route('drugs.destroy', selectedItem.id), {
            onSuccess: () => setDeleteModalOpen(false),
        });
    };

    const handleDeplete = () => {
        if (!selectedBatch) {
            return;
        }

        router.put(
            route('batches.deplete', selectedBatch.id),
            {},
            {
                onSuccess: () => {
                    setDepleteModalOpen(false);
                    setSelectedBatch(null);
                },
                preserveScroll: true,
            },
        );
    };

    const allBatches = useMemo(() => {
        return (drugs || [])
            .flatMap((d) =>
                (d.batches || []).map((b) => ({
                    ...b,
                    drugName: d.name,
                    drugDosage: d.dosage_units,
                    drugId: d.id,
                })),
            )
            .sort(
                (a, b) =>
                    new Date(a.expiry_date).getTime() -
                    new Date(b.expiry_date).getTime(),
            );
    }, [drugs]);

    const getStockStatus = (drug: Drug) => {
        if (drug.total_stock === 0) {
            return {
                label: t('outOfStock'),
                class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-950',
            };
        }

        if (drug.total_stock <= drug.min_stock_level) {
            return {
                label: t('lowStock'),
                class: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-950',
            };
        }

        return {
            label: t('inStock'),
            class: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-950',
        };
    };

    const getBatchStatus = (batch: DrugBatch) => {
        if (batch.current_quantity === 0 || batch.status === 'depleted') {
            return {
                label: t('depleted'),
                class: 'bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400 border-gray-200 dark:border-neutral-600',
            };
        }

        const expDate = new Date(batch.expiry_date);
        const daysLeft =
            (expDate.getTime() - new Date().getTime()) / (1000 * 3600 * 24);

        if (daysLeft < 0) {
            return {
                label: t('expired'),
                class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-950',
            };
        }

        if (daysLeft <= 30) {
            return {
                label: t('warning'),
                class: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-950',
            };
        }

        return {
            label: t('valid'),
            class: 'bg-slate-50 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 border-slate-200 dark:border-neutral-700',
        };
    };

    const formatDate = (dateStr?: string) =>
        dateStr
            ? new Date(dateStr).toLocaleDateString('en-US', {
                  month: 'short',
                  day: '2-digit',
                  year: 'numeric',
              })
            : '—';

    return (
        <AppLayout
            headerTitle={t('manageDrugs')}
            headerSubtitle={t('inventoryManagementBatchTracking')}
        >
            <Head title={t('drugInventory')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* 1. Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-bold text-blue-600">
                            {t('drugInventory')}
                        </li>
                    </ol>
                </nav>

                {/* 2. Top Metric Blocks */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('totalFormulations')}
                        value={stats.totalMedications}
                        icon={BeakerIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('lowStockItems')}
                        value={stats.lowStockCount}
                        icon={ExclamationTriangleIcon}
                        color="amber"
                    />
                    <StatCard
                        label={t('outOfStockAlerts')}
                        value={stats.outOfStockCount}
                        icon={ArchiveBoxXMarkIcon}
                        color="red"
                    />
                    <StatCard
                        label={t('expiringWarningBatches')}
                        value={stats.expiringWarningCount}
                        icon={ClockIcon}
                        color="red"
                    />
                </div>

                {/* 3. Controls / Filtering Layout */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div className="dark:bg-neutral-850 flex w-fit rounded-xl bg-gray-100 p-1">
                        <TabButton
                            active={activeTab === 'all'}
                            onClick={() => setActiveTab('all')}
                            label={t('allDrugs')}
                            icon={<CubeIcon className="h-4 w-4" />}
                        />
                        <TabButton
                            active={activeTab === 'low-stock'}
                            onClick={() => setActiveTab('low-stock')}
                            label={t('lowStock')}
                            icon={
                                <ExclamationTriangleIcon className="h-4 w-4" />
                            }
                        />
                        <TabButton
                            active={activeTab === 'expiring'}
                            onClick={() => setActiveTab('expiring')}
                            label={t('expiring')}
                            icon={<ClockIcon className="h-4 w-4" />}
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t('searchDrugs')}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="border-gray-250 w-48 rounded-lg border bg-white py-2 pr-4 pl-9 text-sm text-gray-900 transition-all outline-none focus:ring-2 focus:ring-blue-500 sm:w-64 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>
                        <Link
                            href={route('drugs.create')}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold whitespace-nowrap text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" /> {t('addDrug')}
                        </Link>
                    </div>
                </div>

                {/* 4. Main Medication Workspace */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    {drugs.length > 0 ? (
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 text-xs font-bold tracking-wider text-gray-500 uppercase dark:bg-neutral-700 dark:text-neutral-500">
                                <tr>
                                    <th className="px-6 py-4 text-left">
                                        {t('medication')}
                                    </th>
                                    <th className="px-6 py-4 text-right">
                                        {t('stock')}
                                    </th>
                                    <th className="px-6 py-4 text-center">
                                        {t('status')}
                                    </th>
                                    <th className="px-6 py-4 text-left">
                                        {t('earliestExpiry')}
                                    </th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                {drugs.map((drug) => {
                                    const status = getStockStatus(drug);
                                    const earliestBatch = drug.batches?.find(
                                        (b) => b.current_quantity > 0,
                                    );

                                    return (
                                        <tr
                                            key={drug.id}
                                            className="dark:hover:bg-neutral-750 group transition-colors hover:bg-gray-50"
                                        >
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 dark:border-neutral-700">
                                                        {drug.image_url ? (
                                                            <img
                                                                src={
                                                                    drug.image_url
                                                                }
                                                                alt=""
                                                                className="h-full w-full object-cover"
                                                            />
                                                        ) : (
                                                            <div className="flex h-full w-full items-center justify-center bg-blue-50 text-sm font-bold text-blue-600 dark:bg-neutral-900/20 dark:text-blue-400">
                                                                {drug.name.charAt(
                                                                    0,
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                            {drug.name}
                                                        </div>
                                                        <div className="text-xs text-gray-500 dark:text-neutral-400">
                                                            {drug.dosage_units}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                className={`px-6 py-4 text-right font-mono text-sm font-bold whitespace-nowrap ${drug.total_stock === 0 ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-neutral-100'}`}
                                            >
                                                {drug.total_stock}
                                            </td>
                                            <td className="px-6 py-4 text-center whitespace-nowrap">
                                                <span
                                                    className={`inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-bold ${status.class}`}
                                                >
                                                    {status.label}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm whitespace-nowrap">
                                                {earliestBatch ? (
                                                    <div className="flex flex-col">
                                                        <span
                                                            className={
                                                                new Date(
                                                                    earliestBatch.expiry_date,
                                                                ) < new Date()
                                                                    ? 'font-bold text-red-600 dark:text-red-400'
                                                                    : 'text-gray-900 dark:text-neutral-100'
                                                            }
                                                        >
                                                            {formatDate(
                                                                earliestBatch.expiry_date,
                                                            )}
                                                        </span>
                                                        <span className="text-gray-455 text-xs dark:text-neutral-500">
                                                            {
                                                                earliestBatch.batch_number
                                                            }
                                                        </span>
                                                    </div>
                                                ) : (
                                                    <span className="text-xs text-gray-400 italic dark:text-neutral-500">
                                                        {t('noActiveStock')}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 text-right whitespace-nowrap">
                                                <div className="flex justify-end gap-1.5">
                                                    <button
                                                        onClick={() =>
                                                            openEditModal(drug)
                                                        }
                                                        className="text-gray-450 rounded-lg p-2 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-950/40"
                                                    >
                                                        <PencilSquareIcon className="h-4 w-4" />
                                                    </button>
                                                    <button
                                                        onClick={() => {
                                                            setSelectedItem(
                                                                drug,
                                                            );
                                                            setDeleteModalOpen(
                                                                true,
                                                            );
                                                        }}
                                                        className="text-gray-450 rounded-lg p-2 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                                    >
                                                        <TrashIcon className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    ) : (
                        <div className="flex flex-col items-center justify-center px-4 py-20">
                            <InboxIcon className="mb-4 h-12 w-12 text-gray-300 dark:text-neutral-600" />
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                {t('noDrugsFound')}
                            </h3>
                            <p className="mt-1 mb-6 text-sm text-gray-500 dark:text-neutral-400">
                                {searchQuery
                                    ? t('tryRefiningSearch')
                                    : t('beginCreatingFormulation')}
                            </p>
                            <Link
                                href={route('drugs.create')}
                                className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm"
                            >
                                <PlusIcon className="h-4 w-4" />{' '}
                                {t('addMedication')}
                            </Link>
                        </div>
                    )}
                </div>

                {/* Detailed Batch Workspace */}
                {allBatches.length > 0 && (
                    <div className="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-700">
                            <h3 className="text-xs font-bold tracking-wider text-gray-900 uppercase dark:text-neutral-100">
                                {t('detailedBatchTracking')}
                            </h3>
                            <ArchiveBoxIcon className="h-5 w-5 text-gray-400" />
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead className="border-b border-gray-200 bg-white text-xs font-bold tracking-wider text-gray-500 uppercase dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500">
                                    <tr>
                                        <th className="px-6 py-3">
                                            {t('medication')}
                                        </th>
                                        <th className="px-6 py-3">
                                            {t('batchNumberShort')}
                                        </th>
                                        <th className="px-6 py-3">
                                            {t('expiry')}
                                        </th>
                                        <th className="px-6 py-3 text-right">
                                            {t('qty')}
                                        </th>
                                        <th className="px-6 py-3 text-center">
                                            {t('status')}
                                        </th>
                                        <th className="px-6 py-3 text-right">
                                            {t('actions')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-gray-150 divide-y dark:divide-neutral-700">
                                    {allBatches.map((batch, idx) => {
                                        const bStatus = getBatchStatus(batch);
                                        const isDepleted =
                                            batch.current_quantity === 0 ||
                                            batch.status === 'depleted';

                                        return (
                                            <tr
                                                key={`${batch.id}-${idx}`}
                                                className={`dark:hover:bg-neutral-750 transition-all hover:bg-gray-50 ${isDepleted ? 'bg-gray-50/50 dark:bg-neutral-800/10' : ''}`}
                                            >
                                                <td className="px-6 py-3">
                                                    <div
                                                        className={`text-sm font-bold ${isDepleted ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-neutral-100'}`}
                                                    >
                                                        {batch.drugName}
                                                    </div>
                                                    <div className="text-xs text-gray-500 dark:text-neutral-400">
                                                        {batch.drugDosage}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-3 font-mono text-sm text-gray-600 dark:text-neutral-400">
                                                    {batch.batch_number}
                                                </td>
                                                <td className="px-6 py-3 text-sm text-gray-900 dark:text-neutral-100">
                                                    {formatDate(
                                                        batch.expiry_date,
                                                    )}
                                                </td>
                                                <td
                                                    className={`px-6 py-3 text-right font-mono text-sm font-bold ${isDepleted ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-neutral-100'}`}
                                                >
                                                    {batch.current_quantity}
                                                </td>
                                                <td className="px-6 py-3 text-center">
                                                    <span
                                                        className={`inline-block rounded-full border px-2.5 py-0.5 text-[10px] font-bold ${bStatus.class}`}
                                                    >
                                                        {bStatus.label}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-3 text-right">
                                                    {!isDepleted && (
                                                        <button
                                                            onClick={() => {
                                                                setSelectedBatch(
                                                                    batch as any,
                                                                );
                                                                setDepleteModalOpen(
                                                                    true,
                                                                );
                                                            }}
                                                            className="inline-block rounded-lg p-2 text-gray-400 transition-all hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-orange-950/40 dark:hover:text-orange-400"
                                                            title={t(
                                                                'depleteStock',
                                                            )}
                                                        >
                                                            <ArchiveBoxXMarkIcon className="h-5 w-5" />
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
                            className="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-700">
                                <h3 className="text-base font-bold tracking-wider text-gray-900 uppercase dark:text-neutral-100">
                                    {t('edit')} {selectedItem?.name}
                                </h3>
                                <button
                                    onClick={() => setEditModalOpen(false)}
                                    className="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300"
                                >
                                    <XMarkIcon className="h-5 w-5" />
                                </button>
                            </div>
                            <div className="space-y-8 overflow-y-auto p-6">
                                <div className="space-y-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="mb-1 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                {t('drugName')}
                                            </label>
                                            <input
                                                type="text"
                                                value={drugForm.data.name}
                                                onChange={(e) =>
                                                    drugForm.setData(
                                                        'name',
                                                        e.target.value,
                                                    )
                                                }
                                                className="border-gray-250 w-full rounded-lg border px-3 py-2 text-sm transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div>
                                            <label className="mb-1 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                                {t('dosageUnits')}
                                            </label>
                                            <input
                                                type="text"
                                                value={
                                                    drugForm.data.dosage_units
                                                }
                                                onChange={(e) =>
                                                    drugForm.setData(
                                                        'dosage_units',
                                                        e.target.value,
                                                    )
                                                }
                                                className="border-gray-250 w-full rounded-lg border px-3 py-2 text-sm transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                    </div>
                                    <div className="w-1/2">
                                        <label className="mb-1 block text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-neutral-400">
                                            {t('minStockLevel')}
                                        </label>
                                        <input
                                            type="number"
                                            value={
                                                drugForm.data.min_stock_level
                                            }
                                            onChange={(e) =>
                                                drugForm.setData(
                                                    'min_stock_level',
                                                    parseInt(e.target.value),
                                                )
                                            }
                                            className="border-gray-250 w-full rounded-lg border px-3 py-2 text-sm transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                        />
                                    </div>
                                    <button
                                        onClick={handleUpdateDrug}
                                        disabled={drugForm.processing}
                                        className="rounded-lg bg-blue-600 px-4 py-2 text-xs font-black tracking-wider text-white uppercase shadow-md transition-all hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {drugForm.processing
                                            ? t('updating')
                                            : t('updateInformation')}
                                    </button>
                                </div>

                                <div className="dark:border-neutral-750 border-t pt-6">
                                    <h4 className="dark:text-emerald-450 mb-4 flex items-center gap-2 text-xs font-black tracking-widest text-emerald-600 uppercase">
                                        <PlusIcon className="h-4 w-4" />{' '}
                                        {t('addNewStockBatch')}
                                    </h4>
                                    <div className="grid grid-cols-12 gap-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-950 dark:bg-emerald-950/10">
                                        <div className="col-span-3">
                                            <label className="dark:text-emerald-450 text-[10px] font-bold text-emerald-800 uppercase">
                                                {t('batchNumberShort')}
                                            </label>
                                            <input
                                                type="text"
                                                placeholder={t('ref001')}
                                                value={
                                                    batchForm.data.batch_number
                                                }
                                                onChange={(e) =>
                                                    batchForm.setData(
                                                        'batch_number',
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full rounded border border-emerald-200 p-2 text-xs outline-none focus:ring-1 focus:ring-emerald-500 dark:border-emerald-800 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div className="col-span-3">
                                            <label className="dark:text-emerald-450 text-[10px] font-bold text-emerald-800 uppercase">
                                                {t('expiry')}
                                            </label>
                                            <input
                                                type="date"
                                                value={
                                                    batchForm.data.expiry_date
                                                }
                                                onChange={(e) =>
                                                    batchForm.setData(
                                                        'expiry_date',
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full rounded border border-emerald-200 p-2 text-xs outline-none focus:ring-1 focus:ring-emerald-500 dark:border-emerald-800 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div className="col-span-2">
                                            <label className="dark:text-emerald-455 text-[10px] font-bold text-emerald-800 uppercase">
                                                {t('qty')}
                                            </label>
                                            <input
                                                type="number"
                                                placeholder="0"
                                                value={batchForm.data.quantity}
                                                onChange={(e) =>
                                                    batchForm.setData(
                                                        'quantity',
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full rounded border border-emerald-200 p-2 text-xs outline-none focus:ring-1 focus:ring-emerald-500 dark:border-emerald-800 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div className="col-span-2">
                                            <label className="dark:text-emerald-455 text-[10px] font-bold text-emerald-800 uppercase">
                                                {t('price')}
                                            </label>
                                            <input
                                                type="number"
                                                placeholder="0.00"
                                                value={
                                                    batchForm.data
                                                        .purchase_price
                                                }
                                                onChange={(e) =>
                                                    batchForm.setData(
                                                        'purchase_price',
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full rounded border border-emerald-200 p-2 text-xs outline-none focus:ring-1 focus:ring-emerald-500 dark:border-emerald-800 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                        <div className="col-span-2 flex items-end">
                                            <button
                                                type="button"
                                                onClick={handleAddBatch}
                                                disabled={batchForm.processing}
                                                className="w-full rounded bg-emerald-600 py-2 text-xs font-bold text-white transition-colors hover:bg-emerald-700 disabled:opacity-50"
                                            >
                                                {t('add')}
                                            </button>
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
                            className="relative z-10 w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full border border-orange-200 bg-orange-100 text-orange-600 dark:border-orange-900 dark:bg-orange-950/20 dark:text-orange-400">
                                <ArchiveBoxXMarkIcon className="h-6 w-6" />
                            </div>
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                {t('markBatchDepleted')}
                            </h3>
                            <p className="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                {t('setsStockToZero')}{' '}
                                <span className="font-mono font-bold text-gray-900 dark:text-neutral-100">
                                    {selectedBatch?.batch_number}
                                </span>
                                . {t('cannotBeUndone')}
                            </p>
                            <div className="mt-6 flex gap-3">
                                <button
                                    onClick={() => setDepleteModalOpen(false)}
                                    className="flex-1 rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold transition-colors hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                >
                                    {t('cancel')}
                                </button>
                                <button
                                    onClick={handleDeplete}
                                    className="flex-1 rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-orange-700"
                                >
                                    {t('confirmDepletion')}
                                </button>
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
                            className="relative z-10 w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full border border-red-200 bg-red-100 text-red-600 dark:border-red-900 dark:bg-red-950/20 dark:text-red-400">
                                <ExclamationTriangleIcon className="h-6 w-6" />
                            </div>
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                {t('deleteMedication')}
                            </h3>
                            <p className="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                {t('allDataBatchLogsRemoved')}{' '}
                                <b className="dark:text-neutral-100">
                                    {selectedItem?.name}
                                </b>
                            </p>
                            <div className="mt-6 flex gap-3">
                                <button
                                    onClick={() => setDeleteModalOpen(false)}
                                    className="flex-1 rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold transition-colors hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                >
                                    {t('cancel')}
                                </button>
                                <button
                                    onClick={handleDelete}
                                    className="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-red-700"
                                >
                                    {t('deleteMedication')}
                                </button>
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
            className={`flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold tracking-wider uppercase transition-all ${
                active
                    ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700 dark:text-blue-400'
                    : 'text-gray-500 hover:text-gray-950 dark:hover:text-neutral-300'
            }`}
        >
            {icon} {label}
        </button>
    );
}
