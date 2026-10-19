import {
    ArrowRightIcon,
    ClockIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Avatar, Button, KeyValue, TableCell, TableRow } from '@/Components/ui';
import { CaseStatusBadge, TriageBadge } from './TriageBadges';

/** The emergency case shape shared by the registry grid and table. */
export interface EmergencyCase {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    attending_doctor?: { id: number; name: string } | null;
    triage_nurse?: { id: number; name: string } | null;
    chief_complaint: string;
    triage_level: string;
    status: string;
    arrived_at: string;
    emergency_fee: number;
}

export function casePatientName(emergency: EmergencyCase): string {
    return [emergency.patient?.first_name, emergency.patient?.last_name]
        .filter(Boolean)
        .join(' ');
}

export function caseArrivalLabel(emergency: EmergencyCase): string {
    return new Date(emergency.arrived_at).toLocaleDateString();
}

/** Copy every card/row needs, resolved once by the page. */
export interface CaseLabels {
    complaining: string;
    arrived: string;
    physician: string;
    unassigned: string;
    fee: string;
    open: string;
    unassignedDoctor: string;
}

interface EmergencyCaseCardProps {
    emergency: EmergencyCase;
    labels: CaseLabels;
    viewHref: string;
}

/**
 * EmergencyCaseCard — one case in the registry grid. Triage and lifecycle
 * badges sit top-right so priority is the first thing scanned.
 */
export function EmergencyCaseCard({
    emergency,
    labels,
    viewHref,
}: EmergencyCaseCardProps) {
    return (
        <article className="flex h-full flex-col rounded-xl border border-line bg-surface p-5 transition-[border-color,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong">
            <div className="mb-3.5 flex items-start justify-between gap-3">
                <Avatar name={casePatientName(emergency)} size="md" />
                <div className="flex flex-col items-end gap-1.5">
                    <TriageBadge level={emergency.triage_level} />
                    <CaseStatusBadge status={emergency.status} />
                </div>
            </div>

            <h3 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                {casePatientName(emergency)}
            </h3>
            <p className="mt-1 line-clamp-2 text-[12.5px] leading-relaxed text-ink-muted">
                <span className="text-ink-subtle">{labels.complaining}: </span>
                {emergency.chief_complaint}
            </p>

            <div className="mt-3.5 grid grid-cols-2 gap-2">
                <KeyValue
                    tile
                    label={labels.arrived}
                    value={caseArrivalLabel(emergency)}
                />
                <KeyValue
                    tile
                    label={labels.physician}
                    value={
                        emergency.attending_doctor?.name ?? labels.unassigned
                    }
                />
            </div>

            <div className="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                <span className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase tabular-nums">
                    {labels.fee}:{' '}
                    {emergency.emergency_fee?.toLocaleString() ?? 0} FCFA
                </span>
                <Button
                    href={viewHref}
                    variant="secondary"
                    size="sm"
                    trailingIcon={ArrowRightIcon}
                >
                    {labels.open}
                </Button>
            </div>
        </article>
    );
}

interface EmergencyCaseRowProps {
    emergency: EmergencyCase;
    labels: CaseLabels;
    viewHref: string;
}

/** The table twin of `EmergencyCaseCard`. */
export function EmergencyCaseRow({
    emergency,
    labels,
    viewHref,
}: EmergencyCaseRowProps) {
    return (
        <TableRow>
            <TableCell>
                <div className="flex min-w-0 items-center gap-3">
                    <Avatar name={casePatientName(emergency)} size="sm" />
                    <div className="min-w-0">
                        <div className="truncate text-[13px] font-semibold text-ink">
                            {casePatientName(emergency)}
                        </div>
                        <div className="truncate text-[11px] text-ink-subtle">
                            {emergency.attending_doctor?.name ??
                                labels.unassignedDoctor}
                        </div>
                    </div>
                </div>
            </TableCell>
            <TableCell muted>
                <span className="line-clamp-1 max-w-xs">
                    {emergency.chief_complaint}
                </span>
            </TableCell>
            <TableCell>
                <TriageBadge level={emergency.triage_level} />
            </TableCell>
            <TableCell>
                <CaseStatusBadge status={emergency.status} />
            </TableCell>
            <TableCell muted>
                <span className="inline-flex items-center gap-1.5 whitespace-nowrap tabular-nums">
                    <ClockIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                    {caseArrivalLabel(emergency)}
                </span>
            </TableCell>
            <TableCell className="text-right">
                <Button
                    href={viewHref}
                    variant="ghost"
                    size="sm"
                    trailingIcon={ArrowRightIcon}
                >
                    {labels.open}
                </Button>
            </TableCell>
        </TableRow>
    );
}

/** Quiet physician marker for the header meta row. */
export function CasePhysicianIcon() {
    return <UserIcon className="h-3.5 w-3.5" />;
}
