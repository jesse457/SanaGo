import { ShieldCheckIcon } from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { Badge, ProgressBar } from '@/Components/ui';
import { ClaimStatusBadge } from '@/pages/Finance/components/FinanceStatusBadges';
import { formatMoney } from '@/pages/Finance/components/format';

export interface PaymentSummaryClaim {
    status: string;
    payer?: {
        name: string;
        coverage_percentage: number;
    } | null;
}

interface InvoicePaymentSummaryProps {
    totalAmount: number;
    remainingBalance: number;
    totalPaid: number;
    insuranceCovered?: number;
    claim?: PaymentSummaryClaim | null;
    labels: {
        total: string;
        insurance: string;
        remaining: string;
        paid: string;
        paidProgress: string;
    };
}

/**
 * InvoicePaymentSummary — insurance cover, patient responsibility, cash
 * received and the settlement progress bar. Amounts stay right-aligned and in
 * tabular figures so the rows read as a ledger.
 */
export function InvoicePaymentSummary({
    totalAmount,
    remainingBalance,
    totalPaid,
    insuranceCovered = 0,
    claim,
    labels,
}: InvoicePaymentSummaryProps) {
    const settled =
        Number(totalAmount) > 0
            ? Math.min(100, (Number(totalPaid) / Number(totalAmount)) * 100)
            : 0;

    return (
        <div className="space-y-3 rounded-lg border border-line bg-surface-muted p-4">
            <Row
                label={
                    <span className="text-[13px] text-ink-muted">
                        {labels.total}
                    </span>
                }
                value={
                    <span className="text-[13px] font-semibold text-ink">
                        {formatMoney(totalAmount)}
                    </span>
                }
            />

            {claim && (
                <div className="space-y-1.5">
                    <Row
                        label={
                            <span className="flex items-center gap-1.5 text-[13px] text-accent-ink">
                                <ShieldCheckIcon className="h-4 w-4" />
                                {labels.insurance} ({claim.payer?.name ?? '—'})
                            </span>
                        }
                        value={
                            <span className="text-[13px] font-semibold text-accent-ink">
                                {formatMoney(insuranceCovered)}
                            </span>
                        }
                    />
                    <div className="flex flex-wrap items-center gap-2">
                        <ClaimStatusBadge status={claim.status} />
                        {typeof claim.payer?.coverage_percentage ===
                            'number' && (
                            <Badge tone="muted" variant="outline">
                                {claim.payer.coverage_percentage}%
                            </Badge>
                        )}
                    </div>
                </div>
            )}

            <div className="border-t border-line pt-2">
                <Row
                    label={
                        <span className="text-[13px] font-medium text-ink">
                            {labels.remaining}
                        </span>
                    }
                    value={
                        <span className="text-[13px] font-semibold text-ink">
                            {formatMoney(remainingBalance)}
                        </span>
                    }
                />
            </div>

            <Row
                label={
                    <span className="text-[13px] text-positive-ink">
                        {labels.paid}
                    </span>
                }
                value={
                    <span className="text-[13px] font-semibold text-positive-ink">
                        {formatMoney(totalPaid)}
                    </span>
                }
            />

            <div className="space-y-1.5 pt-1">
                <div className="flex items-center justify-between text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    <span>{labels.paidProgress}</span>
                    <span className="tabular-nums">{Math.round(settled)}%</span>
                </div>
                <ProgressBar
                    value={settled}
                    tone={settled >= 100 ? 'positive' : 'accent'}
                    label={labels.paidProgress}
                />
            </div>
        </div>
    );
}

function Row({ label, value }: { label: ReactNode; value: ReactNode }) {
    return (
        <div className="flex items-baseline justify-between gap-4">
            {label}
            <span className="tabular-nums">{value}</span>
        </div>
    );
}
