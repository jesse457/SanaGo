import {
    ArchiveBoxIcon,
    ArchiveBoxXMarkIcon,
    BeakerIcon,
    ClockIcon,
    CubeIcon,
    ExclamationTriangleIcon,
    InboxIcon,
    ListBulletIcon,
    PlusIcon,
    Squares2X2Icon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import debounce from 'lodash/debounce';
import React, { useEffect, useMemo, useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Dialog,
    EmptyState,
    Field,
    Input,
    PageHeader,
    SectionCard,
    SegmentedControl,
    StatCard,
    StatGrid,
    Tabs,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import type { SegmentedOption } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { BatchQuickAddForm } from '@/pages/Drugs/components/BatchCard';
import type { BatchFormValues } from '@/pages/Drugs/components/BatchCard';
import { ExpiryDate } from '@/pages/Drugs/components/DrugBadges';
import { DrugCard } from '@/pages/Drugs/components/DrugCard';
import { BatchTable, DrugTable } from '@/pages/Drugs/components/DrugTable';
import type {
    BatchRow,
    Drug,
    DrugBatch,
    DrugFilters,
} from '@/pages/Drugs/components/types';
import { daysUntil, isBatchDepleted } from '@/pages/Drugs/components/types';

interface Props {
    drugs: Drug[];
    filters: DrugFilters;
}

type ViewMode = 'grid' | 'table';

const VIEW_MODE_KEY = 'drugs_view_mode';

const viewOptions: SegmentedOption<ViewMode>[] = [
    { value: 'grid', icon: Squares2X2Icon, ariaLabel: 'Grid view' },
    { value: 'table', icon: ListBulletIcon, ariaLabel: 'Table view' },
];

const emptyBatchForm = (): BatchFormValues => ({
    batch_number: '',
    expiry_date: '',
    quantity: '',
    purchase_price: '',
    sale_price: '',
    manufactured_date: '',
    notes: '',
});

export default function DrugInventory({ drugs = [], filters }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [activeTab, setActiveTab] = useState(filters.tab || 'all');

    // Persist the grid/table preference; the toolbar switch owns the control.
    const [viewMode, setViewMode] = useState<ViewMode>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem(VIEW_MODE_KEY);

            if (saved === 'grid' || saved === 'table') {
                return saved;
            }
        }

        return 'table';
    });

    const [params, setParams] = useState({
        search: filters.search || '',
        tab: filters.tab || 'all',
    });

    const [editModalOpen, setEditModalOpen] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [depleteModalOpen, setDepleteModalOpen] = useState(false);

    const [selectedItem, setSelectedItem] = useState<Drug | null>(null);
    const [selectedBatch, setSelectedBatch] = useState<BatchRow | null>(null);
    const [batchValues, setBatchValues] =
        useState<BatchFormValues>(emptyBatchForm());

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
        sale_price: '',
        manufactured_date: '',
        notes: '',
    });

    // Sync local filter state with the server-provided props.
    useEffect(() => {
        // Mirror server-driven filters (pagination / back-forward) into local
        // state; this responds to an external event, it is not derived state.
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setParams({
            search: filters.search || '',
            tab: filters.tab || 'all',
        });
        setSearchQuery(filters.search || '');
        setActiveTab(filters.tab || 'all');
    }, [filters]);

    const handleViewModeChange = (mode: ViewMode) => {
        setViewMode(mode);

        if (typeof window !== 'undefined') {
            localStorage.setItem(VIEW_MODE_KEY, mode);
        }
    };

    const requestUpdate = useMemo(
        () =>
            debounce((query: Record<string, string>) => {
                router.get(route('drugs.index'), query, {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                });
            }, 300),
        [],
    );

    const handleFilterChange = (key: string, value: string) => {
        const newParams = { ...params, [key]: value };
        setParams(newParams);
        requestUpdate(newParams);
    };

    const handleSearchChange = (value: string) => {
        setSearchQuery(value);
        handleFilterChange('search', value);
    };

    const handleTabChange = (value: string) => {
        setActiveTab(value);
        handleFilterChange('tab', value);
    };

    const stats = useMemo(() => {
        const lowStockCount = drugs.filter(
            (d) => d.total_stock <= d.min_stock_level && d.total_stock > 0,
        ).length;
        const outOfStockCount = drugs.filter((d) => d.total_stock === 0).length;

        const expiringWarningCount = drugs
            .flatMap((d) => d.batches || [])
            .filter((b) => {
                if (isBatchDepleted(b)) {
                    return false;
                }

                return daysUntil(b.expiry_date) <= 30;
            }).length;

        return {
            totalMedications: drugs.length,
            lowStockCount,
            outOfStockCount,
            expiringWarningCount,
        };
    }, [drugs]);

    const openEditModal = (drug: Drug) => {
        setSelectedItem(drug);
        drugForm.setData({
            name: drug.name,
            dosage_units: drug.dosage_units,
            min_stock_level: drug.min_stock_level,
            description: drug.description || '',
        });
        setBatchValues(emptyBatchForm());
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

        batchForm.transform(() => batchValues);
        batchForm.put(route('drugs.update', selectedItem.id), {
            onSuccess: () => {
                batchForm.reset();
                setBatchValues(emptyBatchForm());
            },
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

    const allBatches = useMemo<BatchRow[]>(() => {
        return (drugs || [])
            .flatMap((d) =>
                (d.batches || []).map((b: DrugBatch) => ({
                    ...b,
                    drugName: d.name,
                    drugDosage: d.dosage_units,
                    drugId: d.id,
                })),
            )
            .sort(
                (a, b) => daysUntil(a.expiry_date) - daysUntil(b.expiry_date),
            );
    }, [drugs]);

    const openDeleteModal = (drug: Drug) => {
        setSelectedItem(drug);
        setDeleteModalOpen(true);
    };

    const openDepleteModal = (batch: BatchRow) => {
        setSelectedBatch(batch);
        setDepleteModalOpen(true);
    };

    return (
        <>
            <Head title={t('general.drugInventory')} />

            <div className="space-y-5">
                <PageHeader
                    eyebrow={t('general.pharmacy')}
                    title={t('general.manageDrugs')}
                    description={t('general.inventoryManagementBatchTracking')}
                    breadcrumbs={[{ label: t('general.drugInventory') }]}
                    actions={
                        <Button href={route('drugs.create')} icon={PlusIcon}>
                            {t('general.addDrug')}
                        </Button>
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        label={t('general.totalFormulations')}
                        value={stats.totalMedications}
                        icon={BeakerIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('general.lowStockItems')}
                        value={stats.lowStockCount}
                        icon={ExclamationTriangleIcon}
                        tone="warning"
                    />
                    <StatCard
                        label={t('general.outOfStockAlerts')}
                        value={stats.outOfStockCount}
                        icon={ArchiveBoxXMarkIcon}
                        tone="critical"
                    />
                    <StatCard
                        label={t('general.expiringWarningBatches')}
                        value={stats.expiringWarningCount}
                        icon={ClockIcon}
                        tone="warning"
                    />
                </StatGrid>

                <div className="space-y-3">
                    <Tabs
                        value={activeTab}
                        onChange={handleTabChange}
                        items={[
                            {
                                value: 'all',
                                label: t('general.allDrugs'),
                                icon: CubeIcon,
                            },
                            {
                                value: 'low-stock',
                                label: t('general.lowStock'),
                                icon: ExclamationTriangleIcon,
                            },
                            {
                                value: 'expiring',
                                label: t('general.expiring'),
                                icon: ClockIcon,
                            },
                        ]}
                    />

                    <Toolbar>
                        <ToolbarSearch
                            value={searchQuery}
                            onChange={handleSearchChange}
                            placeholder={t('general.searchDrugs')}
                        />
                        <ToolbarSpacer />
                        <SegmentedControl
                            value={viewMode}
                            onChange={handleViewModeChange}
                            options={viewOptions}
                        />
                        <Button
                            href={route('drugs.create')}
                            icon={PlusIcon}
                            size="sm"
                        >
                            {t('general.addDrug')}
                        </Button>
                    </Toolbar>
                </div>

                {drugs.length === 0 ? (
                    <EmptyState
                        icon={InboxIcon}
                        title={t('general.noDrugsFound')}
                        description={
                            searchQuery
                                ? t('general.tryRefiningSearch')
                                : t('general.beginCreatingFormulation')
                        }
                        action={{
                            label: t('general.addMedication'),
                            href: route('drugs.create'),
                        }}
                    />
                ) : viewMode === 'grid' ? (
                    <motion.div
                        key="grid"
                        initial={{ opacity: 0, y: 6 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                        className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                    >
                        {drugs.map((drug) => (
                            <DrugCard
                                key={drug.id}
                                drug={drug}
                                onEdit={openEditModal}
                                onDelete={openDeleteModal}
                            />
                        ))}
                    </motion.div>
                ) : (
                    <motion.div
                        key="table"
                        initial={{ opacity: 0, y: 6 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                    >
                        <DrugTable
                            drugs={drugs}
                            onEdit={openEditModal}
                            onDelete={openDeleteModal}
                        />
                    </motion.div>
                )}

                {allBatches.length > 0 && (
                    <SectionCard
                        title={t('general.detailedBatchTracking')}
                        icon={ArchiveBoxIcon}
                        contentClassName="px-0 py-0 sm:px-0"
                    >
                        <BatchTable
                            batches={allBatches}
                            onDeplete={openDepleteModal}
                        />
                    </SectionCard>
                )}
            </div>

            {/* Edit formulation + add a stock batch */}
            <Dialog
                open={editModalOpen}
                onClose={() => setEditModalOpen(false)}
                title={`${t('common.edit')} ${selectedItem?.name ?? ''}`}
                description={t('general.medicationDetails')}
                size="xl"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setEditModalOpen(false)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            onClick={handleUpdateDrug}
                            loading={drugForm.processing}
                        >
                            {drugForm.processing
                                ? t('general.updating')
                                : t('general.updateInformation')}
                        </Button>
                    </>
                }
            >
                <form onSubmit={handleUpdateDrug} className="space-y-5">
                    <div className="grid grid-cols-1 gap-x-4 gap-y-3.5 sm:grid-cols-2">
                        <Field
                            label={t('general.drugName')}
                            htmlFor="edit-drug-name"
                            required
                            error={drugForm.errors.name}
                        >
                            <Input
                                id="edit-drug-name"
                                value={drugForm.data.name}
                                invalid={Boolean(drugForm.errors.name)}
                                onChange={(e) =>
                                    drugForm.setData('name', e.target.value)
                                }
                            />
                        </Field>
                        <Field
                            label={t('general.dosageDosageUnits')}
                            htmlFor="edit-drug-dosage"
                            required
                            error={drugForm.errors.dosage_units}
                        >
                            <Input
                                id="edit-drug-dosage"
                                value={drugForm.data.dosage_units}
                                invalid={Boolean(drugForm.errors.dosage_units)}
                                onChange={(e) =>
                                    drugForm.setData(
                                        'dosage_units',
                                        e.target.value,
                                    )
                                }
                            />
                        </Field>
                        <Field
                            label={t('general.minStockLevel')}
                            htmlFor="edit-drug-min-stock"
                            error={drugForm.errors.min_stock_level}
                        >
                            <Input
                                id="edit-drug-min-stock"
                                type="number"
                                min={0}
                                inputMode="numeric"
                                className="tabular-nums"
                                value={drugForm.data.min_stock_level}
                                invalid={Boolean(
                                    drugForm.errors.min_stock_level,
                                )}
                                onChange={(e) =>
                                    drugForm.setData(
                                        'min_stock_level',
                                        parseInt(e.target.value, 10),
                                    )
                                }
                            />
                        </Field>
                    </div>

                    <div className="border-t border-line pt-4">
                        <div className="mb-3 flex items-center gap-2">
                            <span className="flex h-7 w-7 items-center justify-center rounded-md bg-positive-soft text-positive-ink">
                                <PlusIcon className="h-4 w-4" />
                            </span>
                            <h4 className="text-[13px] font-semibold tracking-tight text-ink">
                                {t('general.addNewStockBatch')}
                            </h4>
                        </div>
                        <BatchQuickAddForm
                            values={batchValues}
                            processing={batchForm.processing}
                            onChange={(field, value) =>
                                setBatchValues((prev) => ({
                                    ...prev,
                                    [field]: value,
                                }))
                            }
                            onCancel={() => setBatchValues(emptyBatchForm())}
                            onSubmit={handleAddBatch}
                        />
                    </div>
                </form>
            </Dialog>

            {/* Confirm depletion */}
            <Dialog
                open={depleteModalOpen}
                onClose={() => setDepleteModalOpen(false)}
                title={t('general.markBatchDepleted')}
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setDepleteModalOpen(false)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button variant="danger" onClick={handleDeplete}>
                            {t('general.confirmDepletion')}
                        </Button>
                    </>
                }
            >
                <Alert
                    tone="warning"
                    title={t('general.markBatchDepleted')}
                    className="mb-0"
                >
                    {t('general.setsStockToZero')}{' '}
                    <span className="font-mono font-semibold text-ink">
                        {selectedBatch?.batch_number}
                    </span>
                    . {t('general.cannotBeUndone')}
                </Alert>
                {selectedBatch && (
                    <div className="mt-4 flex flex-wrap items-center gap-2">
                        <Badge tone="muted">{selectedBatch.drugName}</Badge>
                        <span className="text-[12.5px] text-ink-muted tabular-nums">
                            <ExpiryDate dateStr={selectedBatch.expiry_date} />
                        </span>
                        <span className="text-[12.5px] text-ink-muted tabular-nums">
                            {selectedBatch.current_quantity}{' '}
                            {selectedBatch.drugDosage}
                        </span>
                    </div>
                )}
            </Dialog>

            {/* Confirm deletion */}
            <Dialog
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={t('general.deleteMedication')}
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setDeleteModalOpen(false)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button variant="danger" onClick={handleDelete}>
                            {t('general.deleteMedication')}
                        </Button>
                    </>
                }
            >
                <Alert
                    tone="critical"
                    title={t('general.deleteMedication')}
                    className="mb-0"
                >
                    {t('general.allDataBatchLogsRemoved')}{' '}
                    <span className="font-semibold text-ink">
                        {selectedItem?.name}
                    </span>
                </Alert>
            </Dialog>
        </>
    );
}

/**
 * Layout as a property keeps the shell mounted across Inertia navigations.
 */
function DrugInventoryLayout({ children }: { children: React.ReactNode }) {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle={t('general.manageDrugs')}
            headerSubtitle={t('general.inventoryManagementBatchTracking')}
        >
            {children}
        </AppLayout>
    );
}

DrugInventory.layout = DrugInventoryLayout;
