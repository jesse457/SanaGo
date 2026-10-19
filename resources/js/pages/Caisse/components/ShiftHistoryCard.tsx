import { Badge } from '@/Components/ui';
import type { CashShift } from '@/types';

interface ShiftHistoryCardProps {
    shift: CashShift;
    labels: {
        open: string;
        closed: string;
        opening: string;
        closing: string;
        payments: string;
    };
}

/**
 * ShiftHistoryCard — one past cash shift: status, date and the opening /
 * closing / payment-count reconciliation rows.
 */
export function ShiftHistoryCard({ shift, labels }: ShiftHistoryCardProps) {
    const isOpen = shift.status === 'open';

    return (
        <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3">
            <div className="mb-2 flex items-center justify-between gap-2">
                <Badge tone={isOpen ? 'positive' : 'muted'} dot>
                    {isOpen ? labels.open : labels.closed}
                </Badge>
                <span className="text-[11.5px] text-ink-subtle tabular-nums">
                    {new Date(shift.opened_at).toLocaleDateString()}
                </span>
            </div>

            <div className="space-y-1 text-[12px] text-ink-subtle">
                <div className="flex items-center justify-between gap-2">
                    <span>{labels.opening}</span>
                    <span className="font-medium text-ink-muted tabular-nums">
                        {Number(shift.opening_balance).toLocaleString()} XAF
                    </span>
                </div>
                {shift.closing_balance && (
                    <div className="flex items-center justify-between gap-2">
                        <span>{labels.closing}</span>
                        <span className="font-medium text-ink-muted tabular-nums">
                            {Number(shift.closing_balance).toLocaleString()} XAF
                        </span>
                    </div>
                )}
                <div className="flex items-center justify-between gap-2">
                    <span>{labels.payments}</span>
                    <span className="tabular-nums">
                        {shift.payments_count ?? 0}
                    </span>
                </div>
            </div>
        </div>
    );
}
