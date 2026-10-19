import type { ReactNode } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui';
import { cn } from '@/lib/utils';
import { formatMoney } from '@/pages/Finance/components/format';

export interface TotalsLine {
    label: string;
    value: number | string;
    /** Emphasised as the final total. */
    emphasis?: boolean;
    /** Tint for supporting amounts. */
    tone?: 'muted' | 'positive' | 'critical' | 'accent';
    icon?: ReactNode;
}

const toneClasses = {
    muted: 'text-ink-muted',
    positive: 'text-positive-ink',
    critical: 'text-critical-ink',
    accent: 'text-accent-ink',
} as const;

interface InvoiceTotalsPanelProps {
    lines: TotalsLine[];
    total: number | string;
    totalLabel: string;
    title?: string;
    description?: string;
    footer?: ReactNode;
    className?: string;
}

/**
 * InvoiceTotalsPanel — the right-hand totals card of an invoice: quiet
 * supporting lines, hairline rules, and the one ink-solid total. Shared by the
 * invoice detail view and the invoice builder so both agree on alignment.
 */
export function InvoiceTotalsPanel({
    lines,
    total,
    totalLabel,
    title,
    description,
    footer,
    className,
}: InvoiceTotalsPanelProps) {
    return (
        <Card className={cn('overflow-hidden', className)}>
            <CardHeader>
                <CardTitle>{title ?? totalLabel}</CardTitle>
                {description && (
                    <p className="mt-1 text-[13px] leading-relaxed text-ink-muted">
                        {description}
                    </p>
                )}
            </CardHeader>
            <CardContent className="space-y-2">
                <dl className="space-y-2">
                    {lines.map((line) => (
                        <div
                            key={line.label}
                            className="flex items-baseline justify-between gap-4"
                        >
                            <dt className="flex items-center gap-1.5 text-[13px] text-ink-muted">
                                {line.icon}
                                {line.label}
                            </dt>
                            <dd
                                className={cn(
                                    'text-[13px] font-medium tabular-nums',
                                    toneClasses[line.tone ?? 'muted'],
                                )}
                            >
                                {typeof line.value === 'number'
                                    ? formatMoney(line.value)
                                    : line.value}
                            </dd>
                        </div>
                    ))}
                </dl>

                <div className="flex items-baseline justify-between gap-4 border-t border-line pt-3">
                    <span className="text-[13px] font-semibold text-ink">
                        {totalLabel}
                    </span>
                    <span className="text-[17px] font-semibold tracking-tight text-ink tabular-nums">
                        {typeof total === 'number' ? formatMoney(total) : total}
                    </span>
                </div>
            </CardContent>
            {footer}
        </Card>
    );
}
