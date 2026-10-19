import {
    ArrowUpIcon,
    CubeIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import { Badge, ProgressBar, TableCell, TableRow } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface SupplyListRow {
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

export type SupplyStockStatus = 'out' | 'low' | 'good';

/** Maps the derived stock status onto a kit badge tone. */
export function supplyStatusTone(status: SupplyStockStatus): BadgeTone {
    switch (status) {
        case 'out':
            return 'critical';
        case 'low':
            return 'warning';
        case 'good':
            return 'positive';
        default:
            return 'neutral';
    }
}

interface SupplyRowProps {
    supply: SupplyListRow;
    status: SupplyStockStatus;
    onRestock: () => void;
    onDelete: () => void;
}

/**
 * SupplyRow — one inventory line in the table view. The grid view renders its
 * own card from the same supply shape; this keeps the table and grid showing
 * identical stock numbers, tone mapping and actions.
 */
export function SupplyRow({
    supply,
    status,
    onRestock,
    onDelete,
}: SupplyRowProps) {
    const { t } = useTranslation();

    const statusLabel =
        status === 'out'
            ? t('page.supplies.out_of_stock')
            : status === 'low'
              ? t('page.supplies.low_stock')
              : t('page.supplies.in_stock');

    const totalValue = (
        Number(supply.current_stock) * Number(supply.cost_per_unit)
    ).toFixed(2);

    return (
        <TableRow>
            <TableCell>
                <div className="flex items-center gap-3">
                    <div className="h-9 w-9 shrink-0 overflow-hidden rounded-md border border-line">
                        {supply.image_url ? (
                            <img
                                src={supply.image_url}
                                alt=""
                                className="h-full w-full object-cover"
                            />
                        ) : (
                            <div className="flex h-full w-full items-center justify-center bg-accent-soft text-accent">
                                <CubeIcon className="h-4 w-4" />
                            </div>
                        )}
                    </div>
                    <div className="min-w-0">
                        <div className="truncate text-[13px] font-semibold text-ink">
                            {supply.name}
                        </div>
                        <div className="text-[11px] text-ink-subtle">
                            {t('page.supplies.alert_min')}:{' '}
                            <span className="tabular-nums">
                                {supply.min_stock_level}
                            </span>
                        </div>
                    </div>
                </div>
            </TableCell>
            <TableCell>
                <Badge tone="neutral">{supply.category}</Badge>
            </TableCell>
            <TableCell>
                <div className="w-32 space-y-1.5">
                    <div className="flex items-center justify-between gap-2">
                        <span className="text-[13px] font-semibold text-ink tabular-nums">
                            {supply.current_stock}
                        </span>
                        <span className="text-[10px] font-semibold tracking-[0.08em] text-ink-subtle uppercase">
                            {supply.unit_of_measure}
                        </span>
                    </div>
                    <ProgressBar
                        value={supply.current_stock}
                        max={Math.max(supply.min_stock_level * 2, 1)}
                        tone={
                            status === 'good'
                                ? 'positive'
                                : status === 'low'
                                  ? 'warning'
                                  : 'critical'
                        }
                        size="sm"
                        label={statusLabel}
                    />
                </div>
            </TableCell>
            <TableCell numeric>
                <div className="font-semibold text-ink">
                    ${Number(supply.cost_per_unit).toFixed(2)}
                </div>
                <div className="text-[11px] text-ink-subtle">${totalValue}</div>
            </TableCell>
            <TableCell numeric>
                <div className="flex items-center justify-end gap-1.5">
                    <button
                        type="button"
                        onClick={onRestock}
                        className="rounded-md border border-accent/30 bg-accent-soft p-1.5 text-accent-ink transition-colors duration-150 ease-premium hover:border-accent"
                        title={t('page.supplies.restock')}
                    >
                        <ArrowUpIcon className="h-4 w-4" />
                    </button>
                    <Link
                        href={route('supplies.edit', supply.id)}
                        className="rounded-md border border-line bg-surface p-1.5 text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink"
                        title={t('common.edit')}
                    >
                        <PencilSquareIcon className="h-4 w-4" />
                    </Link>
                    <button
                        type="button"
                        onClick={onDelete}
                        className="rounded-md border border-critical/30 bg-critical-soft p-1.5 text-critical-ink transition-colors duration-150 ease-premium hover:border-critical"
                        title={t('common.delete')}
                    >
                        <TrashIcon className="h-4 w-4" />
                    </button>
                </div>
            </TableCell>
        </TableRow>
    );
}
