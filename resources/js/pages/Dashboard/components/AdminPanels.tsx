import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import {
    Card,
    CardHeader,
    CardTitle,
    EmptyState,
    ProgressBar,
    StatCard,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import type { StatTone, StatTrend } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface MetricCardConfig {
    /** Accent key handed down by the role layout config. */
    accent: string;
    label: string;
    value: string;
    subtitle: string;
    icon: React.ComponentType<{ className?: string }>;
    trend?: StatTrend;
    /** Bottom occupancy bar (0–100). */
    occupancy?: { percentage: number };
    /** Completed / waiting / cancelled split. */
    composition?: {
        completed: number;
        waiting: number;
        cancelled: number;
        total: number;
    };
}

const accentTone: Record<string, StatTone> = {
    blue: 'accent',
    violet: 'accent',
    purple: 'accent',
    teal: 'accent',
    amber: 'warning',
    orange: 'warning',
    emerald: 'positive',
    rose: 'critical',
};

/**
 * AdminMetricCard — a KPI tile for the admin dashboard that may carry a bed
 * occupancy bar or an appointment composition bar under the value.
 */
export function AdminMetricCard({
    accent,
    label,
    value,
    subtitle,
    icon,
    trend,
    occupancy,
    composition,
}: MetricCardConfig) {
    const hasFooter = Boolean(occupancy || composition);

    return (
        <StatCard
            label={label}
            value={value}
            icon={icon}
            tone={accentTone[accent] ?? 'neutral'}
            trend={trend}
            footer={
                hasFooter ? (
                    <div className="mt-2.5 space-y-1.5 text-[11px] text-ink-subtle">
                        {subtitle && <p>{subtitle}</p>}
                        {occupancy && (
                            <div>
                                <div className="mb-1 flex items-center justify-between tabular-nums">
                                    <span>
                                        {occupancy.percentage}% occupied
                                    </span>
                                    <span>
                                        {occupancy.percentage > 80
                                            ? 'Critical'
                                            : occupancy.percentage > 60
                                              ? 'High'
                                              : 'Normal'}
                                    </span>
                                </div>
                                <ProgressBar
                                    value={occupancy.percentage}
                                    size="sm"
                                    tone={
                                        occupancy.percentage > 80
                                            ? 'critical'
                                            : occupancy.percentage > 60
                                              ? 'warning'
                                              : 'positive'
                                    }
                                />
                            </div>
                        )}
                        {composition && (
                            <div>
                                <div className="flex h-1.5 w-full overflow-hidden rounded-full bg-surface-inset">
                                    <div
                                        className="bg-positive transition-[width] duration-500 ease-premium"
                                        style={{
                                            width: `${(composition.completed / Math.max(composition.total, 1)) * 100}%`,
                                        }}
                                    />
                                    <div
                                        className="bg-warning transition-[width] duration-500 ease-premium"
                                        style={{
                                            width: `${(composition.waiting / Math.max(composition.total, 1)) * 100}%`,
                                        }}
                                    />
                                    <div
                                        className="bg-critical transition-[width] duration-500 ease-premium"
                                        style={{
                                            width: `${(composition.cancelled / Math.max(composition.total, 1)) * 100}%`,
                                        }}
                                    />
                                </div>
                                <div className="mt-1.5 flex items-center gap-3 tabular-nums">
                                    <span className="flex items-center gap-1">
                                        <span className="h-1.5 w-1.5 rounded-full bg-positive" />
                                        Done {composition.completed}
                                    </span>
                                    <span className="flex items-center gap-1">
                                        <span className="h-1.5 w-1.5 rounded-full bg-warning" />
                                        Wait {composition.waiting}
                                    </span>
                                    <span className="flex items-center gap-1">
                                        <span className="h-1.5 w-1.5 rounded-full bg-critical" />
                                        Cancel {composition.cancelled}
                                    </span>
                                </div>
                            </div>
                        )}
                    </div>
                ) : (
                    subtitle || undefined
                )
            }
        />
    );
}

/**
 * ClinicalChip — the compact count tile in the surgeries / lab /
 * prescriptions strip. Links when an href is supplied.
 */
export function ClinicalChip({
    icon: Icon,
    label,
    value,
    tone = 'accent',
    pulse = false,
}: {
    icon: React.ComponentType<{ className?: string }>;
    label: string;
    value: number;
    tone?: StatTone;
    pulse?: boolean;
}) {
    const toneClasses: Record<StatTone, string> = {
        accent: 'bg-accent-soft text-accent-ink',
        positive: 'bg-positive-soft text-positive-ink',
        warning: 'bg-warning-soft text-warning-ink',
        critical: 'bg-critical-soft text-critical-ink',
        neutral: 'bg-surface-inset text-ink-muted',
    };

    return (
        <div className="flex items-center gap-3 rounded-xl border border-line bg-surface p-3.5 transition-colors duration-200 ease-premium hover:border-line-strong">
            <span
                className={cn(
                    'relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                    toneClasses[tone],
                )}
            >
                {pulse && (
                    <span className="absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-critical ring-2 ring-surface" />
                )}
                <Icon className="h-4 w-4" />
            </span>
            <div className="min-w-0">
                <p className="text-[17px] leading-none font-semibold tracking-[-0.01em] text-ink tabular-nums">
                    {value}
                </p>
                <p className="mt-1 truncate text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {label}
                </p>
            </div>
        </div>
    );
}

export interface DashboardTableColumn {
    label: string;
    numeric?: boolean;
}

/**
 * DashboardTable — the small role-scoped table card used by the admin
 * dashboard panels (low stock, pending labs, waiting patients…).
 */
export function DashboardTable({
    title,
    icon,
    columns,
    rows,
    emptyTitle,
    action,
}: {
    title: string;
    icon: React.ComponentType<{ className?: string }>;
    columns: DashboardTableColumn[];
    rows: ReactNode[][];
    emptyTitle: string;
    action?: ReactNode;
}) {
    return (
        <Card>
            <CardHeader actions={action}>
                <CardTitle>{title}</CardTitle>
            </CardHeader>

            {rows.length > 0 ? (
                <Table bare>
                    <TableHeader>
                        <TableRow>
                            {columns.map((column) => (
                                <TableHeaderCell
                                    key={column.label}
                                    numeric={column.numeric}
                                >
                                    {column.label}
                                </TableHeaderCell>
                            ))}
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {rows.map((row, rowIndex) => (
                            <TableRow key={rowIndex}>
                                {row.map((cell, cellIndex) => (
                                    <TableCell
                                        key={cellIndex}
                                        numeric={columns[cellIndex]?.numeric}
                                        muted
                                    >
                                        {cell}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            ) : (
                <div className="px-5 py-6">
                    <EmptyState panel={false} icon={icon} title={emptyTitle} />
                </div>
            )}
        </Card>
    );
}

/**
 * BreakdownRow — one revenue share row with a labelled progress bar.
 */
export function BreakdownRow({
    label,
    value,
    count,
    total,
    countLabel,
    tone = 'accent',
}: {
    label: string;
    value: number;
    count?: number;
    total: number;
    countLabel?: string;
    tone?: 'accent' | 'positive' | 'warning' | 'critical';
}) {
    const percentage = total > 0 ? (value / total) * 100 : 0;

    return (
        <div className="rounded-lg border border-line bg-surface px-3.5 py-3">
            <div className="mb-2 flex items-center justify-between gap-3">
                <div className="flex min-w-0 items-center gap-2">
                    <span
                        aria-hidden="true"
                        className={cn(
                            'h-1.5 w-1.5 shrink-0 rounded-full',
                            tone === 'positive' && 'bg-positive',
                            tone === 'warning' && 'bg-warning',
                            tone === 'critical' && 'bg-critical',
                            tone === 'accent' && 'bg-accent',
                        )}
                    />
                    <span className="truncate text-[12.5px] font-medium text-ink-muted">
                        {label}
                    </span>
                    {count !== undefined && (
                        <span className="shrink-0 text-[11px] text-ink-subtle tabular-nums">
                            {countLabel}
                        </span>
                    )}
                </div>
                <span className="shrink-0 text-[12.5px] font-semibold text-ink tabular-nums">
                    {value.toLocaleString()} FCFA
                </span>
            </div>
            <ProgressBar value={percentage} size="sm" tone={tone} />
        </div>
    );
}

/**
 * PanelLinkCard — the wide "revenue analytics" navigation tile.
 */
export function PanelLinkCard({
    href,
    label,
    value,
    icon: Icon,
}: {
    href: string;
    label: string;
    value: string;
    icon: React.ComponentType<{ className?: string }>;
}) {
    return (
        <Link
            href={href}
            className="col-span-2 flex items-center gap-3 rounded-xl border border-line bg-surface p-3.5 transition-[border-color,background-color,transform] duration-200 ease-premium hover:border-line-strong hover:bg-surface-hover focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:outline-none active:scale-[0.99]"
        >
            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-accent-soft text-accent-ink">
                <Icon className="h-[17px] w-[17px]" />
            </span>
            <div className="min-w-0 flex-1">
                <p className="truncate text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {label}
                </p>
                <p className="mt-0.5 truncate text-[13px] font-semibold text-accent">
                    {value}
                </p>
            </div>
        </Link>
    );
}
