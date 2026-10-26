import {
    ArrowDownTrayIcon,
    ArrowUpRightIcon,
    BanknotesIcon,
    BeakerIcon,
    BellIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ChevronDownIcon,
    ClockIcon,
    CubeIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    PlusIcon,
    Squares2X2Icon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { SanaGoMark, SanaGoWordmark } from '@/Components/brand/SanaGoLogo';
import {
    Badge,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { formatCurrency } from '@/lib/format';
import { cn } from '@/lib/utils';

/**
 * AppWindow — the real tenant workspace, drawn at marketing scale.
 *
 * These are not screenshots and they are not illustrations of a product that
 * does not exist: every frame here is assembled from the same UI kit the
 * hospital actually runs (`@/Components/ui`), on the same design tokens, with
 * the same navigation groups, the same status vocabulary and the same money
 * formatting. That is the point of the showcase: what a visitor sees on this
 * page is what the nurse sees after signing in, minus the interaction.
 *
 * The frames are inert (`aria-hidden`) because they are pictures of screens.
 * Anything they need to *say* to a screen reader is said by the caption the
 * page renders underneath them.
 */

export type AppScreenKey =
    | 'dashboard'
    | 'patients'
    | 'consultation'
    | 'laboratory'
    | 'radiology'
    | 'pharmacy'
    | 'wards'
    | 'finance'
    | 'caisse';

interface AppWindowProps {
    screen: AppScreenKey;
    /**
     * `full` keeps the whole chrome (browser bar, sidebar, top bar) for hero
     * and feature placements. `compact` drops the sidebar so the frame still
     * reads inside a narrow column.
     */
    density?: 'full' | 'compact';
    className?: string;
}

const SUBDOMAIN = 'hopital-laquintinie.sanago.app';

/**
 * The real navigation groups, in the real order. Only the group that owns the
 * screen in view is expanded, exactly as the workspace behaves.
 */
const NAV_GROUPS: {
    group: string;
    icon: ReactNode;
    items: string[];
    screen: AppScreenKey;
}[] = [
    {
        group: 'Patient Management',
        icon: <UserGroupIcon className="h-[11px] w-[11px]" />,
        items: ['Patient Directory', 'Admissions', 'Appointments'],
        screen: 'patients',
    },
    {
        group: 'Clinical',
        icon: <CalendarDaysIcon className="h-[11px] w-[11px]" />,
        items: ['New Consultation', 'Certificates'],
        screen: 'consultation',
    },
    {
        group: 'Lab & Diagnostics',
        icon: <BeakerIcon className="h-[11px] w-[11px]" />,
        items: ['Test Catalog', 'Lab Requests', 'Lab Results'],
        screen: 'laboratory',
    },
    {
        group: 'Radiology',
        icon: <CalendarDaysIcon className="h-[11px] w-[11px]" />,
        items: ['Radiology Requests', 'Radiology Results'],
        screen: 'radiology',
    },
    {
        group: 'Pharmacy',
        icon: <CubeIcon className="h-[11px] w-[11px]" />,
        items: ['Drug Inventory', 'Dispensation'],
        screen: 'pharmacy',
    },
    {
        group: 'Infrastructure',
        icon: <Squares2X2Icon className="h-[11px] w-[11px]" />,
        items: ['Wards', 'Beds'],
        screen: 'wards',
    },
    {
        group: 'Finance',
        icon: <BanknotesIcon className="h-[11px] w-[11px]" />,
        items: ['Invoices', 'Revenue Analytics'],
        screen: 'finance',
    },
    {
        group: 'Cash Register',
        icon: <BanknotesIcon className="h-[11px] w-[11px]" />,
        items: ['Caisse', 'Open Shift'],
        screen: 'caisse',
    },
];

/** Which nav row lights up for each screen, and the trail above the title. */
const SCREEN_META: Record<
    AppScreenKey,
    { trail: string; active: string; group?: string }
> = {
    dashboard: { trail: 'Dashboard', active: 'Dashboard' },
    patients: {
        trail: 'Patient Management / Patient Directory',
        active: 'Patient Directory',
        group: 'Patient Management',
    },
    consultation: {
        trail: 'Patient Management / Patient Directory / Consultation',
        active: 'New Consultation',
        group: 'Clinical',
    },
    laboratory: {
        trail: 'Lab & Diagnostics / Lab Requests',
        active: 'Lab Requests',
        group: 'Lab & Diagnostics',
    },
    radiology: {
        trail: 'Radiology / Radiology Requests',
        active: 'Radiology Requests',
        group: 'Radiology',
    },
    pharmacy: {
        trail: 'Pharmacy / Dispensation',
        active: 'Dispensation',
        group: 'Pharmacy',
    },
    wards: {
        trail: 'Infrastructure / Wards',
        active: 'Wards',
        group: 'Infrastructure',
    },
    finance: {
        trail: 'Finance / Invoices',
        active: 'Invoices',
        group: 'Finance',
    },
    caisse: {
        trail: 'Cash Register / Caisse',
        active: 'Caisse',
        group: 'Cash Register',
    },
};

export function AppWindow({
    screen,
    density = 'full',
    className,
}: AppWindowProps) {
    const meta = SCREEN_META[screen];

    return (
        <div
            className={cn(
                'overflow-hidden rounded-xl border border-line bg-canvas shadow-float',
                className,
            )}
            aria-hidden
        >
            {/* Browser chrome: this is a tenant address, not a demo render. */}
            <div className="flex items-center gap-2.5 border-b border-line bg-surface-muted px-3 py-1.5">
                <span className="flex shrink-0 items-center gap-1">
                    <span className="h-1.5 w-1.5 rounded-full bg-critical/50" />
                    <span className="h-1.5 w-1.5 rounded-full bg-warning/60" />
                    <span className="h-1.5 w-1.5 rounded-full bg-positive/60" />
                </span>
                <span className="flex min-w-0 flex-1 items-center gap-1.5 rounded-sm border border-line bg-surface px-2 py-[3px]">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        className="h-2.5 w-2.5 shrink-0 text-positive"
                    >
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg>
                    <span className="truncate font-mono text-[9px] tracking-[0.02em] text-ink-subtle">
                        {SUBDOMAIN}/
                        {meta.trail
                            .split(' / ')
                            .pop()
                            ?.toLowerCase()
                            .replace(/\s+/g, '-')}
                    </span>
                </span>
                <Badge
                    tone="positive"
                    variant="soft"
                    className="hidden shrink-0 text-[8px] sm:inline-flex"
                >
                    Live
                </Badge>
            </div>

            <div className="flex">
                {density === 'full' && <Sidebar screen={screen} />}

                <div className="flex min-w-0 flex-1 flex-col">
                    <TopBar trail={meta.trail} />

                    <div className="@container min-w-0 flex-1 p-3 @sm:p-4">
                        {screen === 'dashboard' && <DashboardScreen />}
                        {screen === 'patients' && <PatientsScreen />}
                        {screen === 'consultation' && <ConsultationScreen />}
                        {screen === 'laboratory' && <LaboratoryScreen />}
                        {screen === 'radiology' && <RadiologyScreen />}
                        {screen === 'pharmacy' && <PharmacyScreen />}
                        {screen === 'wards' && <WardsScreen />}
                        {screen === 'finance' && <FinanceScreen />}
                        {screen === 'caisse' && <CaisseScreen />}
                    </div>
                </div>
            </div>
        </div>
    );
}

/* ── Shell ───────────────────────────────────────────────────────────────── */

function Sidebar({ screen }: { screen: AppScreenKey }) {
    const meta = SCREEN_META[screen];

    return (
        <aside className="hidden w-[164px] shrink-0 flex-col border-r border-line bg-surface sm:flex">
            <div className="flex h-9 items-center gap-1.5 border-b border-line px-2.5">
                <SanaGoMark className="h-[15px] w-auto shrink-0" />
                <SanaGoWordmark size="xs" className="text-[11.5px]" />
            </div>

            <div className="flex-1 overflow-hidden px-1.5 py-1.5">
                <SidebarRow
                    label="Dashboard"
                    active={meta.active === 'Dashboard'}
                    lead={<Squares2X2Icon className="h-[11px] w-[11px]" />}
                />

                {NAV_GROUPS.map((group) => {
                    const open = meta.group === group.group;

                    return (
                        <div key={group.group}>
                            <SidebarRow
                                label={group.group}
                                open={open}
                                lead={group.icon}
                                chevron
                            />

                            {open && (
                                <div className="mt-px mb-1 ml-[13px] space-y-px border-l border-line pl-1.5">
                                    {group.items.map((item) => (
                                        <div
                                            key={item}
                                            className={cn(
                                                'relative flex items-center rounded-sm px-1.5 py-[3px] text-[9.5px] leading-tight',
                                                meta.active === item
                                                    ? 'bg-accent-soft font-semibold text-accent-ink'
                                                    : 'text-ink-muted',
                                            )}
                                        >
                                            {meta.active === item && (
                                                <span className="absolute top-[3px] bottom-[3px] left-0 w-px rounded-full bg-accent" />
                                            )}
                                            <span className="truncate">
                                                {item}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>

            <div className="border-t border-line px-2.5 py-2">
                <p className="font-mono text-[8px] tracking-[0.12em] text-ink-subtle uppercase">
                    Subscription
                </p>
                <p className="mt-0.5 flex items-center gap-1 text-[9.5px] text-ink-muted">
                    <span className="h-1 w-1 rounded-full bg-positive" />
                    Clinical · 214 days left
                </p>
            </div>
        </aside>
    );
}

function SidebarRow({
    label,
    active = false,
    open = false,
    lead,
    chevron = false,
}: {
    label: string;
    active?: boolean;
    open?: boolean;
    lead?: ReactNode;
    chevron?: boolean;
}) {
    return (
        <div
            className={cn(
                'relative flex items-center justify-between gap-1 rounded-sm px-1.5 py-[4px]',
                active
                    ? 'bg-accent-soft font-semibold text-accent-ink'
                    : 'text-ink-muted',
            )}
        >
            {active && (
                <span className="absolute top-[4px] bottom-[4px] left-0 w-px rounded-full bg-accent" />
            )}
            <span className="flex min-w-0 items-center gap-1">
                <span
                    className={cn(
                        'shrink-0',
                        active || open ? 'text-accent' : 'text-ink-subtle',
                    )}
                >
                    {lead}
                </span>
                <span className="truncate text-[9.5px] leading-tight tracking-tight">
                    {label}
                </span>
            </span>
            {chevron && (
                <ChevronDownIcon
                    className={cn(
                        'h-2 w-2 shrink-0 text-ink-subtle transition-transform',
                        open && 'rotate-180',
                    )}
                />
            )}
        </div>
    );
}

function TopBar({ trail }: { trail: string }) {
    return (
        <div className="flex h-9 shrink-0 items-center justify-between gap-2 border-b border-line bg-canvas px-3">
            <p className="truncate font-mono text-[8.5px] tracking-[0.08em] text-ink-subtle uppercase">
                {trail}
            </p>

            <div className="flex shrink-0 items-center gap-1.5">
                <span className="hidden items-center gap-1 rounded-sm border border-line bg-surface px-1.5 py-[2px] text-[8.5px] text-ink-subtle @md:flex">
                    <MagnifyingGlassIcon className="h-2.5 w-2.5" />
                    Search
                </span>
                <span className="relative flex h-5 w-5 items-center justify-center rounded-sm border border-line bg-surface text-ink-muted">
                    <BellIcon className="h-2.5 w-2.5" />
                    <span className="absolute -top-px -right-px h-1.5 w-1.5 rounded-full border border-surface bg-critical" />
                </span>
                <span className="flex h-5 w-5 items-center justify-center rounded-sm bg-accent text-[8px] font-bold text-ink-inverse">
                    SK
                </span>
            </div>
        </div>
    );
}

/* ── Shared content primitives ───────────────────────────────────────────── */

function ScreenTitle({
    eyebrow,
    title,
    meta,
    actions,
}: {
    eyebrow?: string;
    title: string;
    meta?: ReactNode;
    actions?: ReactNode;
}) {
    return (
        <div className="mb-3 flex items-end justify-between gap-3">
            <div className="min-w-0">
                {eyebrow && (
                    <p className="font-mono text-[8px] tracking-[0.16em] text-ink-subtle uppercase">
                        {eyebrow}
                    </p>
                )}
                <h3 className="mt-0.5 truncate text-[14px] leading-tight font-semibold tracking-[-0.02em] text-ink">
                    {title}
                </h3>
                {meta && (
                    <p className="mt-0.5 truncate text-[9.5px] text-ink-muted">
                        {meta}
                    </p>
                )}
            </div>
            {actions && (
                <div className="flex shrink-0 items-center gap-1.5">
                    {actions}
                </div>
            )}
        </div>
    );
}

function MockButton({
    children,
    primary = false,
    icon,
}: {
    children: ReactNode;
    primary?: boolean;
    icon?: ReactNode;
}) {
    return (
        <span
            className={cn(
                'inline-flex items-center gap-1 rounded-sm px-1.5 py-[3px] text-[9px] font-semibold whitespace-nowrap',
                primary
                    ? 'bg-ink text-ink-inverse'
                    : 'border border-line bg-surface text-ink',
            )}
        >
            {icon}
            {children}
        </span>
    );
}

function Kpi({
    label,
    value,
    delta,
    tone = 'neutral',
    hint,
}: {
    label: string;
    value: string;
    delta?: string;
    tone?: 'positive' | 'warning' | 'critical' | 'neutral';
    hint?: string;
}) {
    const toneClasses = {
        positive: 'text-positive-ink',
        warning: 'text-warning-ink',
        critical: 'text-critical-ink',
        neutral: 'text-ink-subtle',
    } as const;

    return (
        <div className="rounded-lg border border-line bg-surface px-2.5 py-2">
            <p className="font-mono text-[8px] leading-tight tracking-[0.1em] text-ink-subtle uppercase">
                {label}
            </p>
            <p
                data-numeric
                title={value}
                className="mt-1 truncate text-[13.5px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums"
            >
                {value}
            </p>
            <div className="mt-1 flex items-center gap-1.5">
                {delta && (
                    <span
                        className={cn(
                            'inline-flex items-center gap-0.5 font-mono text-[8px] font-medium tabular-nums',
                            toneClasses[tone],
                        )}
                    >
                        {tone === 'positive' && (
                            <ArrowUpRightIcon className="h-2 w-2" />
                        )}
                        {tone === 'critical' && (
                            <ExclamationTriangleIcon className="h-2 w-2" />
                        )}
                        {delta}
                    </span>
                )}
                {hint && (
                    <span className="truncate font-mono text-[8px] text-ink-subtle">
                        {hint}
                    </span>
                )}
            </div>
        </div>
    );
}

function Panel({
    title,
    meta,
    children,
    className,
}: {
    title: string;
    meta?: string;
    children: ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'overflow-hidden rounded-lg border border-line bg-surface',
                className,
            )}
        >
            <div className="flex items-center justify-between gap-2 border-b border-line bg-surface-muted px-2.5 py-1.5">
                <p className="truncate font-mono text-[8.5px] tracking-[0.12em] text-ink-muted uppercase">
                    {title}
                </p>
                {meta && (
                    <p className="shrink-0 font-mono text-[8.5px] text-ink-subtle tabular-nums">
                        {meta}
                    </p>
                )}
            </div>
            {children}
        </div>
    );
}

function Initials({ name, tone }: { name: string; tone?: string }) {
    const initials = name
        .split(' ')
        .map((part) => part.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase();

    return (
        <span
            className={cn(
                'flex h-5 w-5 shrink-0 items-center justify-center rounded-sm font-mono text-[8px] font-semibold',
                tone ?? 'bg-surface-inset text-ink-muted',
            )}
        >
            {initials}
        </span>
    );
}

function PatientCell({ name, reference }: { name: string; reference: string }) {
    return (
        <span className="flex items-center gap-1.5">
            <Initials name={name} />
            <span className="min-w-0">
                <span className="block truncate text-[10px] leading-tight font-medium text-ink">
                    {name}
                </span>
                <span className="block truncate font-mono text-[8px] leading-tight text-ink-subtle">
                    {reference}
                </span>
            </span>
        </span>
    );
}

function Status({ tone, children }: { tone: BadgeTone; children: string }) {
    return (
        <Badge tone={tone} variant="soft" className="text-[8px]">
            {children}
        </Badge>
    );
}

function money(amount: number): string {
    return formatCurrency(amount, 'XAF', 'FCFA');
}

/* ── Screens ─────────────────────────────────────────────────────────────── */

const WEEK = [
    { day: 'L', value: 184000 },
    { day: 'M', value: 236500 },
    { day: 'M', value: 211000 },
    { day: 'J', value: 268000 },
    { day: 'V', value: 302500 },
    { day: 'S', value: 141000 },
    { day: 'D', value: 96000 },
];

function DashboardScreen() {
    const peak = Math.max(...WEEK.map((day) => day.value));

    return (
        <>
            <ScreenTitle
                eyebrow="Dashboard"
                title="Hospital overview"
                meta="Thursday 14 November · 08:42"
                actions={
                    <>
                        <MockButton
                            icon={<ArrowDownTrayIcon className="h-2.5 w-2.5" />}
                        >
                            Export
                        </MockButton>
                        <MockButton
                            primary
                            icon={<PlusIcon className="h-2.5 w-2.5" />}
                        >
                            New patient
                        </MockButton>
                    </>
                }
            />

            <div className="grid grid-cols-2 gap-1.5 @md:grid-cols-4">
                <Kpi
                    label="Patients today"
                    value="84"
                    delta="+12"
                    tone="positive"
                    hint="vs yesterday"
                />
                <Kpi
                    label="Inpatients"
                    value="37 / 52"
                    delta="71 %"
                    tone="neutral"
                    hint="beds"
                />
                <Kpi
                    label="Revenue today"
                    value={money(1349000)}
                    delta="+8,4 %"
                    tone="positive"
                />
                <Kpi
                    label="Outstanding"
                    value={money(2184500)}
                    delta="9 overdue"
                    tone="critical"
                />
            </div>

            <div className="mt-2 grid gap-1.5 @lg:grid-cols-12">
                <Panel
                    title="Revenue · last 7 days"
                    meta={money(1439000)}
                    className="@lg:col-span-7"
                >
                    <div className="flex h-[92px] items-end gap-1 px-2.5 pt-3 pb-2">
                        {WEEK.map((day, index) => (
                            <div
                                key={`${day.day}-${index}`}
                                className="flex flex-1 flex-col items-center gap-1"
                            >
                                <span
                                    className={cn(
                                        'w-full rounded-sm',
                                        index === 4
                                            ? 'bg-accent'
                                            : 'bg-accent/25',
                                    )}
                                    style={{
                                        height: `${Math.max(6, (day.value / peak) * 64)}px`,
                                    }}
                                />
                                <span className="font-mono text-[7px] text-ink-subtle">
                                    {day.day}
                                </span>
                            </div>
                        ))}
                    </div>
                    <div className="flex items-center justify-between border-t border-line px-2.5 py-1.5">
                        <span className="font-mono text-[8px] tracking-[0.1em] text-ink-subtle uppercase">
                            Drawer variance
                        </span>
                        <span className="font-mono text-[8.5px] font-medium text-positive-ink tabular-nums">
                            balanced
                        </span>
                    </div>
                </Panel>

                <Panel title="Waiting now" meta="6" className="@lg:col-span-5">
                    <ul className="divide-y divide-line">
                        {[
                            {
                                name: 'Emmanuel Fotso',
                                unit: 'Médecine interne',
                                status: 'Waiting',
                                tone: 'warning' as const,
                                wait: '18 min',
                            },
                            {
                                name: 'Sandrine Mbarga',
                                unit: 'Pédiatrie',
                                status: 'In consultation',
                                tone: 'accent' as const,
                                wait: '6 min',
                            },
                            {
                                name: 'Brice Ondoua',
                                unit: 'Chirurgie · pré-op',
                                status: 'Checked in',
                                tone: 'positive' as const,
                                wait: '3 min',
                            },
                        ].map((row) => (
                            <li
                                key={row.name}
                                className="flex items-center justify-between gap-2 px-2.5 py-1.5"
                            >
                                <PatientCell
                                    name={row.name}
                                    reference={row.unit}
                                />
                                <span className="flex shrink-0 items-center gap-1.5">
                                    <span className="font-mono text-[8px] text-ink-subtle tabular-nums">
                                        {row.wait}
                                    </span>
                                    <Status tone={row.tone}>
                                        {row.status}
                                    </Status>
                                </span>
                            </li>
                        ))}
                    </ul>
                </Panel>
            </div>
        </>
    );
}

const PATIENTS = [
    {
        name: 'Aïcha Ngo Bassa',
        ref: 'SG-2024-0184',
        age: '32 · F',
        unit: 'Consultation externe',
        last: 'Today',
        balance: 5000,
        status: 'Active',
        tone: 'positive' as const,
    },
    {
        name: 'Emmanuel Fotso',
        ref: 'SG-2024-0191',
        age: '41 · M',
        unit: 'Médecine interne',
        last: 'Today',
        balance: 0,
        status: 'Admitted',
        tone: 'accent' as const,
    },
    {
        name: 'Brice Ondoua',
        ref: 'SG-2024-0208',
        age: '55 · M',
        unit: 'Chirurgie',
        last: '12 Nov',
        balance: 47000,
        status: 'Admitted',
        tone: 'accent' as const,
    },
    {
        name: 'Mireille Tchoumi',
        ref: 'SG-2024-0203',
        age: '27 · F',
        unit: 'Laboratoire',
        last: 'Yesterday',
        balance: 12500,
        status: 'Discharged',
        tone: 'neutral' as const,
    },
    {
        name: 'Sandrine Mbarga',
        ref: 'SG-2024-0210',
        age: '8 · F',
        unit: 'Pédiatrie',
        last: '12 Nov',
        balance: 0,
        status: 'Active',
        tone: 'positive' as const,
    },
];

function PatientsScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Patient Management"
                title="Patient Directory"
                meta="1 284 files · searched by name, phone or hospital UID"
                actions={
                    <>
                        <MockButton
                            icon={<ArrowDownTrayIcon className="h-2.5 w-2.5" />}
                        >
                            Export
                        </MockButton>
                        <MockButton
                            primary
                            icon={<PlusIcon className="h-2.5 w-2.5" />}
                        >
                            New patient
                        </MockButton>
                    </>
                }
            />

            <div className="mb-2 flex items-center gap-1.5">
                <span className="flex flex-1 items-center gap-1 rounded-sm border border-line bg-surface px-1.5 py-1 text-[9px] text-ink-subtle">
                    <MagnifyingGlassIcon className="h-2.5 w-2.5" />
                    Search patients
                </span>
                <span className="flex items-center gap-1 rounded-sm border border-line bg-surface px-1.5 py-1 text-[9px] text-ink-muted">
                    All statuses
                    <ChevronDownIcon className="h-2 w-2" />
                </span>
            </div>

            <Table className="max-w-full [&_td]:px-2 [&_td]:py-1.5 [&_th]:px-2 [&_th]:py-1">
                <TableHeader>
                    <TableRow>
                        <TableHeaderCell className="text-[8px]">
                            Patient
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Age
                        </TableHeaderCell>
                        <TableHeaderCell className="hidden text-[8px] @sm:table-cell">
                            Unit
                        </TableHeaderCell>
                        <TableHeaderCell className="hidden text-[8px] @md:table-cell">
                            Last visit
                        </TableHeaderCell>
                        <TableHeaderCell numeric className="text-[8px]">
                            Balance
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Status
                        </TableHeaderCell>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {PATIENTS.map((patient) => (
                        <TableRow key={patient.ref}>
                            <TableCell>
                                <PatientCell
                                    name={patient.name}
                                    reference={patient.ref}
                                />
                            </TableCell>
                            <TableCell className="font-mono text-[9px] whitespace-nowrap text-ink-muted tabular-nums">
                                {patient.age}
                            </TableCell>
                            <TableCell className="hidden text-[9px] whitespace-nowrap text-ink-muted @sm:table-cell">
                                {patient.unit}
                            </TableCell>
                            <TableCell className="hidden font-mono text-[9px] whitespace-nowrap text-ink-subtle @md:table-cell">
                                {patient.last}
                            </TableCell>
                            <TableCell
                                numeric
                                className={cn(
                                    'font-mono text-[9px] whitespace-nowrap tabular-nums',
                                    patient.balance > 0
                                        ? 'text-warning-ink'
                                        : 'text-ink-subtle',
                                )}
                            >
                                {patient.balance > 0
                                    ? money(patient.balance)
                                    : 'settled'}
                            </TableCell>
                            <TableCell>
                                <Status tone={patient.tone}>
                                    {patient.status}
                                </Status>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </>
    );
}

function ConsultationScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="New Consultation"
                title="Aïcha Ngo Bassa"
                meta="SG-2024-0184 · 32 years · female · CNAM policy 8841 220"
                actions={
                    <>
                        <MockButton
                            icon={<BeakerIcon className="h-2.5 w-2.5" />}
                        >
                            Request lab
                        </MockButton>
                        <MockButton primary>Sign &amp; send</MockButton>
                    </>
                }
            />

            <div className="mb-1.5 flex items-center gap-1">
                <Status tone="critical">Penicillin allergy</Status>
                <Status tone="accent">Follow-up</Status>
                <Status tone="positive">Insured</Status>
            </div>

            <div className="grid grid-cols-2 gap-1 @md:grid-cols-4">
                {[
                    { label: 'HR', value: '88', unit: 'bpm' },
                    { label: 'BP', value: '128/82', unit: 'mmHg' },
                    { label: 'SpO₂', value: '97', unit: '%' },
                    { label: 'Temp', value: '37,2', unit: '°C' },
                ].map((vital) => (
                    <div
                        key={vital.label}
                        className="rounded-lg border border-line bg-surface px-2 py-1.5"
                    >
                        <p className="font-mono text-[7.5px] tracking-[0.12em] text-ink-subtle uppercase">
                            {vital.label}
                        </p>
                        <p
                            data-numeric
                            className="mt-0.5 text-[11.5px] leading-none font-semibold text-ink tabular-nums"
                        >
                            {vital.value}
                            <span className="ml-0.5 text-[7.5px] font-normal text-ink-subtle">
                                {vital.unit}
                            </span>
                        </p>
                    </div>
                ))}
            </div>

            <div className="mt-1.5 grid gap-1.5 @lg:grid-cols-12">
                <Panel title="Assessment" className="@lg:col-span-5">
                    <div className="space-y-1.5 px-2.5 py-2">
                        <div>
                            <p className="font-mono text-[7.5px] tracking-[0.12em] text-ink-subtle uppercase">
                                Diagnosis
                            </p>
                            <p className="mt-0.5 text-[9.5px] font-medium text-ink">
                                Simple malaria · ICD B50.9
                            </p>
                        </div>
                        <div>
                            <p className="font-mono text-[7.5px] tracking-[0.12em] text-ink-subtle uppercase">
                                Plan
                            </p>
                            <p className="mt-0.5 text-[9.5px] leading-relaxed text-ink-muted">
                                Artemether combination, review in 72 hours,
                                insecticide-treated net advised.
                            </p>
                        </div>
                    </div>
                </Panel>

                <Panel
                    title="Prescription"
                    meta="3 lines"
                    className="@lg:col-span-7"
                >
                    <ul className="divide-y divide-line">
                        {[
                            {
                                drug: 'Artemether/Lumefantrine 80/480',
                                dose: '1 tablet twice daily · 3 days',
                                stock: 'in stock',
                                tone: 'positive' as const,
                            },
                            {
                                drug: 'Paracétamol 500 mg',
                                dose: '2 tablets three times daily · 5 days',
                                stock: 'in stock',
                                tone: 'positive' as const,
                            },
                            {
                                drug: 'Amoxicilline 500 mg',
                                dose: '1 capsule three times daily · 7 days',
                                stock: '12 left',
                                tone: 'warning' as const,
                            },
                        ].map((line) => (
                            <li
                                key={line.drug}
                                className="flex items-center justify-between gap-2 px-2.5 py-1.5"
                            >
                                <span className="min-w-0">
                                    <span className="block truncate text-[9.5px] font-medium text-ink">
                                        {line.drug}
                                    </span>
                                    <span className="block truncate font-mono text-[8px] text-ink-subtle">
                                        {line.dose}
                                    </span>
                                </span>
                                <Status tone={line.tone}>{line.stock}</Status>
                            </li>
                        ))}
                    </ul>
                    <div className="flex items-center justify-between border-t border-line bg-surface-muted px-2.5 py-1.5">
                        <span className="font-mono text-[8px] tracking-[0.1em] text-ink-subtle uppercase">
                            Dispensing total
                        </span>
                        <span className="font-mono text-[9px] font-semibold text-ink tabular-nums">
                            {money(14200)}
                        </span>
                    </div>
                </Panel>
            </div>
        </>
    );
}

const LAB_ROWS = [
    {
        ref: '#8841',
        name: 'Aïcha Ngo Bassa',
        patient: 'SG-2024-0184',
        test: 'Full blood count',
        priority: 'Routine',
        priorityTone: 'neutral' as const,
        at: '08:12',
        status: 'In progress',
        tone: 'warning' as const,
    },
    {
        ref: '#8842',
        name: 'Brice Ondoua',
        patient: 'SG-2024-0208',
        test: 'Malaria RDT',
        priority: 'Urgent',
        priorityTone: 'critical' as const,
        at: '08:40',
        status: 'Pending',
        tone: 'critical' as const,
    },
    {
        ref: '#8843',
        name: 'Sandrine Mbarga',
        patient: 'SG-2024-0210',
        test: 'Stool examination',
        priority: 'Urgent',
        priorityTone: 'critical' as const,
        at: '09:05',
        status: 'Pending',
        tone: 'critical' as const,
    },
    {
        ref: '#8839',
        name: 'Mireille Tchoumi',
        patient: 'SG-2024-0203',
        test: 'Fasting glucose',
        priority: 'Routine',
        priorityTone: 'neutral' as const,
        at: 'Yesterday',
        status: 'Validated',
        tone: 'positive' as const,
    },
];

function LaboratoryScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Lab & Diagnostics"
                title="Lab requests"
                meta="14 open · oldest first · results entered against the test definition"
                actions={
                    <>
                        <MockButton
                            icon={<ClockIcon className="h-2.5 w-2.5" />}
                        >
                            Queue
                        </MockButton>
                        <MockButton primary>Validate &amp; release</MockButton>
                    </>
                }
            />

            <div className="mb-2 grid grid-cols-3 gap-1.5">
                <Kpi label="Awaiting result" value="9" hint="requests" />
                <Kpi
                    label="Urgent"
                    value="3"
                    delta="same hour"
                    tone="critical"
                />
                <Kpi
                    label="Released today"
                    value="21"
                    delta="+6"
                    tone="positive"
                />
            </div>

            <Table className="max-w-full [&_td]:px-2 [&_td]:py-1.5 [&_th]:px-2 [&_th]:py-1">
                <TableHeader>
                    <TableRow>
                        <TableHeaderCell className="text-[8px]">
                            Request
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Patient
                        </TableHeaderCell>
                        <TableHeaderCell className="hidden text-[8px] @sm:table-cell">
                            Test
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Priority
                        </TableHeaderCell>
                        <TableHeaderCell className="hidden text-[8px] @md:table-cell">
                            Received
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Status
                        </TableHeaderCell>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {LAB_ROWS.map((row) => (
                        <TableRow key={row.ref}>
                            <TableCell className="font-mono text-[9px] whitespace-nowrap text-ink-muted tabular-nums">
                                {row.ref}
                            </TableCell>
                            <TableCell>
                                <PatientCell
                                    name={row.name}
                                    reference={row.patient}
                                />
                            </TableCell>
                            <TableCell className="hidden text-[9px] whitespace-nowrap text-ink-muted @sm:table-cell">
                                {row.test}
                            </TableCell>
                            <TableCell>
                                <Status tone={row.priorityTone}>
                                    {row.priority}
                                </Status>
                            </TableCell>
                            <TableCell className="hidden font-mono text-[9px] text-ink-subtle tabular-nums @md:table-cell">
                                {row.at}
                            </TableCell>
                            <TableCell>
                                <Status tone={row.tone}>{row.status}</Status>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </>
    );
}

function RadiologyScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Radiology"
                title="Imaging requests"
                meta="Modality worklist · findings and conclusion signed by the radiologist"
                actions={<MockButton primary>Sign report</MockButton>}
            />

            <div className="grid gap-1.5 @lg:grid-cols-12">
                <div className="@lg:col-span-8">
                    <Table className="max-w-full [&_td]:px-2 [&_td]:py-1.5 [&_th]:px-2 [&_th]:py-1">
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell className="text-[8px]">
                                    Order
                                </TableHeaderCell>
                                <TableHeaderCell className="text-[8px]">
                                    Patient
                                </TableHeaderCell>
                                <TableHeaderCell className="text-[8px]">
                                    Study
                                </TableHeaderCell>
                                <TableHeaderCell className="text-[8px]">
                                    Status
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {[
                                {
                                    ref: 'RAD-1180',
                                    name: 'Brice Ondoua',
                                    patient: 'SG-2024-0208',
                                    study: 'X-ray · chest',
                                    status: 'Awaiting report',
                                    tone: 'warning' as const,
                                },
                                {
                                    ref: 'RAD-1179',
                                    name: 'Emmanuel Fotso',
                                    patient: 'SG-2024-0191',
                                    study: 'Ultrasound · abdomen',
                                    status: 'Scheduled',
                                    tone: 'accent' as const,
                                },
                                {
                                    ref: 'RAD-1176',
                                    name: 'Mireille Tchoumi',
                                    patient: 'SG-2024-0203',
                                    study: 'CT · head',
                                    status: 'Reported',
                                    tone: 'positive' as const,
                                },
                            ].map((row) => (
                                <TableRow key={row.ref}>
                                    <TableCell className="font-mono text-[9px] whitespace-nowrap text-ink-muted tabular-nums">
                                        {row.ref}
                                    </TableCell>
                                    <TableCell>
                                        <PatientCell
                                            name={row.name}
                                            reference={row.patient}
                                        />
                                    </TableCell>
                                    <TableCell className="hidden text-[9px] whitespace-nowrap text-ink-muted @sm:table-cell">
                                        {row.study}
                                    </TableCell>
                                    <TableCell>
                                        <Status tone={row.tone}>
                                            {row.status}
                                        </Status>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <Panel
                    title="Report · RAD-1176"
                    meta="signed"
                    className="@lg:col-span-4"
                >
                    <div className="px-2.5 py-2">
                        <p className="font-mono text-[7.5px] tracking-[0.12em] text-ink-subtle uppercase">
                            Findings
                        </p>
                        <p className="mt-0.5 text-[9px] leading-relaxed text-ink-muted">
                            No intracranial haemorrhage. Ventricles of normal
                            calibre. No midline shift.
                        </p>
                        <p className="mt-1.5 font-mono text-[7.5px] tracking-[0.12em] text-ink-subtle uppercase">
                            Conclusion
                        </p>
                        <p className="mt-0.5 text-[9px] leading-relaxed text-ink">
                            Unremarkable CT brain.
                        </p>
                        <p className="mt-2 flex items-center gap-1 font-mono text-[8px] text-ink-subtle">
                            <CheckCircleIcon className="h-2.5 w-2.5 text-positive" />
                            Dr. S. Kamdem
                        </p>
                    </div>
                </Panel>
            </div>
        </>
    );
}

function PharmacyScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Pharmacy"
                title="Dispensation"
                meta="Prescription 2401 · Aïcha Ngo Bassa · batch tracked"
                actions={
                    <>
                        <MockButton icon={<CubeIcon className="h-2.5 w-2.5" />}>
                            Stock
                        </MockButton>
                        <MockButton primary>Dispense all</MockButton>
                    </>
                }
            />

            <div className="grid gap-1.5 @lg:grid-cols-12">
                <Panel
                    title="To dispense"
                    meta={money(14200)}
                    className="@lg:col-span-8"
                >
                    <ul className="divide-y divide-line">
                        {[
                            {
                                drug: 'Artemether/Lumefantrine 80/480',
                                qty: '1 box',
                                stock: 42,
                                expiry: 'exp 2027-04',
                            },
                            {
                                drug: 'Paracétamol 500 mg',
                                qty: '20 tablets',
                                stock: 68,
                                expiry: 'exp 2026-11',
                            },
                            {
                                drug: 'Amoxicilline 500 mg',
                                qty: '21 capsules',
                                stock: 12,
                                expiry: 'exp 2026-02',
                            },
                        ].map((line) => (
                            <li key={line.drug} className="px-2.5 py-1.5">
                                <div className="flex items-baseline justify-between gap-2">
                                    <span className="truncate text-[9.5px] font-medium text-ink">
                                        {line.drug}
                                    </span>
                                    <span className="shrink-0 font-mono text-[8.5px] text-ink-muted tabular-nums">
                                        {line.qty}
                                    </span>
                                </div>
                                <div className="mt-1 flex items-center gap-1.5">
                                    <span className="h-[3px] flex-1 overflow-hidden rounded-full bg-surface-inset">
                                        <span
                                            className={cn(
                                                'block h-full rounded-full',
                                                line.stock < 20
                                                    ? 'bg-critical'
                                                    : 'bg-positive',
                                            )}
                                            style={{
                                                width: `${line.stock}%`,
                                            }}
                                        />
                                    </span>
                                    <span className="w-14 shrink-0 text-right font-mono text-[7.5px] text-ink-subtle tabular-nums">
                                        {line.stock} left
                                    </span>
                                    <span className="hidden w-16 shrink-0 text-right font-mono text-[7.5px] text-ink-subtle @sm:block">
                                        {line.expiry}
                                    </span>
                                </div>
                            </li>
                        ))}
                    </ul>
                </Panel>

                <Panel title="Reorder soon" meta="3" className="@lg:col-span-4">
                    <ul className="divide-y divide-line">
                        {[
                            { drug: 'Amoxicilline 500 mg', left: '12' },
                            { drug: 'Diclofénac 75 mg', left: '18' },
                            { drug: 'Sérum salé 0,9 %', left: '24' },
                        ].map((row) => (
                            <li
                                key={row.drug}
                                className="flex items-center justify-between gap-2 px-2.5 py-1.5"
                            >
                                <span className="truncate text-[9px] text-ink">
                                    {row.drug}
                                </span>
                                <span className="shrink-0 font-mono text-[8px] text-critical-ink tabular-nums">
                                    {row.left}
                                </span>
                            </li>
                        ))}
                    </ul>
                </Panel>
            </div>
        </>
    );
}

const BED_ROWS = [
    { ward: 'Ward A', free: 2, total: 20, tone: 'accent' as const },
    { ward: 'Ward B', free: 4, total: 16, tone: 'accent' as const },
    { ward: 'Maternity', free: 2, total: 8, tone: 'positive' as const },
    { ward: 'Intensive care', free: 1, total: 4, tone: 'warning' as const },
];

const BEDS = [
    'occupied',
    'occupied',
    'free',
    'occupied',
    'occupied',
    'service',
    'occupied',
    'free',
    'occupied',
    'occupied',
    'occupied',
    'free',
] as const;

function WardsScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Infrastructure"
                title="Wards & beds"
                meta="37 of 52 beds occupied · live, before the patient arrives"
                actions={<MockButton primary>Admit patient</MockButton>}
            />

            <div className="grid grid-cols-2 gap-1 @md:grid-cols-4">
                {BED_ROWS.map((ward) => (
                    <div
                        key={ward.ward}
                        className="rounded-lg border border-line bg-surface px-2 py-1.5"
                    >
                        <p className="truncate font-mono text-[8px] tracking-[0.1em] text-ink-subtle uppercase">
                            {ward.ward}
                        </p>
                        <p className="mt-0.5 flex items-baseline gap-1">
                            <span
                                data-numeric
                                className="text-[12px] leading-none font-semibold text-ink tabular-nums"
                            >
                                {ward.total - ward.free}
                            </span>
                            <span className="font-mono text-[8px] text-ink-subtle tabular-nums">
                                / {ward.total}
                            </span>
                        </p>
                        <Status tone={ward.tone}>{`${ward.free} free`}</Status>
                    </div>
                ))}
            </div>

            <div className="mt-1.5 grid gap-1.5 @lg:grid-cols-12">
                <Panel
                    title="Ward B · beds"
                    meta="12 / 16"
                    className="@lg:col-span-7"
                >
                    <div className="grid grid-cols-6 gap-1 p-2">
                        {BEDS.map((state, index) => (
                            <span
                                key={index}
                                className={cn(
                                    'flex h-6 items-center justify-center rounded-sm border font-mono text-[7.5px] font-medium',
                                    state === 'occupied' &&
                                        'border-accent/25 bg-accent/12 text-accent-ink',
                                    state === 'free' &&
                                        'border-line bg-surface-inset text-ink-subtle',
                                    state === 'service' &&
                                        'border-warning/30 bg-warning-soft text-warning-ink',
                                )}
                            >
                                {String(index + 1).padStart(2, '0')}
                            </span>
                        ))}
                    </div>
                    <div className="flex items-center gap-2.5 border-t border-line px-2.5 py-1">
                        <Legend className="bg-accent" label="Occupied" />
                        <Legend className="bg-line-strong" label="Free" />
                        <Legend className="bg-warning" label="Service" />
                    </div>
                </Panel>

                <Panel
                    title="Discharges due"
                    meta="today"
                    className="@lg:col-span-5"
                >
                    <ul className="divide-y divide-line">
                        {[
                            { name: 'Emmanuel Fotso', bed: '12 B' },
                            { name: 'Mireille Tchoumi', bed: '04 A' },
                            { name: 'Sandrine Mbarga', bed: '09 B' },
                        ].map((row) => (
                            <li
                                key={row.name}
                                className="flex items-center justify-between gap-2 px-2.5 py-1.5"
                            >
                                <PatientCell
                                    name={row.name}
                                    reference={`bed ${row.bed}`}
                                />
                                <Status tone="accent">Round</Status>
                            </li>
                        ))}
                    </ul>
                </Panel>
            </div>
        </>
    );
}

function Legend({ className, label }: { className: string; label: string }) {
    return (
        <span className="flex items-center gap-1">
            <span className={cn('h-1 w-1 rounded-full', className)} />
            <span className="font-mono text-[7px] tracking-[0.08em] text-ink-subtle uppercase">
                {label}
            </span>
        </span>
    );
}

const INVOICES = [
    {
        ref: 'INV-2025-0417',
        patient: 'SG-2024-0208',
        name: 'Brice Ondoua',
        issued: '12 Nov',
        total: 47000,
        paid: 0,
        status: 'Pending',
        tone: 'warning' as const,
    },
    {
        ref: 'INV-2025-0416',
        patient: 'SG-2024-0184',
        name: 'Aïcha Ngo Bassa',
        issued: '13 Nov',
        total: 5000,
        paid: 5000,
        status: 'Paid',
        tone: 'positive' as const,
    },
    {
        ref: 'INV-2025-0412',
        patient: 'SG-2024-0203',
        name: 'Mireille Tchoumi',
        issued: '11 Nov',
        total: 12500,
        paid: 12500,
        status: 'Paid',
        tone: 'positive' as const,
    },
    {
        ref: 'INV-2025-0409',
        patient: 'SG-2024-0191',
        name: 'Emmanuel Fotso',
        issued: '09 Nov',
        total: 31000,
        paid: 10000,
        status: 'Overdue',
        tone: 'critical' as const,
    },
];

function FinanceScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Finance"
                title="Invoices"
                meta="One invoice per episode · partial payments carry the balance"
                actions={
                    <>
                        <MockButton
                            icon={<ArrowDownTrayIcon className="h-2.5 w-2.5" />}
                        >
                            Export
                        </MockButton>
                        <MockButton primary>New invoice</MockButton>
                    </>
                }
            />

            <div className="mb-2 grid grid-cols-3 gap-1.5">
                <Kpi label="Issued" value="128" hint="this month" />
                <Kpi
                    label="Collected"
                    value={money(4820500)}
                    delta="+14 %"
                    tone="positive"
                />
                <Kpi
                    label="Outstanding"
                    value={money(2184500)}
                    delta="9 overdue"
                    tone="critical"
                />
            </div>

            <Table className="max-w-full [&_td]:px-2 [&_td]:py-1.5 [&_th]:px-2 [&_th]:py-1">
                <TableHeader>
                    <TableRow>
                        <TableHeaderCell className="text-[8px]">
                            Invoice
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Patient
                        </TableHeaderCell>
                        <TableHeaderCell className="hidden text-[8px] @sm:table-cell">
                            Issued
                        </TableHeaderCell>
                        <TableHeaderCell numeric className="text-[8px]">
                            Total
                        </TableHeaderCell>
                        <TableHeaderCell
                            numeric
                            className="hidden text-[8px] @sm:table-cell"
                        >
                            Paid
                        </TableHeaderCell>
                        <TableHeaderCell className="text-[8px]">
                            Status
                        </TableHeaderCell>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {INVOICES.map((invoice) => (
                        <TableRow key={invoice.ref}>
                            <TableCell className="font-mono text-[9px] whitespace-nowrap text-ink-muted tabular-nums">
                                {invoice.ref}
                            </TableCell>
                            <TableCell>
                                <PatientCell
                                    name={invoice.name}
                                    reference={invoice.patient}
                                />
                            </TableCell>
                            <TableCell className="hidden font-mono text-[9px] whitespace-nowrap text-ink-subtle @md:table-cell">
                                {invoice.issued}
                            </TableCell>
                            <TableCell
                                numeric
                                className="font-mono text-[9px] font-medium whitespace-nowrap text-ink tabular-nums"
                            >
                                {money(invoice.total)}
                            </TableCell>
                            <TableCell
                                numeric
                                className="hidden font-mono text-[9px] whitespace-nowrap text-ink-muted tabular-nums @sm:table-cell"
                            >
                                {money(invoice.paid)}
                            </TableCell>
                            <TableCell>
                                <Status tone={invoice.tone}>
                                    {invoice.status}
                                </Status>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </>
    );
}

function CaisseScreen() {
    return (
        <>
            <ScreenTitle
                eyebrow="Cash Register"
                title="Caisse · shift 221"
                meta="Opened 07:45 by S. Kamdem · closed against a counted drawer"
                actions={
                    <>
                        <MockButton>Print shift</MockButton>
                        <MockButton primary>Close shift</MockButton>
                    </>
                }
            />

            <div className="grid grid-cols-2 gap-1.5 @md:grid-cols-4">
                <Kpi label="Opening float" value={money(50000)} />
                <Kpi
                    label="Collected"
                    value={money(412500)}
                    delta="23 payments"
                    tone="positive"
                />
                <Kpi
                    label="Expected"
                    value={money(462500)}
                    hint="float + cash"
                />
                <Kpi
                    label="Counted"
                    value={money(462500)}
                    delta="balanced"
                    tone="positive"
                />
            </div>

            <div className="mt-1.5 grid gap-1.5 @lg:grid-cols-12">
                <Panel
                    title="Payments this shift"
                    meta="23"
                    className="@lg:col-span-8"
                >
                    <ul className="divide-y divide-line">
                        {[
                            {
                                at: '08:22',
                                ref: 'INV-2025-0416',
                                name: 'Aïcha Ngo Bassa',
                                method: 'Cash',
                                amount: 5000,
                            },
                            {
                                at: '08:51',
                                ref: 'INV-2025-0414',
                                name: 'Sandrine Mbarga',
                                method: 'Orange Money',
                                amount: 7500,
                            },
                            {
                                at: '09:14',
                                ref: 'INV-2025-0409',
                                name: 'Emmanuel Fotso',
                                method: 'Cash',
                                amount: 10000,
                            },
                            {
                                at: '09:38',
                                ref: 'INV-2025-0412',
                                name: 'Mireille Tchoumi',
                                method: 'MTN MoMo',
                                amount: 12500,
                            },
                        ].map((payment) => (
                            <li
                                key={payment.ref}
                                className="flex items-center justify-between gap-2 px-2.5 py-1.5"
                            >
                                <span className="flex min-w-0 items-center gap-2">
                                    <span className="shrink-0 font-mono text-[8.5px] text-ink-subtle tabular-nums">
                                        {payment.at}
                                    </span>
                                    <span className="min-w-0">
                                        <span className="block truncate text-[9.5px] font-medium text-ink">
                                            {payment.name}
                                        </span>
                                        <span className="block truncate font-mono text-[8px] text-ink-subtle">
                                            {payment.ref}
                                            {' · '}
                                            {payment.method}
                                        </span>
                                    </span>
                                </span>
                                <span className="shrink-0 font-mono text-[9.5px] font-semibold text-ink tabular-nums">
                                    {money(payment.amount)}
                                </span>
                            </li>
                        ))}
                    </ul>
                </Panel>

                <Panel
                    title="Drawer"
                    meta="reconciled"
                    className="@lg:col-span-4"
                >
                    <div className="space-y-1 px-2.5 py-2">
                        {[
                            { label: 'Cash in', value: money(286000) },
                            { label: 'Mobile money', value: money(126500) },
                            { label: 'Variance', value: money(0) },
                        ].map((line, index) => (
                            <div
                                key={line.label}
                                className={cn(
                                    'flex items-center justify-between gap-2',
                                    index === 2 && 'border-t border-line pt-1',
                                )}
                            >
                                <span className="font-mono text-[8px] tracking-[0.08em] text-ink-subtle uppercase">
                                    {line.label}
                                </span>
                                <span
                                    className={cn(
                                        'font-mono text-[9px] tabular-nums',
                                        index === 2
                                            ? 'font-semibold text-positive-ink'
                                            : 'text-ink',
                                    )}
                                >
                                    {line.value}
                                </span>
                            </div>
                        ))}
                    </div>
                </Panel>
            </div>
        </>
    );
}
