import {
    PencilSquareIcon,
    PlusIcon,
    TagIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
    Button,
    EmptyState,
    PageHeader,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { BedTypeFormDialog } from './components/BedTypeFormDialog';
import type { BedType } from './components/BedTypeFormDialog';

interface Props {
    bedTypes: BedType[];
}

export default function BedTypeIndex({ bedTypes }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingType, setEditingType] = useState<BedType | null>(null);

    const filteredTypes = useMemo(() => {
        const query = searchQuery.trim().toLowerCase();

        if (!query) {
            return bedTypes;
        }

        return bedTypes.filter((bedType) =>
            bedType.name?.toLowerCase().includes(query),
        );
    }, [bedTypes, searchQuery]);

    const openCreateDialog = () => {
        setEditingType(null);
        setDialogOpen(true);
    };

    const openEditDialog = (bedType: BedType) => {
        setEditingType(bedType);
        setDialogOpen(true);
    };

    const closeDialog = () => {
        setDialogOpen(false);
        setEditingType(null);
    };

    const handleDelete = (bedType: BedType) => {
        if (
            confirm(
                t('page.bed_type.delete_confirm', {
                    name: bedType.name ?? '',
                }),
            )
        ) {
            router.delete(route('bed-types.destroy', bedType.id), {
                preserveScroll: true,
            });
        }
    };

    return (
        <AppLayout
            headerTitle={t('page.bed_type.title')}
            headerSubtitle={t('page.bed_type.subtitle')}
        >
            <Head title={t('page.bed_type.head_title')} />

            <div className="mx-auto max-w-7xl space-y-5 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    eyebrow={t('page.bed_type.title')}
                    title={t('page.bed_type.heading')}
                    description={t('page.bed_type.subtitle')}
                    breadcrumbs={[{ label: t('page.bed_type.head_title') }]}
                    meta={
                        <span className="tabular-nums">
                            {bedTypes.length} definitions
                        </span>
                    }
                    actions={
                        <Button
                            variant="primary"
                            icon={PlusIcon}
                            onClick={openCreateDialog}
                        >
                            {t('page.bed_type.add_type')}
                        </Button>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('page.bed_type.search_placeholder')}
                    />
                    <ToolbarSpacer />
                    <span className="text-[12px] text-ink-subtle tabular-nums">
                        {filteredTypes.length} of {bedTypes.length} shown
                    </span>
                </Toolbar>

                {filteredTypes.length === 0 ? (
                    <EmptyState
                        icon={TagIcon}
                        title={t('page.bed_type.no_bed_types')}
                        action={
                            <Button icon={PlusIcon} onClick={openCreateDialog}>
                                {t('page.bed_type.add_first_type')}
                            </Button>
                        }
                    />
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('page.bed_type.col_name')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('page.bed_type.col_capacity')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('common.actions')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filteredTypes.map((bedType) => (
                                <TableRow key={bedType.id}>
                                    <TableCell className="font-medium">
                                        {bedType.name}
                                    </TableCell>
                                    <TableCell numeric>
                                        {bedType.capacity}
                                        <span className="ml-1 text-[11px] font-medium text-ink-subtle">
                                            {t('page.bed_type.patients')}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                icon={PencilSquareIcon}
                                                aria-label={`${t('common.edit')} ${bedType.name ?? ''}`}
                                                onClick={() =>
                                                    openEditDialog(bedType)
                                                }
                                                className="h-8 w-8"
                                            />
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                icon={TrashIcon}
                                                aria-label={`${t('common.delete')} ${bedType.name ?? ''}`}
                                                onClick={() =>
                                                    handleDelete(bedType)
                                                }
                                                className="h-8 w-8 text-critical-ink hover:bg-critical-soft hover:text-critical-ink"
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}
            </div>

            <BedTypeFormDialog
                key={editingType?.id ?? 'new'}
                open={dialogOpen}
                bedType={editingType}
                onClose={closeDialog}
            />
        </AppLayout>
    );
}
