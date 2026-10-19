import { PencilSquareIcon, TrashIcon } from '@heroicons/react/24/outline';
import { Button, ProgressBar } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import {
    ExpiryDate,
    StockBadge,
    stockLevelOf,
} from '@/pages/Drugs/components/DrugBadges';
import { DrugImage } from '@/pages/Drugs/components/DrugImage';
import type { Drug } from '@/pages/Drugs/components/types';

interface DrugCardProps {
    drug: Drug;
    onEdit: (drug: Drug) => void;
    onDelete: (drug: Drug) => void;
}

/**
 * DrugCard — the grid-view tile for one formulation: identity, shelf state,
 * stock against the alert threshold and the earliest live expiry.
 */
export function DrugCard({ drug, onEdit, onDelete }: DrugCardProps) {
    const { t } = useTranslation();
    const level = stockLevelOf(drug);
    const earliestBatch = drug.batches?.find((b) => b.current_quantity > 0);
    const ceiling = Math.max(drug.min_stock_level * 2, drug.total_stock, 1);

    return (
        <article className="group relative flex h-full flex-col rounded-xl border border-line bg-surface p-4 transition-[border-color,box-shadow,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong hover:shadow-surface">
            <div className="flex items-start justify-between gap-3">
                <DrugImage src={drug.image_url} name={drug.name} />
                <StockBadge drug={drug} />
            </div>

            <div className="mt-3 min-w-0 flex-1">
                <h3 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                    {drug.name}
                </h3>
                <p className="truncate text-[12px] text-ink-subtle">
                    {drug.dosage_units}
                </p>

                <div className="mt-3 space-y-2 rounded-lg bg-surface-muted px-3 py-2.5">
                    <div className="flex items-baseline justify-between gap-2">
                        <span className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('general.stock')}
                        </span>
                        <span className="text-[13px] font-semibold text-ink tabular-nums">
                            {drug.total_stock}
                            <span className="ml-1 text-[11px] font-normal text-ink-subtle">
                                / {drug.min_stock_level}
                            </span>
                        </span>
                    </div>
                    <ProgressBar
                        value={drug.total_stock}
                        max={ceiling}
                        size="sm"
                        tone={
                            level === 'ok'
                                ? 'positive'
                                : level === 'low'
                                  ? 'warning'
                                  : 'critical'
                        }
                        label={`${drug.name} ${t('general.stock')}`}
                    />
                    <div className="flex items-baseline justify-between gap-2 pt-0.5">
                        <span className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('general.earliestExpiry')}
                        </span>
                        {earliestBatch ? (
                            <span className="text-[12px] font-medium">
                                <ExpiryDate
                                    dateStr={earliestBatch.expiry_date}
                                />
                            </span>
                        ) : (
                            <span className="text-[12px] text-ink-subtle italic">
                                {t('general.noActiveStock')}
                            </span>
                        )}
                    </div>
                </div>
            </div>

            <div className="mt-3 flex items-center justify-end gap-1 border-t border-line pt-3">
                <Button
                    variant="ghost"
                    size="icon"
                    icon={PencilSquareIcon}
                    aria-label={t('common.edit')}
                    title={t('common.edit')}
                    onClick={() => onEdit(drug)}
                />
                <Button
                    variant="ghost"
                    size="icon"
                    icon={TrashIcon}
                    aria-label={t('common.delete')}
                    title={t('common.delete')}
                    className="hover:text-critical-ink"
                    onClick={() => onDelete(drug)}
                />
            </div>
        </article>
    );
}
