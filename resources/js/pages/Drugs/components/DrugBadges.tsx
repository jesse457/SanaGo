import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { isBatchDepleted, daysUntil } from '@/pages/Drugs/components/types';
import type { Drug, DrugBatch } from '@/pages/Drugs/components/types';

export type StockLevel = 'out' | 'low' | 'ok';

/** The one place that decides whether a formulation is out, low or healthy. */
export function stockLevelOf(drug: {
    total_stock: number;
    min_stock_level: number;
}): StockLevel {
    if (drug.total_stock === 0) {
        return 'out';
    }

    if (drug.total_stock <= drug.min_stock_level) {
        return 'low';
    }

    return 'ok';
}

const stockTone: Record<StockLevel, BadgeTone> = {
    out: 'critical',
    low: 'warning',
    ok: 'positive',
};

/**
 * StockBadge — shelf state for a formulation. Tone is paired with the word, so
 * the reading never depends on colour alone.
 */
export function StockBadge({
    drug,
    className,
}: {
    drug: Drug;
    className?: string;
}) {
    const { t } = useTranslation();
    const level = stockLevelOf(drug);

    const label =
        level === 'out'
            ? t('general.outOfStock')
            : level === 'low'
              ? t('general.lowStock')
              : t('general.inStock');

    return (
        <Badge tone={stockTone[level]} variant="soft" dot className={className}>
            {label}
        </Badge>
    );
}

export type BatchState = 'depleted' | 'expired' | 'near' | 'valid';

/** Depleted wins over expiry: an empty batch cannot go off. */
export function batchStateOf(batch: DrugBatch): BatchState {
    if (isBatchDepleted(batch)) {
        return 'depleted';
    }

    const left = daysUntil(batch.expiry_date);

    if (left < 0) {
        return 'expired';
    }

    if (left <= 30) {
        return 'near';
    }

    return 'valid';
}

const stateTone: Record<BatchState, BadgeTone> = {
    depleted: 'muted',
    expired: 'critical',
    near: 'warning',
    valid: 'positive',
};

/**
 * ExpiryBadge — batch shelf life. `depleted` stays muted, `near` is a warning
 * within 30 days, `expired` is critical.
 */
export function ExpiryBadge({
    batch,
    className,
}: {
    batch: DrugBatch;
    className?: string;
}) {
    const { t } = useTranslation();
    const state = batchStateOf(batch);

    const label =
        state === 'depleted'
            ? t('general.depleted')
            : state === 'expired'
              ? t('general.expired')
              : state === 'near'
                ? t('general.warning')
                : t('general.valid');

    return (
        <Badge tone={stateTone[state]} variant="soft" dot className={className}>
            {label}
        </Badge>
    );
}

/** ExpiryDate — a date that turns critical once it has passed, with the word. */
export function ExpiryDate({
    dateStr,
    className,
}: {
    dateStr: string;
    className?: string;
}) {
    const { t } = useTranslation();
    const expired = daysUntil(dateStr) < 0;

    return (
        <span
            className={[
                expired ? 'text-critical-ink' : 'text-ink',
                'tabular-nums',
                className ?? '',
            ].join(' ')}
        >
            {new Date(dateStr).toLocaleDateString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
            })}
            {expired && (
                <span className="ml-1.5 text-[11px] font-medium">
                    ({t('general.expired')})
                </span>
            )}
        </span>
    );
}
