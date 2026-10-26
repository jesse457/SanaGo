import { Badge } from '@/Components/ui';
import { formatCurrency, formatCurrencyCompact } from '@/lib/format';
import { cn } from '@/lib/utils';

export type ProductShotVariant =
    | 'queue'
    | 'vitals'
    | 'dispense'
    | 'revenue'
    | 'report'
    | 'ward';

interface ProductShotProps {
    variant: ProductShotVariant;
    className?: string;
}

/**
 * ProductShot — screens from the actual workspace, drawn in markup.
 *
 * Marketing pages usually resort to stock photography of someone frowning at a
 * laptop. These are the opposite: miniature, faithful renderings of the
 * screens a hospital actually works in, built from the same design tokens as
 * the product. They stay crisp at any zoom, weigh nothing, and flip with the
 * theme.
 */
export function ProductShot({ variant, className }: ProductShotProps) {
    return (
        <div
            className={cn(
                'overflow-hidden rounded-xl border border-line bg-surface shadow-raised',
                className,
            )}
            aria-hidden
        >
            {variant === 'queue' && <QueueShot />}
            {variant === 'vitals' && <VitalsShot />}
            {variant === 'dispense' && <DispenseShot />}
            {variant === 'revenue' && <RevenueShot />}
            {variant === 'report' && <ReportShot />}
            {variant === 'ward' && <WardShot />}
        </div>
    );
}

function ShotHeader({
    title,
    meta,
    badge,
}: {
    title: string;
    meta?: string;
    badge?: { label: string; tone: 'accent' | 'positive' | 'warning' };
}) {
    return (
        <div className="flex items-center justify-between gap-3 border-b border-line bg-surface-muted px-3.5 py-2.5">
            <div className="flex min-w-0 items-baseline gap-2">
                <span className="truncate font-mono text-[9.5px] font-medium tracking-[0.16em] text-ink-muted uppercase">
                    {title}
                </span>
                {meta && (
                    <span
                        data-numeric
                        className="shrink-0 font-mono text-[9.5px] text-ink-subtle tabular-nums"
                    >
                        {meta}
                    </span>
                )}
            </div>
            {badge && (
                <Badge tone={badge.tone} variant="soft" className="text-[9px]">
                    {badge.label}
                </Badge>
            )}
        </div>
    );
}

const QUEUE = [
    {
        time: '08:15',
        initials: 'AN',
        name: 'Aïcha Ngo Bassa',
        unit: 'Consultation externe',
        fee: 5000,
        status: 'In consult',
        tone: 'accent' as const,
    },
    {
        time: '08:40',
        initials: 'EF',
        name: 'Emmanuel Fotso',
        unit: 'Maternité · suivi',
        fee: 7500,
        status: 'Waiting',
        tone: 'warning' as const,
    },
    {
        time: '09:05',
        initials: 'MT',
        name: 'Mireille Tchoumi',
        unit: 'Laboratoire',
        fee: 12500,
        status: 'Checked in',
        tone: 'positive' as const,
    },
    {
        time: '09:20',
        initials: 'BO',
        name: 'Brice Ondoua',
        unit: 'Chirurgie · pré-op',
        fee: 25000,
        status: 'Scheduled',
        tone: 'neutral' as const,
    },
];

function QueueShot() {
    return (
        <>
            <ShotHeader
                title="Appointments · today"
                meta="04 / 04"
                badge={{ label: 'Live', tone: 'positive' }}
            />
            <ul className="divide-y divide-line">
                {QUEUE.map((row) => (
                    <li
                        key={row.time}
                        className="flex items-center gap-2.5 px-3.5 py-2.5"
                    >
                        <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-surface-inset font-mono text-[9px] font-semibold text-ink-muted">
                            {row.initials}
                        </span>
                        <span className="min-w-0 flex-1">
                            <span className="block truncate text-[11.5px] leading-tight font-medium text-ink">
                                {row.name}
                            </span>
                            <span className="block truncate text-[10px] leading-tight text-ink-subtle">
                                {row.unit}
                            </span>
                        </span>
                        <span
                            data-numeric
                            className="shrink-0 font-mono text-[10px] text-ink-muted tabular-nums"
                        >
                            {row.time}
                        </span>
                        <Badge
                            tone={row.tone}
                            variant="soft"
                            className="shrink-0 text-[9px]"
                        >
                            {row.status}
                        </Badge>
                    </li>
                ))}
            </ul>
        </>
    );
}

const SPARK = [62, 58, 64, 55, 71, 66, 74, 69, 81, 76, 72, 68];

function VitalsShot() {
    const points = SPARK.map((v, i) => {
        const x = (i / (SPARK.length - 1)) * 100;
        const y = 30 - ((v - 50) / 40) * 26;

        return `${x.toFixed(1)},${y.toFixed(1)}`;
    }).join(' ');

    return (
        <>
            <ShotHeader
                title="Vitals · K. Mbarga"
                meta="08:12"
                badge={{ label: 'Recorded', tone: 'accent' }}
            />
            <div className="grid grid-cols-4 gap-px bg-line">
                {[
                    { label: 'HR', value: '88', unit: 'bpm' },
                    { label: 'BP', value: '128/82', unit: 'mmHg' },
                    { label: 'SpO₂', value: '97', unit: '%' },
                    { label: 'Temp', value: '37.2', unit: '°C' },
                ].map((m) => (
                    <div key={m.label} className="bg-surface px-2.5 py-2.5">
                        <p className="font-mono text-[8.5px] tracking-[0.14em] text-ink-subtle uppercase">
                            {m.label}
                        </p>
                        <p
                            data-numeric
                            className="mt-1 text-[13px] leading-none font-semibold text-ink tabular-nums"
                        >
                            {m.value}
                        </p>
                        <p className="mt-0.5 text-[8.5px] text-ink-subtle">
                            {m.unit}
                        </p>
                    </div>
                ))}
            </div>
            <div className="px-3.5 py-3">
                <div className="flex items-center justify-between">
                    <span className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                        Heart rate · 12h
                    </span>
                    <span
                        data-numeric
                        className="font-mono text-[9px] text-positive-ink tabular-nums"
                    >
                        stable
                    </span>
                </div>
                <svg
                    viewBox="0 0 100 34"
                    className="mt-2 h-10 w-full text-accent"
                    preserveAspectRatio="none"
                >
                    <polyline
                        points={points}
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="1.4"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        vectorEffect="non-scaling-stroke"
                    />
                </svg>
            </div>
        </>
    );
}

const DISPENSE = [
    {
        name: 'Artemether/Lumefantrine 80/480',
        qty: 1,
        unit: 'boîte',
        stock: 42,
    },
    { name: 'Paracétamol 500 mg', qty: 20, unit: 'cp', stock: 68 },
    { name: 'Amoxicilline 500 mg', qty: 21, unit: 'gél.', stock: 12 },
];

function DispenseShot() {
    return (
        <>
            <ShotHeader
                title="Dispense · J. Nkoulou"
                meta="Rx 2401"
                badge={{ label: 'Pending', tone: 'warning' }}
            />
            <ul className="divide-y divide-line">
                {DISPENSE.map((row) => (
                    <li key={row.name} className="px-3.5 py-2.5">
                        <div className="flex items-baseline justify-between gap-3">
                            <span className="truncate text-[11px] leading-tight font-medium text-ink">
                                {row.name}
                            </span>
                            <span
                                data-numeric
                                className="shrink-0 font-mono text-[9.5px] text-ink-muted tabular-nums"
                            >
                                {row.qty} {row.unit}
                            </span>
                        </div>
                        <div className="mt-1.5 flex items-center gap-2">
                            <span className="h-1 flex-1 overflow-hidden rounded-full bg-surface-inset">
                                <span
                                    className={cn(
                                        'block h-full rounded-full',
                                        row.stock < 20
                                            ? 'bg-critical'
                                            : 'bg-positive',
                                    )}
                                    style={{ width: `${row.stock}%` }}
                                />
                            </span>
                            <span
                                data-numeric
                                className="w-10 shrink-0 text-right font-mono text-[8.5px] text-ink-subtle tabular-nums"
                            >
                                {row.stock} left
                            </span>
                        </div>
                    </li>
                ))}
            </ul>
            <div className="flex items-center justify-between border-t border-line bg-surface-muted px-3.5 py-2.5">
                <span className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                    Total
                </span>
                <span
                    data-numeric
                    className="text-[12px] font-semibold text-ink tabular-nums"
                >
                    {formatCurrency(14200, 'XAF', 'FCFA')}
                </span>
            </div>
        </>
    );
}

const DAYS = [
    { day: 'L', value: 184000 },
    { day: 'M', value: 236500 },
    { day: 'M', value: 211000 },
    { day: 'J', value: 268000 },
    { day: 'V', value: 302500 },
    { day: 'S', value: 141000 },
    { day: 'D', value: 96000 },
];

function RevenueShot() {
    const max = Math.max(...DAYS.map((d) => d.value));

    return (
        <>
            <ShotHeader
                title="Caisse · this week"
                meta="7 d"
                badge={{ label: 'Balanced', tone: 'positive' }}
            />
            <div className="px-3.5 pt-3.5">
                <div className="flex items-baseline justify-between">
                    <span
                        data-numeric
                        className="text-[17px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums"
                    >
                        {formatCurrency(1439000, 'XAF', 'FCFA')}
                    </span>
                    <span
                        data-numeric
                        className="font-mono text-[9px] text-positive-ink tabular-nums"
                    >
                        +12,4 %
                    </span>
                </div>
                <div className="mt-3 flex h-[58px] items-end gap-1.5">
                    {DAYS.map((d, i) => (
                        <div
                            key={`${d.day}-${i}`}
                            className="flex flex-1 flex-col items-center gap-1"
                        >
                            <span
                                className={cn(
                                    'w-full rounded-sm',
                                    i === 4 ? 'bg-accent' : 'bg-accent/25',
                                )}
                                style={{
                                    height: `${Math.max(6, (d.value / max) * 48)}px`,
                                }}
                            />
                            <span className="font-mono text-[8px] text-ink-subtle">
                                {d.day}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
            <div className="mt-1 flex items-center justify-between border-t border-line px-3.5 py-2">
                <span className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                    Écart de caisse
                </span>
                <span className="text-[10px] font-medium text-positive-ink">
                    {formatCurrencyCompact(0, 'XAF', 'FCFA')}
                </span>
            </div>
        </>
    );
}

const REPORT = [
    { label: 'Haemoglobin', value: '13,4', unit: 'g/dL', flag: 'normal' },
    { label: 'Leukocytes', value: '11,8', unit: '10³/µL', flag: 'high' },
    { label: 'Platelets', value: '212', unit: '10³/µL', flag: 'normal' },
];

function ReportShot() {
    return (
        <>
            <ShotHeader
                title="Lab result · NFS"
                meta="req 8841"
                badge={{ label: 'Validated', tone: 'positive' }}
            />
            <ul className="divide-y divide-line">
                {REPORT.map((row) => (
                    <li
                        key={row.label}
                        className="flex items-center justify-between gap-3 px-3.5 py-2.5"
                    >
                        <span className="truncate text-[11px] font-medium text-ink">
                            {row.label}
                        </span>
                        <span className="flex shrink-0 items-center gap-2">
                            <span
                                data-numeric
                                className="font-mono text-[10.5px] text-ink tabular-nums"
                            >
                                {row.value}{' '}
                                <span className="text-[8.5px] text-ink-subtle">
                                    {row.unit}
                                </span>
                            </span>
                            <Badge
                                tone={
                                    row.flag === 'high' ? 'warning' : 'positive'
                                }
                                variant="soft"
                                className="text-[8.5px]"
                            >
                                {row.flag}
                            </Badge>
                        </span>
                    </li>
                ))}
            </ul>
            <div className="flex items-center gap-2 border-t border-line bg-surface-muted px-3.5 py-2">
                <span className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                    Signed by
                </span>
                <span className="text-[10px] font-medium text-ink">
                    Dr. S. Kamdem
                </span>
            </div>
        </>
    );
}

const BEDS = [
    'occupied',
    'occupied',
    'free',
    'occupied',
    'maintenance',
    'occupied',
    'free',
    'occupied',
    'occupied',
    'free',
    'occupied',
    'occupied',
] as const;

function WardShot() {
    const occupied = BEDS.filter((b) => b === 'occupied').length;

    return (
        <>
            <ShotHeader
                title="Ward B · beds"
                meta={`${occupied} / ${BEDS.length}`}
                badge={{ label: '58 %', tone: 'accent' }}
            />
            <div className="grid grid-cols-6 gap-1.5 p-3.5">
                {BEDS.map((state, i) => (
                    <span
                        key={i}
                        className={cn(
                            'flex h-7 items-center justify-center rounded-sm border font-mono text-[8px] font-medium',
                            state === 'occupied' &&
                                'border-accent/25 bg-accent/12 text-accent-ink',
                            state === 'free' &&
                                'border-line bg-surface-inset text-ink-subtle',
                            state === 'maintenance' &&
                                'border-warning/30 bg-warning-soft text-warning-ink',
                        )}
                    >
                        {String(i + 1).padStart(2, '0')}
                    </span>
                ))}
            </div>
            <div className="flex items-center gap-3 border-t border-line px-3.5 py-2">
                <LegendDot className="bg-accent" label="Occupied" />
                <LegendDot className="bg-line-strong" label="Free" />
                <LegendDot className="bg-warning" label="Service" />
            </div>
        </>
    );
}

function LegendDot({ className, label }: { className: string; label: string }) {
    return (
        <span className="flex items-center gap-1.5">
            <span className={cn('h-1.5 w-1.5 rounded-full', className)} />
            <span className="font-mono text-[8px] tracking-[0.1em] text-ink-subtle uppercase">
                {label}
            </span>
        </span>
    );
}
