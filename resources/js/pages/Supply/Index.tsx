import {
    PlusIcon,
    PencilSquareIcon,
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
import {
    Table,
    TableBody,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { SupplyRow } from '@/pages/Supply/components/SupplyRow';

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
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                            <input
                                type="text"
                                placeholder={t(
                                    'page.supplies.search_placeholder',
                                )}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="w-full rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-ink shadow-surface focus:ring-2 focus:ring-blue-500 focus:outline-none"
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
                                className="cursor-pointer appearance-none rounded-lg border border-line bg-surface py-2 pr-10 pl-4 text-ink shadow-surface focus:ring-2 focus:ring-blue-500 focus:outline-none"
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
                            <FunnelIcon className="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                        </div>
                    </div>

                    <div className="flex items-center gap-3">
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                        <Link
                            href={route('supplies.create')}
                            className="flex items-center justify-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-bold text-ink-inverse shadow-surface transition-colors hover:bg-accent-hover"
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
                                            className="group relative flex h-full flex-col rounded-xl border border-line bg-surface p-6 transition-all hover:shadow-surface"
                                        >
                                            <div className="mb-4 flex items-start justify-between">
                                                <div
                                                    className={`overflow-hidden rounded-lg p-3 ${status === 'good' ? 'bg-accent-soft text-accent' : 'bg-surface-inset text-ink-subtle'}`}
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
                                                <h3 className="mb-1 truncate text-lg font-bold text-ink">
                                                    {supply.name}
                                                </h3>
                                                <p className="mb-4 text-xs font-bold tracking-wide text-ink-subtle uppercase">
                                                    {supply.category}
                                                </p>
                                                <div className="space-y-3">
                                                    <div>
                                                        <div className="mb-1 flex justify-between text-xs font-semibold">
                                                            <span className="text-ink-subtle">
                                                                {t(
                                                                    'page.supplies.stock_level',
                                                                )}
                                                            </span>
                                                            <span
                                                                className={`font-bold ${status === 'low' || status === 'out' ? 'text-critical-ink' : 'text-ink'}`}
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
                                                        <div className="h-2 w-full rounded-full bg-surface-inset">
                                                            <div
                                                                className={`h-2 rounded-full ${status === 'good' ? 'bg-positive' : status === 'low' ? 'bg-warning' : 'bg-critical'}`}
                                                                style={{
                                                                    width: `${Math.min((supply.current_stock / (supply.min_stock_level * 2)) * 100, 100)}%`,
                                                                }}
                                                            ></div>
                                                        </div>
                                                    </div>
                                                    <div className="grid grid-cols-2 gap-2 pt-2">
                                                        <div className="rounded-lg border border-line bg-surface-muted p-2">
                                                            <p className="text-[10px] font-bold text-ink-subtle uppercase">
                                                                {t(
                                                                    'page.supplies.unit_cost',
                                                                )}
                                                            </p>
                                                            <p className="text-sm font-bold text-ink-muted">
                                                                $
                                                                {Number(
                                                                    supply.cost_per_unit,
                                                                ).toFixed(2)}
                                                            </p>
                                                        </div>
                                                        <div className="rounded-lg border border-line bg-surface-muted p-2">
                                                            <p className="text-[10px] font-bold text-ink-subtle uppercase">
                                                                {t(
                                                                    'page.supplies.total_value',
                                                                )}
                                                            </p>
                                                            <p className="text-sm font-bold text-ink-muted">
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
                                            <div className="mt-4 flex gap-2 border-t border-line pt-4">
                                                <button
                                                    onClick={() =>
                                                        openModal(
                                                            'restock',
                                                            supply,
                                                        )
                                                    }
                                                    className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-accent-soft py-2 text-xs font-bold text-accent-ink transition-colors hover:bg-accent-soft"
                                                >
                                                    <ArrowUpIcon className="h-4 w-4" />{' '}
                                                    {t('page.supplies.restock')}
                                                </button>
                                                <Link
                                                    href={route(
                                                        'supplies.edit',
                                                        supply.id,
                                                    )}
                                                    className="rounded-lg border border-line p-2 text-ink-muted transition-colors hover:bg-surface-hover"
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
                            <div className="overflow-hidden rounded-xl border border-line bg-surface shadow-surface">
                                <Table bare>
                                    <TableHeader>
                                        <TableRow className="hover:bg-transparent">
                                            <TableHeaderCell>
                                                {t(
                                                    'page.supplies.col_supply_name',
                                                )}
                                            </TableHeaderCell>
                                            <TableHeaderCell>
                                                {t(
                                                    'page.supplies.col_category',
                                                )}
                                            </TableHeaderCell>
                                            <TableHeaderCell>
                                                {t(
                                                    'page.supplies.col_stock_level',
                                                )}
                                            </TableHeaderCell>
                                            <TableHeaderCell numeric>
                                                {t(
                                                    'page.supplies.col_cost_value',
                                                )}
                                            </TableHeaderCell>
                                            <TableHeaderCell numeric>
                                                {t('common.actions')}
                                            </TableHeaderCell>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {filteredSupplies.map((supply) => (
                                            <SupplyRow
                                                key={supply.id}
                                                supply={supply}
                                                status={getStockStatus(supply)}
                                                onRestock={() =>
                                                    openModal('restock', supply)
                                                }
                                                onDelete={() =>
                                                    openModal('delete', supply)
                                                }
                                            />
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>
                        )}
                    </>
                )}
            </div>

            <Modal isOpen={modalOpen} onClose={closeModal} title={modalTitle}>
                <form onSubmit={handleSubmit} className="space-y-6">
                    {modalMode === 'delete' ? (
                        <div className="text-center">
                            <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-critical-soft">
                                <ExclamationTriangleIcon className="h-7 w-7 text-critical-ink" />
                            </div>
                            <p className="text-sm text-ink-subtle">
                                {t('page.supplies.delete_confirm', {
                                    name: selectedItem?.name ?? '',
                                })}
                            </p>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            <div className="flex items-center justify-between rounded-lg border border-accent/30 bg-accent-soft p-4">
                                <div>
                                    <p className="text-[10px] font-bold tracking-wide text-accent-ink uppercase">
                                        Current
                                    </p>
                                    <p className="text-2xl font-bold text-accent-ink">
                                        {selectedItem?.current_stock}{' '}
                                        <span className="text-sm font-semibold">
                                            {selectedItem?.unit_of_measure}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <label className="mb-1.5 block text-xs font-bold tracking-wide text-ink-muted uppercase">
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
                                    className="w-full rounded-lg border border-line-strong bg-surface-inset px-4 py-2.5 text-ink shadow-surface focus:ring-2 focus:ring-blue-500"
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
                            className="flex-1 rounded-lg border border-line-strong bg-surface py-2 text-sm font-bold text-ink-muted shadow-surface hover:bg-surface-hover"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className={`flex flex-1 items-center justify-center gap-2 rounded-lg py-2 text-sm font-bold text-ink-inverse shadow-surface ${modalMode === 'delete' ? 'bg-critical hover:bg-red-700' : 'bg-accent hover:bg-accent-hover'}`}
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
