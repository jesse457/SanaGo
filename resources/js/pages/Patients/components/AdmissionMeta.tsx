import {
    CalendarDaysIcon,
    ClockIcon,
    MapPinIcon,
} from '@heroicons/react/24/outline';
import { Badge } from '@/Components/ui';
import { Avatar } from '@/Components/ui/Avatar';
import { cn } from '@/lib/utils';

/**
 * AdmissionMeta — the location / date / attending clues shared by the
 * admissions grid card and the table rows.
 */
export function AdmissionLocation({
    code,
    ward,
    className,
}: {
    code?: string | null;
    ward?: string | null;
    className?: string;
}) {
    return (
        <div className={cn('flex min-w-0 items-center gap-2', className)}>
            <MapPinIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
            <div className="min-w-0">
                <p className="truncate text-[12px] font-medium text-ink">
                    {code ?? '—'}
                </p>
                {ward && (
                    <p className="truncate text-[11px] text-ink-subtle">
                        {ward}
                    </p>
                )}
            </div>
        </div>
    );
}

export function AdmissionDate({
    date,
    className,
}: {
    date: string;
    className?: string;
}) {
    return (
        <span
            className={cn(
                'inline-flex items-center gap-1.5 text-[12px] font-medium text-ink-muted tabular-nums',
                className,
            )}
        >
            <CalendarDaysIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
            {date}
        </span>
    );
}

export function AttendingPhysician({ name }: { name: string }) {
    return (
        <span className="inline-flex items-center gap-2 text-[12.5px] font-medium text-ink">
            <Avatar name={name} size="xs" />
            {name}
        </span>
    );
}

/** Larger identity block used by the admission summary header. */
export function AdmissionPatientHeader({
    firstName,
    lastName,
    patientUid,
    demographics,
    statusBadge,
    aside,
    className,
}: {
    firstName: string;
    lastName: string;
    patientUid: string;
    demographics?: string | null;
    statusBadge?: React.ReactNode;
    aside?: React.ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between',
                className,
            )}
        >
            <div className="flex min-w-0 items-center gap-3.5">
                <Avatar
                    name={`${firstName} ${lastName}`}
                    size="lg"
                    shape="squircle"
                />
                <div className="min-w-0">
                    <div className="flex flex-wrap items-center gap-2">
                        <h2 className="truncate text-[17px] leading-tight font-semibold tracking-tight text-ink">
                            {firstName} {lastName}
                        </h2>
                        {statusBadge}
                    </div>
                    <div className="mt-1.5 flex flex-wrap items-center gap-2">
                        <Badge
                            tone="accent"
                            variant="outline"
                            className="font-mono"
                        >
                            {patientUid}
                        </Badge>
                        {demographics && (
                            <span className="text-[11px] font-medium text-ink-subtle uppercase">
                                {demographics}
                            </span>
                        )}
                    </div>
                </div>
            </div>
            {aside && <div className="shrink-0">{aside}</div>}
        </div>
    );
}

export function AdmissionSummaryRow({
    icon: Icon,
    label,
    value,
}: {
    icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    label: string;
    value: React.ReactNode;
}) {
    return (
        <div className="flex items-start gap-2.5">
            <Icon className="mt-0.5 h-4 w-4 shrink-0 text-ink-subtle" />
            <div className="min-w-0">
                <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {label}
                </p>
                <p className="mt-0.5 truncate text-[13px] font-medium text-ink">
                    {value}
                </p>
            </div>
        </div>
    );
}

/** "Awaiting bed" chip shown while an admission is still Pending. */
export function PendingPlacementNotice({ label }: { label: string }) {
    return (
        <div className="flex items-center gap-2.5 rounded-lg border border-line bg-surface-muted px-3.5 py-2.5">
            <ClockIcon className="h-4 w-4 shrink-0 text-warning-ink" />
            <span className="text-[11px] font-semibold tracking-[0.1em] text-warning-ink uppercase">
                {label}
            </span>
        </div>
    );
}
