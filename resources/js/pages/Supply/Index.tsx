import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    ArrowUpIcon,
    ArrowDownIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    ExclamationTriangleIcon,
    CurrencyDollarIcon,
    CubeIcon,
    BeakerIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Supply {
    id: number;
    name: string;
    image_url: string | null;
    unit_of_measure: string;
    current_stock: number;
    min_stock_level: number;
    cost_per_unit: number;
    category: string;
    last_restocked: string | null;
}

type ViewMode = 'grid' | 'table';
type ModalMode = 'restock' | 'delete' | null;
type StockStatus = 'all' | 'critical' | 'low' | 'good';

export default function SupplyManagement({ supplies }: { supplies: Supply[] }) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState<StockStatus>('all');
    const [viewMode, setViewMode] = useState<ViewMode>('table');

    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<ModalMode>(null);
    const [selectedItem, setSelectedItem] = useState<Supply | null>(null);

    const { data, setData, put, processing, reset, errors, clearErrors } =
        useForm({
            add_amount: '' as string | number,
        });

    const getStockStatus = (supply: Supply) => {
        if (supply.current_stock === 0) {
            return 'out';
        }

        if (supply.current_stock <= supply.min_stock_level) {
            return 'low';
        }

        return 'good';
    };

    const getBadgeColor = (status: string) => {
        switch (status) {
            case 'out':
                return 'red';
            case 'low':
                return 'amber';
            case 'good':
                return 'emerald';
            default:
                return 'gray';
        }
    };

    const getStatusLabel = (status: string) => {
        switch (status) {
            case 'out':
                return t('page.supplies.out_of_stock');
            case 'low':
                return t('page.supplies.low_stock');
            case 'good':
                return t('page.supplies.in_stock');
            default:
                return t('page.supplies.unknown');
        }
    };

    const openModal = (mode: ModalMode, item: Supply) => {
        setModalMode(mode);
        setSelectedItem(item);

        if (mode === 'restock') {
            reset();
            clearErrors();
        }

        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setModalMode(null);
        setSelectedItem(null);
        reset();
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (!selectedItem) {
            return;
        }

        if (modalMode === 'delete') {
            router.delete(route('supplies.destroy', selectedItem.id), {
                onSuccess: closeModal,
            });
        } else if (modalMode === 'restock') {
            put(route('supplies.restock', selectedItem.id), {
                onSuccess: closeModal,
            });
        }
    };

    const filteredSupplies = useMemo(() => {
        return supplies.filter((s) => {
            const matchesSearch =
                s.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                s.category.toLowerCase().includes(searchQuery.toLowerCase());

            const status = getStockStatus(s);
            const matchesStatus =
                statusFilter === 'all' ||
                (statusFilter === 'critical' && status === 'out') ||
                (statusFilter === 'low' && status === 'low') ||
                (statusFilter === 'good' && status === 'good');

            return matchesSearch && matchesStatus;
        });
    }, [supplies, searchQuery, statusFilter]);

    const stats = useMemo(() => {
        const totalValue = supplies.reduce(
            (acc, s) => acc + s.current_stock * Number(s.cost_per_unit),
            0,
        );
        const lowStockCount = supplies.filter(
            (s) => s.current_stock <= s.min_stock_level && s.current_stock > 0,
        ).length;
        const outOfStockCount = supplies.filter(
            (s) => s.current_stock === 0,
        ).length;

        return {
            totalItems: supplies.length,
            totalValue,
            lowStockCount,
            outOfStockCount,
        };
    }, [supplies]);

    const modalTitle =
        modalMode === 'delete'
            ? t('page.supplies.delete_item')
            : 'Restock Inventory';

    return (
        <AppLayout
            headerTitle={t('page.supplies.title')}
            headerSubtitle={t('page.supplies.subtitle')}
        >
            <Head title={t('page.supplies.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.supplies.administration') },
                        { label: t('page.supplies.inventory') },
                    ]}
                />

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        icon={CubeIcon}
                        label={t('page.supplies.total_items')}
                        value={stats.totalItems}
                        color="blue"
                    />
                    <StatCard
                        icon={CurrencyDollarIcon}
                        label={t('page.supplies.inventory_value')}
                        value={`$${stats.totalValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`}
                        color="emerald"
                    />
                    <StatCard
                        icon={ExclamationTriangleIcon}
                        label={t('page.supplies.low_stock_alerts')}
                        value={stats.lowStockCount}
                        color="amber"
                    />
                    <StatCard
                        icon={ArrowDownIcon}
                        label={t('page.supplies.out_of_stock_label')}
                        value={stats.outOfStockCount}
                        color="red"
                    />
                </div>

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <div className="relative w-full sm:w-80">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t(
                                    'page.supplies.search_placeholder',
                                )}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>

                        <div className="relative">
                            <select
                                value={statusFilter}
                                onChange={(e) =>
                                    setStatusFilter(
                                        e.target.value as StockStatus,
                                    )
                                }
                                className="cursor-pointer appearance-none rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-4 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option value="all">
                                    {t('page.supplies.all_status')}
                                </option>
                                <option value="good">
                                    {t('page.supplies.in_stock')}
                                </option>
                                <option value="low">
                                    {t('page.supplies.low_stock')}
                                </option>
                                <option value="critical">
                                    {t('page.supplies.out_of_stock')}
                                </option>
                            </select>
                            <FunnelIcon className="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        </div>
                    </div>

                    <div className="flex items-center gap-3">
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                        <Link
                            href={route('supplies.create')}
                            className="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.supplies.add_supply')}
                        </Link>
                    </div>
                </div>

                {filteredSupplies.length === 0 ? (
                    <EmptyState
                        icon={CubeIcon}
                        title={t('common.no_results')}
                        action={{
                            label: t('page.supplies.add_supply'),
                            href: route('supplies.create'),
                        }}
                    />
                ) : (
                    <>
                        {viewMode === 'grid' && (
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                {filteredSupplies.map((supply) => {
                                    const status = getStockStatus(supply);

                                    return (
                                        <div
                                            key={supply.id}
                                            className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                                        >
                                            <div className="mb-4 flex items-start justify-between">
                                                <div
                                                    className={`overflow-hidden rounded-lg p-3 ${status === 'good' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-neutral-700 dark:text-neutral-400'}`}
                                                >
                                                    {supply.image_url ? (
                                                        <img
                                                            src={
                                                                supply.image_url
                                                            }
                                                            alt=""
                                                            className="h-6 w-6 rounded object-cover"
                                                        />
                                                    ) : (
                                                        <BeakerIcon className="h-6 w-6" />
                                                    )}
                                                </div>
                                                <StatusBadge
                                                    label={getStatusLabel(
                                                        status,
                                                    )}
                                                    color={getBadgeColor(
                                                        status,
                                                    )}
                                                />
                                            </div>
                                            <div className="mb-4 flex-grow">
                                                <h3 className="mb-1 truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                                    {supply.name}
                                                </h3>
                                                <p className="mb-4 text-xs font-bold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                                    {supply.category}
                                                </p>
                                                <div className="space-y-3">
                                                    <div>
                                                        <div className="mb-1 flex justify-between text-xs font-semibold">
                                                            <span className="text-gray-500 dark:text-neutral-400">
                                                                {t(
                                                                    'page.supplies.stock_level',
                                                                )}
                                                            </span>
                                                            <span
                                                                className={`font-bold ${status === 'low' || status === 'out' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-neutral-100'}`}
                                                            >
                                                                {
                                                                    supply.current_stock
                                                                }{' '}
                                                                /{' '}
                                                                {
                                                                    supply.min_stock_level
                                                                }{' '}
                                                                min
                                                            </span>
                                                        </div>
                                                        <div className="h-2 w-full rounded-full bg-gray-100 dark:bg-neutral-700">
                                                            <div
                                                                className={`h-2 rounded-full ${status === 'good' ? 'bg-emerald-500' : status === 'low' ? 'bg-amber-500' : 'bg-red-500'}`}
                                                                style={{
                                                                    width: `${Math.min((supply.current_stock / (supply.min_stock_level * 2)) * 100, 100)}%`,
                                                                }}
                                                            ></div>
                                                        </div>
                                                    </div>
                                                    <div className="grid grid-cols-2 gap-2 pt-2">
                                                        <div className="rounded-lg border border-gray-100 bg-gray-50 p-2 dark:border-neutral-700 dark:bg-neutral-700/50">
                                                            <p className="text-[10px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                                                                {t(
                                                                    'page.supplies.unit_cost',
                                                                )}
                                                            </p>
                                                            <p className="text-sm font-bold text-gray-700 dark:text-neutral-300">
                                                                $
                                                                {Number(
                                                                    supply.cost_per_unit,
                                                                ).toFixed(2)}
                                                            </p>
                                                        </div>
                                                        <div className="rounded-lg border border-gray-100 bg-gray-50 p-2 dark:border-neutral-700 dark:bg-neutral-700/50">
                                                            <p className="text-[10px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                                                                {t(
                                                                    'page.supplies.total_value',
                                                                )}
                                                            </p>
                                                            <p className="text-sm font-bold text-gray-700 dark:text-neutral-300">
                                                                $
                                                                {(
                                                                    supply.current_stock *
                                                                    supply.cost_per_unit
                                                                ).toFixed(2)}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="mt-4 flex gap-2 border-t border-gray-100 pt-4 dark:border-neutral-700">
                                                <button
                                                    onClick={() =>
                                                        openModal(
                                                            'restock',
                                                            supply,
                                                        )
                                                    }
                                                    className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-50 py-2 text-xs font-bold text-blue-700 transition-colors hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                                >
                                                    <ArrowUpIcon className="h-4 w-4" />{' '}
                                                    {t('page.supplies.restock')}
                                                </button>
                                                <Link
                                                    href={route(
                                                        'supplies.edit',
                                                        supply.id,
                                                    )}
                                                    className="rounded-lg border border-gray-200 p-2 text-gray-600 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700"
                                                >
                                                    <PencilSquareIcon className="h-4 w-4" />
                                                </Link>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}

                        {viewMode === 'table' && (
                            <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                        <thead className="bg-gray-50 dark:bg-neutral-800">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                                                >
                                                    {t(
                                                        'page.supplies.col_supply_name',
                                                    )}
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                                                >
                                                    {t(
                                                        'page.supplies.col_category',
                                                    )}
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                                                >
                                                    {t(
                                                        'page.supplies.col_stock_level',
                                                    )}
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                                                >
                                                    {t(
                                                        'page.supplies.col_cost_value',
                                                    )}
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-3 text-right text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400"
                                                >
                                                    {t('common.actions')}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                            {filteredSupplies.map((supply) => {
                                                const status =
                                                    getStockStatus(supply);

                                                return (
                                                    <tr
                                                        key={supply.id}
                                                        className="transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                                    >
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="flex items-center">
                                                                <div className="h-10 w-10 flex-shrink-0 overflow-hidden rounded-lg border border-blue-100 dark:border-blue-800">
                                                                    {supply.image_url ? (
                                                                        <img
                                                                            src={
                                                                                supply.image_url
                                                                            }
                                                                            alt=""
                                                                            className="h-full w-full object-cover"
                                                                        />
                                                                    ) : (
                                                                        <div className="flex h-full w-full items-center justify-center bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                                                            <CubeIcon className="h-5 w-5" />
                                                                        </div>
                                                                    )}
                                                                </div>
                                                                <div className="ml-4">
                                                                    <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            supply.name
                                                                        }
                                                                    </div>
                                                                    <div className="text-xs font-semibold text-gray-500 dark:text-neutral-400">
                                                                        {t(
                                                                            'page.supplies.alert_min',
                                                                        )}
                                                                        :{' '}
                                                                        {
                                                                            supply.min_stock_level
                                                                        }
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <StatusBadge
                                                                label={
                                                                    supply.category
                                                                }
                                                                color="gray"
                                                            />
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="flex flex-col gap-1.5">
                                                                <div className="flex items-center gap-2">
                                                                    <span
                                                                        className={`text-sm font-bold ${status === 'good' ? 'text-gray-900 dark:text-neutral-100' : 'text-red-600 dark:text-red-400'}`}
                                                                    >
                                                                        {
                                                                            supply.current_stock
                                                                        }
                                                                    </span>
                                                                    <span className="text-[10px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                                                                        {
                                                                            supply.unit_of_measure
                                                                        }
                                                                    </span>
                                                                </div>
                                                                <div className="h-1.5 w-24 rounded-full bg-gray-100 dark:bg-neutral-700">
                                                                    <div
                                                                        className={`h-1.5 rounded-full ${status === 'good' ? 'bg-emerald-500' : status === 'low' ? 'bg-amber-500' : 'bg-red-500'}`}
                                                                        style={{
                                                                            width: `${Math.min((supply.current_stock / (supply.min_stock_level * 2)) * 100, 100)}%`,
                                                                        }}
                                                                    ></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                                $
                                                                {Number(
                                                                    supply.cost_per_unit,
                                                                ).toFixed(2)}
                                                            </div>
                                                            <div className="text-xs font-medium text-gray-500 dark:text-neutral-400">
                                                                Total: $
                                                                {(
                                                                    supply.current_stock *
                                                                    supply.cost_per_unit
                                                                ).toFixed(2)}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                                            <div className="flex justify-end gap-2">
                                                                <button
                                                                    onClick={() =>
                                                                        openModal(
                                                                            'restock',
                                                                            supply,
                                                                        )
                                                                    }
                                                                    className="rounded-md border border-blue-100 bg-blue-50 p-1.5 text-blue-600 transition-colors hover:bg-blue-100 hover:text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                                                                    title={t(
                                                                        'page.supplies.restock',
                                                                    )}
                                                                >
                                                                    <ArrowUpIcon className="h-4 w-4" />
                                                                </button>
                                                                <Link
                                                                    href={route(
                                                                        'supplies.edit',
                                                                        supply.id,
                                                                    )}
                                                                    className="rounded-md border border-gray-200 bg-white p-1.5 text-gray-500 transition-colors hover:bg-gray-50 hover:text-gray-800 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                                                                    title={t(
                                                                        'common.edit',
                                                                    )}
                                                                >
                                                                    <PencilSquareIcon className="h-4 w-4" />
                                                                </Link>
                                                                <button
                                                                    onClick={() =>
                                                                        openModal(
                                                                            'delete',
                                                                            supply,
                                                                        )
                                                                    }
                                                                    className="rounded-md border border-red-100 bg-red-50 p-1.5 text-red-500 transition-colors hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                                                    title={t(
                                                                        'common.delete',
                                                                    )}
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
                                </div>
                            </div>
                        )}
                    </>
                )}
            </div>

            <Modal isOpen={modalOpen} onClose={closeModal} title={modalTitle}>
                <form onSubmit={handleSubmit} className="space-y-6">
                    {modalMode === 'delete' ? (
                        <div className="text-center">
                            <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-900/20">
                                <ExclamationTriangleIcon className="h-7 w-7 text-red-600 dark:text-red-400" />
                            </div>
                            <p className="text-sm text-gray-500 dark:text-neutral-400">
                                {t('page.supplies.delete_confirm', {
                                    name: selectedItem?.name ?? '',
                                })}
                            </p>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            <div className="flex items-center justify-between rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                                <div>
                                    <p className="text-[10px] font-bold tracking-wide text-blue-700 uppercase dark:text-blue-400">
                                        Current
                                    </p>
                                    <p className="text-2xl font-bold text-blue-900 dark:text-blue-300">
                                        {selectedItem?.current_stock}{' '}
                                        <span className="text-sm font-semibold">
                                            {selectedItem?.unit_of_measure}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <label className="mb-1.5 block text-xs font-bold tracking-wide text-gray-600 uppercase dark:text-neutral-400">
                                    Quantity to Add
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    autoFocus
                                    value={data.add_amount}
                                    onChange={(e) =>
                                        setData('add_amount', e.target.value)
                                    }
                                    className="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                    placeholder="Enter amount..."
                                />
                                {errors.add_amount && (
                                    <p className="mt-1 text-xs font-medium text-red-500">
                                        {errors.add_amount}
                                    </p>
                                )}
                            </div>
                        </div>
                    )}

                    <div className="flex gap-3 pt-2">
                        <button
                            type="button"
                            onClick={closeModal}
                            disabled={processing}
                            className="flex-1 rounded-lg border border-gray-300 bg-white py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className={`flex flex-1 items-center justify-center gap-2 rounded-lg py-2 text-sm font-bold text-white shadow-sm ${modalMode === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'}`}
                        >
                            {processing && (
                                <ArrowPathIcon className="h-4 w-4 animate-spin" />
                            )}
                            {modalMode === 'delete' ? 'Delete' : 'Update Stock'}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
