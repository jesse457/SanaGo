import { BuildingOfficeIcon, PlusIcon } from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
    Button,
    EmptyState,
    PageHeader,
    swal,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {} from '@/lib/utils';
import { WardCard } from './components/WardCard';
import { WardFormDialog } from './components/WardFormDialog';
import type { Ward } from './components/WardFormDialog';

interface Props {
    wards: Ward[];
}

export default function WardIndex({ wards }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingWard, setEditingWard] = useState<Ward | null>(null);

    const filteredWards = useMemo(() => {
        const query = searchQuery.trim().toLowerCase();

        if (!query) {
            return wards;
        }

        return wards.filter(
            (ward) =>
                ward.name?.toLowerCase().includes(query) ||
                ward.code?.toLowerCase().includes(query),
        );
    }, [wards, searchQuery]);

    const totals = useMemo(
        () => ({
            wards: wards.length,
            beds: wards.reduce((sum, ward) => sum + (ward.beds_count || 0), 0),
            capacity: wards.reduce(
                (sum, ward) => sum + (ward.capacity || 0),
                0,
            ),
        }),
        [wards],
    );

    const openCreateDialog = () => {
        setEditingWard(null);
        setDialogOpen(true);
    };

    const openEditDialog = (ward: Ward) => {
        setEditingWard(ward);
        setDialogOpen(true);
    };

    const closeDialog = () => {
        setDialogOpen(false);
        setEditingWard(null);
    };

    const handleDelete = async (ward: Ward) => {
        const confirmed = await swal.confirm({
            tone: 'warning',
            danger: true,
            title: t('page.wards.delete_ward', 'Delete this ward?'),
            message: t('page.wards.delete_confirm', {
                name: ward.name ?? '',
            }),
            confirmLabel: t('common.delete', 'Delete'),
            cancelLabel: t('common.cancel', 'Cancel'),
        });

        if (!confirmed) {
            return;
        }

        router.delete(route('wards.destroy', ward.id), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.wards.title')}
            headerSubtitle={t('page.wards.subtitle')}
        >
            <Head title={t('page.wards.head_title')} />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.wards.title')}
                    title={t('page.wards.heading')}
                    description={t('page.wards.subtitle')}
                    breadcrumbs={[{ label: t('page.wards.head_title') }]}
                    meta={
                        <>
                            <span className="tabular-nums">
                                {totals.wards} wards
                            </span>
                            <span
                                aria-hidden="true"
                                className="text-line-strong"
                            >
                                /
                            </span>
                            <span className="tabular-nums">
                                {totals.beds} of {totals.capacity} beds in use
                            </span>
                        </>
                    }
                    actions={
                        <Button
                            variant="primary"
                            icon={PlusIcon}
                            onClick={openCreateDialog}
                        >
                            {t('page.wards.add_ward')}
                        </Button>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('page.wards.search_placeholder')}
                    />
                    <ToolbarSpacer />
                    <span className="text-[12px] text-ink-subtle tabular-nums">
                        {filteredWards.length} of {wards.length} shown
                    </span>
                </Toolbar>

                {filteredWards.length === 0 ? (
                    <EmptyState
                        icon={BuildingOfficeIcon}
                        title={t('page.wards.no_wards')}
                        description={t('page.wards.get_started')}
                        action={
                            <Button icon={PlusIcon} onClick={openCreateDialog}>
                                {t('page.wards.add_first_ward')}
                            </Button>
                        }
                    />
                ) : (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {filteredWards.map((ward) => (
                            <WardCard
                                key={ward.id}
                                ward={ward}
                                onEdit={openEditDialog}
                                onDelete={handleDelete}
                            />
                        ))}
                    </div>
                )}
            </div>

            <WardFormDialog
                key={editingWard?.id ?? 'new'}
                open={dialogOpen}
                ward={editingWard}
                onClose={closeDialog}
            />
        </AppLayout>
    );
}
