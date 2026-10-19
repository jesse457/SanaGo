import {
    InboxStackIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import {
    Badge,
    Button,
    EmptyState,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import {
    EntityCard,
    entityStatusTone,
} from '@/pages/Settings/components/EntityCard';
import type {
    BedRecord,
    BedTypeRecord,
    WardRecord,
} from '@/pages/Settings/components/types';

type Translate = (key: string) => string;

interface RowActionsProps {
    onEdit: () => void;
    onDelete: () => void;
}

/** Shared edit/delete affordance for every Utilities entity. */
export function RowActions({ onEdit, onDelete }: RowActionsProps) {
    const { t } = useTranslation();

    return (
        <div className="flex items-center gap-1">
            <Button
                variant="ghost"
                size="icon"
                icon={PencilSquareIcon}
                aria-label={t('common.edit')}
                title={t('common.edit')}
                onClick={onEdit}
            />
            <Button
                variant="ghost"
                size="icon"
                icon={TrashIcon}
                aria-label={t('common.delete')}
                title={t('common.delete')}
                className="hover:text-critical-ink"
                onClick={onDelete}
            />
        </div>
    );
}

/** WardList — ward cards with a capacity summary and hover row actions. */
export function WardList({
    wards,
    onEdit,
    onDelete,
}: {
    wards: WardRecord[];
    onEdit: (ward: WardRecord) => void;
    onDelete: (ward: WardRecord) => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            {wards.map((ward) => (
                <EntityCard
                    key={ward.id}
                    eyebrow={ward.code ?? undefined}
                    title={ward.name ?? ''}
                    subtitle={ward.floor ?? undefined}
                    actions={
                        <RowActions
                            onEdit={() => onEdit(ward)}
                            onDelete={() => onDelete(ward)}
                        />
                    }
                >
                    <div className="mt-4 flex items-center justify-between gap-3 rounded-md bg-surface-muted px-3 py-2">
                        <span className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('page.utilities.current_capacity')}
                        </span>
                        <span className="text-[12.5px] font-semibold text-ink tabular-nums">
                            {ward.beds_count || 0} / {ward.capacity || 0}{' '}
                            {t('page.utilities.beds')}
                        </span>
                    </div>
                </EntityCard>
            ))}
        </div>
    );
}

function bedStatusLabel(
    status: string | null | undefined,
    t: Translate,
): string {
    if (status === 'Available') {
        return t('page.utilities.available');
    }

    if (status === 'Occupied') {
        return t('page.utilities.occupied');
    }

    if (status === 'Maintenance') {
        return t('page.utilities.maintenance');
    }

    return t('page.utilities.unknown');
}

/** BedGrid — compact bed tiles; status carried by the badge tone. */
export function BedGrid({
    beds,
    onEdit,
    onDelete,
}: {
    beds: BedRecord[];
    onEdit: (bed: BedRecord) => void;
    onDelete: (bed: BedRecord) => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
            {beds.map((bed) => (
                <EntityCard
                    key={bed.id}
                    title={bed.code ?? ''}
                    subtitle={bed.ward?.name || t('page.utilities.no_ward')}
                    actions={
                        <RowActions
                            onEdit={() => onEdit(bed)}
                            onDelete={() => onDelete(bed)}
                        />
                    }
                >
                    <div className="mt-3 flex flex-col items-start gap-2">
                        <span className="flex h-9 w-9 items-center justify-center rounded-md border border-line bg-surface-muted text-ink-subtle">
                            <InboxStackIcon className="h-4 w-4" />
                        </span>
                        <Badge tone={entityStatusTone(bed.status)} dot>
                            {bedStatusLabel(bed.status, t)}
                        </Badge>
                    </div>
                </EntityCard>
            ))}
        </div>
    );
}

/** BedTypeList — bed types as a quiet table with capacity and row actions. */
export function BedTypeList({
    types,
    onEdit,
    onDelete,
}: {
    types: BedTypeRecord[];
    onEdit: (type: BedTypeRecord) => void;
    onDelete: (type: BedTypeRecord) => void;
}) {
    const { t } = useTranslation();

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>
                        {t('page.utilities.type_name')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.utilities.capacity')}
                    </TableHeaderCell>
                    <TableHeaderCell className="text-right">
                        {t('common.actions')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {types.map((type) => (
                    <TableRow key={type.id}>
                        <TableCell className="font-medium">
                            {type.name}
                        </TableCell>
                        <TableCell numeric muted>
                            {type.capacity ?? 0} {t('page.utilities.patients')}
                        </TableCell>
                        <TableCell>
                            <div className="flex justify-end">
                                <RowActions
                                    onEdit={() => onEdit(type)}
                                    onDelete={() => onDelete(type)}
                                />
                            </div>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

/** UtilitiesEmpty — the single empty state for every Utilities tab. */
export function UtilitiesEmpty({
    title,
    description,
    actionLabel,
    onAction,
}: {
    title: string;
    description?: string;
    actionLabel: string;
    onAction: () => void;
}) {
    return (
        <EmptyState
            icon={InboxStackIcon}
            title={title}
            description={description}
            action={
                <Button size="sm" icon={PlusIcon} onClick={onAction}>
                    {actionLabel}
                </Button>
            }
        />
    );
}
