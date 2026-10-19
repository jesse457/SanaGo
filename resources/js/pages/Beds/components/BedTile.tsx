import {
    ArrowsRightLeftIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import type { ComponentType, SVGProps } from 'react';
import { Badge, Button } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

export interface Bed {
    id: number;
    code?: string | null;
    status?: string | null;
    ward_id?: number | null;
    bed_type_id?: number | null;
    ward?: { id?: number; name?: string | null } | null;
    bed_type?: { id?: number; name?: string | null } | null;
}

/** Status → badge tone. The label always accompanies the tone, never color alone. */
export function bedStatusTone(status?: string | null): BadgeTone {
    switch (status) {
        case 'Available':
            return 'positive';
        case 'Occupied':
            return 'critical';
        case 'Maintenance':
            return 'warning';
        case 'Cleaning':
            return 'accent';
        default:
            return 'muted';
    }
}

interface BedTileProps {
    bed: Bed;
    onEdit: (bed: Bed) => void;
    onTransfer: (bed: Bed) => void;
    onDelete: (bed: Bed) => void;
    className?: string;
}

/**
 * BedTile — one bed as a compact, status-coded tile. Each tile carries a status
 * dot, the status label and the ward it belongs to, so the grid reads without
 * relying on color. Actions are always visible (never hover-only).
 */
export function BedTile({
    bed,
    onEdit,
    onTransfer,
    onDelete,
    className,
}: BedTileProps) {
    const { t } = useTranslation();

    const statusLabel = bed.status || t('page.beds.unknown');

    return (
        <div
            className={cn(
                'group relative flex flex-col rounded-md border border-line bg-surface p-3',
                'transition-[border-color,box-shadow,transform] duration-200 ease-premium',
                'hover:-translate-y-0.5 hover:border-line-strong hover:shadow-surface',
                'focus-within:border-line-strong',
                className,
            )}
        >
            <div className="flex items-start justify-between gap-2">
                <p className="truncate text-[13px] font-semibold tracking-tight text-ink tabular-nums">
                    {bed.code}
                </p>
                <Badge tone={bedStatusTone(bed.status)} dot>
                    {statusLabel}
                </Badge>
            </div>

            <p
                className="mt-1 truncate text-[11.5px] text-ink-subtle"
                title={bed.ward?.name ?? undefined}
            >
                {bed.ward?.name || t('page.beds.no_ward')}
            </p>

            {bed.bed_type?.name && (
                <p className="mt-0.5 truncate text-[11px] text-ink-subtle/80">
                    {bed.bed_type.name}
                </p>
            )}

            <div className="mt-2.5 flex items-center gap-1 border-t border-line pt-2">
                <Button
                    variant="ghost"
                    size="icon"
                    icon={
                        PencilSquareIcon as ComponentType<
                            SVGProps<SVGSVGElement>
                        >
                    }
                    aria-label={`${t('page.beds.edit_bed')} ${bed.code}`}
                    onClick={() => onEdit(bed)}
                    className="h-7 w-7"
                />
                <Button
                    variant="ghost"
                    size="icon"
                    icon={ArrowsRightLeftIcon}
                    aria-label={`${t('page.beds.transfer_bed')} ${bed.code}`}
                    onClick={() => onTransfer(bed)}
                    className="h-7 w-7"
                />
                <Button
                    variant="ghost"
                    size="icon"
                    icon={TrashIcon}
                    aria-label={`${t('common.delete')} ${bed.code}`}
                    onClick={() => onDelete(bed)}
                    className="ml-auto h-7 w-7 text-critical-ink hover:bg-critical-soft hover:text-critical-ink"
                />
            </div>
        </div>
    );
}
