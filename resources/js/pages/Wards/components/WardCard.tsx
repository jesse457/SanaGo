import {
    PencilSquareIcon,
    TrashIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Badge, Button, Card, CardContent, ProgressBar } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';
import type { Ward } from '@/pages/Wards/components/WardFormDialog';

interface WardCardProps {
    ward: Ward;
    onEdit: (ward: Ward) => void;
    onDelete: (ward: Ward) => void;
    className?: string;
}

/**
 * WardCard — one ward as a surface: code chip, name, floor and an occupancy
 * bar. Edit/delete actions sit in the footer so they stay reachable on touch
 * (never hover-only).
 */
export function WardCard({ ward, onEdit, onDelete, className }: WardCardProps) {
    const { t } = useTranslation();

    const beds = ward.beds_count || 0;
    const capacity = ward.capacity || 0;
    const isFull = capacity > 0 && beds >= capacity;
    const occupancyTone = isFull ? 'warning' : beds > 0 ? 'accent' : 'positive';

    return (
        <Card
            className={cn(
                'group flex flex-col transition-[border-color,box-shadow] duration-200 ease-premium hover:border-line-strong hover:shadow-surface',
                className,
            )}
        >
            <CardContent className="flex-1">
                <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                        <div className="flex flex-wrap items-center gap-2">
                            {ward.code && (
                                <Badge tone="accent" variant="soft">
                                    {ward.code}
                                </Badge>
                            )}
                            {isFull && (
                                <Badge tone="warning" dot>
                                    At capacity
                                </Badge>
                            )}
                        </div>
                        <h3 className="mt-2.5 truncate text-[15px] font-semibold tracking-tight text-ink">
                            {ward.name}
                        </h3>
                        {ward.floor && (
                            <p className="mt-0.5 truncate text-[12.5px] text-ink-muted">
                                {ward.floor}
                            </p>
                        )}
                    </div>

                    <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                        <UserGroupIcon className="h-[18px] w-[18px]" />
                    </span>
                </div>

                <div className="mt-4">
                    <div className="flex items-baseline justify-between gap-3">
                        <span className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('page.wards.current_capacity')}
                        </span>
                        <span className="text-[13px] font-semibold text-ink tabular-nums">
                            {beds}
                            <span className="text-ink-subtle">
                                {' / '}
                                {capacity}
                            </span>
                            <span className="ml-1 text-[11px] font-medium tracking-normal text-ink-subtle lowercase">
                                {t('page.wards.beds')}
                            </span>
                        </span>
                    </div>
                    <ProgressBar
                        value={beds}
                        max={capacity || 1}
                        tone={occupancyTone}
                        className="mt-2"
                        label={`${ward.name} occupancy`}
                    />
                </div>
            </CardContent>

            <div className="flex items-center justify-end gap-2 border-t border-line px-5 py-3 sm:px-6">
                <Button
                    variant="ghost"
                    size="sm"
                    icon={PencilSquareIcon}
                    onClick={() => onEdit(ward)}
                >
                    {t('common.edit')}
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    icon={TrashIcon}
                    className="text-critical-ink hover:bg-critical-soft hover:text-critical-ink"
                    onClick={() => onDelete(ward)}
                >
                    {t('common.delete')}
                </Button>
            </div>
        </Card>
    );
}
