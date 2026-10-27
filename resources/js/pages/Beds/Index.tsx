import { InboxStackIcon, PlusIcon } from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
    Button,
    EmptyState,
    PageHeader,
    SegmentedControl,
    swal,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { BedFormDialog, BedTransferDialog } from './components/BedFormDialogs';
import { BedTile } from './components/BedTile';
import type { Bed } from './components/BedTile';

interface Option {
    id: number;
    name?: string | null;
}

interface Props {
    beds: Bed[];
    wards: Option[];
    bedTypes: Option[];
}

type StatusFilter = 'all' | 'Available' | 'Occupied' | 'Maintenance';

export default function BedIndex({ beds, wards, bedTypes }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [transferDialogOpen, setTransferDialogOpen] = useState(false);
    const [selectedBed, setSelectedBed] = useState<Bed | null>(null);
    const [editingBed, setEditingBed] = useState<Bed | null>(null);

    const filteredBeds = useMemo(() => {
        const query = searchQuery.trim().toLowerCase();

        return beds.filter((bed) => {
            const matchesStatus =
                statusFilter === 'all' || bed.status === statusFilter;

            if (!matchesStatus) {
                return false;
            }

            if (!query) {
                return true;
            }

            return (
                bed.code?.toLowerCase().includes(query) ||
                bed.ward?.name?.toLowerCase().includes(query)
            );
        });
    }, [beds, searchQuery, statusFilter]);

    /** Ward-grouped sections, in the order wards are provided. */
    const groupedBeds = useMemo(() => {
        const groups = new Map<
            string,
            { key: string; name: string; beds: Bed[] }
        >();

        for (const bed of filteredBeds) {
            const key = bed.ward?.id ? String(bed.ward.id) : 'unassigned';

            if (!groups.has(key)) {
                groups.set(key, {
                    key,
                    name: bed.ward?.name || t('page.beds.no_ward'),
                    beds: [],
                });
            }

            groups.get(key)!.beds.push(bed);
        }

        return [...groups.values()];
    }, [filteredBeds, t]);

    const counts = useMemo(
        () => ({
            total: beds.length,
            available: beds.filter((bed) => bed.status === 'Available').length,
            occupied: beds.filter((bed) => bed.status === 'Occupied').length,
            maintenance: beds.filter((bed) => bed.status === 'Maintenance')
                .length,
        }),
        [beds],
    );

    const openCreateDialog = () => {
        setEditingBed(null);
        setFormDialogOpen(true);
    };

    const openEditDialog = (bed: Bed) => {
        setEditingBed(bed);
        setFormDialogOpen(true);
    };

    const openTransferDialog = (bed: Bed) => {
        setSelectedBed(bed);
        setTransferDialogOpen(true);
    };

    const closeFormDialog = () => {
        setFormDialogOpen(false);
        setEditingBed(null);
    };

    const closeTransferDialog = () => {
        setTransferDialogOpen(false);
        setSelectedBed(null);
    };

    const handleDelete = async (bed: Bed) => {
        const confirmed = await swal.confirm({
            tone: 'warning',
            danger: true,
            title: t('page.beds.delete_bed', 'Delete this bed?'),
            message: t('page.beds.delete_confirm', {
                code: bed.code ?? '',
            }),
            confirmLabel: t('common.delete', 'Delete'),
            cancelLabel: t('common.cancel', 'Cancel'),
        });

        if (!confirmed) {
            return;
        }

        router.delete(route('beds.destroy', bed.id), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.beds.title')}
            headerSubtitle={t('page.beds.subtitle')}
        >
            <Head title={t('page.beds.head_title')} />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.beds.title')}
                    title={t('page.beds.beds_heading')}
                    description={t('page.beds.subtitle')}
                    breadcrumbs={[{ label: t('page.beds.head_title') }]}
                    meta={
                        <>
                            <span className="tabular-nums">
                                {counts.total} beds
                            </span>
                            <span
                                aria-hidden="true"
                                className="text-line-strong"
                            >
                                /
                            </span>
                            <span className="tabular-nums">
                                {counts.available} {t('page.beds.available')}
                            </span>
                            <span
                                aria-hidden="true"
                                className="text-line-strong"
                            >
                                /
                            </span>
                            <span className="tabular-nums">
                                {counts.occupied} {t('page.beds.occupied')}
                            </span>
                        </>
                    }
                    actions={
                        <Button
                            variant="primary"
                            icon={PlusIcon}
                            onClick={openCreateDialog}
                        >
                            {t('page.beds.add_bed')}
                        </Button>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('page.beds.search_placeholder')}
                    />
                    <SegmentedControl
                        value={statusFilter}
                        onChange={(value) =>
                            setStatusFilter(value as StatusFilter)
                        }
                        size="sm"
                        options={[
                            { value: 'all', label: t('common.all') },
                            {
                                value: 'Available',
                                label: t('page.beds.available'),
                            },
                            {
                                value: 'Occupied',
                                label: t('page.beds.occupied'),
                            },
                            {
                                value: 'Maintenance',
                                label: t('page.beds.maintenance'),
                            },
                        ]}
                    />
                    <ToolbarSpacer />
                    <span className="text-[12px] text-ink-subtle tabular-nums">
                        {filteredBeds.length} of {beds.length} shown
                    </span>
                </Toolbar>

                {filteredBeds.length === 0 ? (
                    <EmptyState
                        icon={InboxStackIcon}
                        title={t('page.beds.no_beds')}
                        action={
                            <Button icon={PlusIcon} onClick={openCreateDialog}>
                                {t('page.beds.add_first_bed')}
                            </Button>
                        }
                    />
                ) : (
                    <div className="space-y-6">
                        {groupedBeds.map((group) => (
                            <section key={group.key} className="min-w-0">
                                <div className="mb-2.5 flex items-baseline justify-between gap-3 border-b border-line pb-2">
                                    <h2 className="truncate text-[15px] font-semibold tracking-tight text-ink">
                                        {group.name}
                                    </h2>
                                    <span className="shrink-0 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase tabular-nums">
                                        {group.beds.length} beds
                                    </span>
                                </div>
                                <div className="grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                                    {group.beds.map((bed) => (
                                        <BedTile
                                            key={bed.id}
                                            bed={bed}
                                            onEdit={openEditDialog}
                                            onTransfer={openTransferDialog}
                                            onDelete={handleDelete}
                                        />
                                    ))}
                                </div>
                            </section>
                        ))}
                    </div>
                )}
            </div>

            <BedFormDialog
                key={`form-${editingBed?.id ?? 'new'}`}
                open={formDialogOpen}
                bed={editingBed}
                wards={wards}
                bedTypes={bedTypes}
                onClose={closeFormDialog}
            />

            <BedTransferDialog
                key={`transfer-${selectedBed?.id ?? 'none'}`}
                open={transferDialogOpen}
                bed={selectedBed}
                wards={wards}
                onClose={closeTransferDialog}
            />
        </AppLayout>
    );
}
