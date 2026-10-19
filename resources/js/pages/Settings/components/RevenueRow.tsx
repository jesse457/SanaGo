import { ProgressBar } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface RevenueRowProps {
    label: string;
    amount: number;
    percentage: number;
    icon: React.ComponentType<{ className?: string }>;
    tone: 'positive' | 'accent' | 'warning' | 'muted';
}

const toneClasses: Record<
    RevenueRowProps['tone'],
    { text: string; bar: 'positive' | 'accent' | 'warning' }
> = {
    positive: { text: 'text-positive-ink', bar: 'positive' },
    accent: { text: 'text-accent-ink', bar: 'accent' },
    warning: { text: 'text-warning-ink', bar: 'warning' },
    muted: { text: 'text-ink-muted', bar: 'accent' },
};

/**
 * RevenueRow — one breakdown line inside the patient revenue dialog: label,
 * amount, and a proportional progress bar.
 */
export function RevenueRow({
    label,
    amount,
    percentage,
    icon: Icon,
    tone,
}: RevenueRowProps) {
    const { t } = useTranslation();
    const formatFCFA = (num: number) =>
        new Intl.NumberFormat('fr-FR').format(num);
    const classes = toneClasses[tone];

    return (
        <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3">
            <div className="mb-2 flex items-center justify-between gap-3">
                <div className="flex min-w-0 items-center gap-2">
                    <Icon className={`h-4 w-4 shrink-0 ${classes.text}`} />
                    <span className="truncate text-[13px] font-medium text-ink">
                        {label}
                    </span>
                </div>
                <span
                    className={`shrink-0 text-[13px] font-semibold tabular-nums ${classes.text}`}
                >
                    FCFA {formatFCFA(amount)}
                </span>
            </div>
            <ProgressBar
                value={Math.min(percentage, 100)}
                tone={classes.bar}
                size="sm"
            />
            <p className="mt-1.5 text-[11.5px] text-ink-subtle tabular-nums">
                {percentage.toFixed(1)}% {t('page.analytics.of_total')}
            </p>
        </div>
    );
}
