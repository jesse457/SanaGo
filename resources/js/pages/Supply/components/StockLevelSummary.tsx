import { InboxStackIcon } from '@heroicons/react/24/outline';
import { ProgressBar } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

interface StockLevelSummaryProps {
    currentStock: string | number;
    minStockLevel: string | number;
}

/**
 * StockLevelSummary — turns the two threshold inputs into a readable gauge: how
 * much of the alert threshold the on-hand quantity covers. Purely derived from
 * the same form values, so it adds no payload.
 */
export function StockLevelSummary({
    currentStock,
    minStockLevel,
}: StockLevelSummaryProps) {
    const { t } = useTranslation();

    const onHand = Number(currentStock) || 0;
    const threshold = Number(minStockLevel) || 0;
    const healthy = threshold > 0 && onHand >= threshold;

    return (
        <div className="mt-1 space-y-2">
            <div className="flex items-baseline justify-between gap-3 text-[12px]">
                <span className="flex items-center gap-1.5 text-ink-subtle">
                    <InboxStackIcon className="h-3.5 w-3.5" />
                    {onHand} / {threshold}
                </span>
                <span
                    className={
                        healthy
                            ? 'font-medium text-positive-ink'
                            : 'font-medium text-warning-ink'
                    }
                >
                    {healthy ? t('inStock') : t('lowStock')}
                </span>
            </div>
            <ProgressBar
                value={onHand}
                max={Math.max(threshold, onHand, 1)}
                tone={
                    threshold === 0
                        ? 'accent'
                        : healthy
                          ? 'positive'
                          : 'warning'
                }
                size="sm"
                label={t('stock')}
            />
        </div>
    );
}
