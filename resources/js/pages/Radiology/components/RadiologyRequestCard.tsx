import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Avatar, TableCell, TableRow } from '@/Components/ui';
import { RadiologyStatusBadge } from '@/pages/Radiology/components/RadiologyStatusBadge';

/**
 * The request shape every radiology screen renders — the queue, the report
 * form summary and the archive rows that echo the exam they belong to.
 */
export interface RadiologyRequestRow {
    id: number;
    status: string;
    request_date?: string | null;
    imaging_type: string;
    body_part: string;
    clinical_notes?: string | null;
    /** Null when the patient record was removed after the request was filed. */
    patient: {
        first_name: string;
        last_name: string;
        patient_uid?: string;
    } | null;
    requested_by?: { id: number; name: string } | null;
    /** Null until a report is recorded for the request. */
    result?: {
        id: number;
        request_id?: number;
        status: string;
        updated_at?: string | null;
    } | null;
}

const IMAGING_TYPE_LABELS: Record<string, string> = {
    x_ray: 'X-Ray',
    ultrasound: 'Ultrasound',
    ct_scan: 'CT Scan',
    mri: 'MRI',
    mammography: 'Mammography',
};

/** `extremity_upper` → `Extremity Upper`, so raw enum values stay readable. */
export function humanise(value?: string | null): string {
    if (!value) {
        return '—';
    }

    return value
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
}

export function imagingTypeLabel(value?: string | null): string {
    if (!value) {
        return '—';
    }

    return IMAGING_TYPE_LABELS[value] ?? humanise(value);
}

/** The exam as one line: modality first, then the body part. */
export function radiologyExamLabel(row: {
    imaging_type?: string | null;
    body_part?: string | null;
}): string {
    return `${imagingTypeLabel(row.imaging_type)} · ${humanise(row.body_part)}`;
}

export function formatRadiologyDate(value?: string | null): string {
    return value ? new Date(value).toLocaleDateString() : '—';
}

export function radiologyPatientName(row: {
    patient: RadiologyRequestRow['patient'];
}): string {
    if (!row.patient) {
        return '—';
    }

    return `${row.patient.first_name} ${row.patient.last_name}`.trim() || '—';
}

export function radiologyPatientUid(row: {
    patient: RadiologyRequestRow['patient'];
}): string | undefined {
    return row.patient?.patient_uid ?? undefined;
}

/** Patient identity block — name over a monospaced health id. */
export function RadiologyPatientIdentity({
    request,
    avatarSize,
}: {
    request: Pick<RadiologyRequestRow, 'patient'>;
    avatarSize?: 'sm' | 'md' | 'lg';
}) {
    const identity = (
        <div className="min-w-0">
            <div className="truncate text-[13px] font-semibold text-ink">
                {radiologyPatientName(request)}
            </div>
            <div className="truncate font-mono text-[10.5px] text-ink-subtle">
                {radiologyPatientUid(request) ?? '—'}
            </div>
        </div>
    );

    if (!avatarSize) {
        return identity;
    }

    return (
        <div className="flex min-w-0 items-center gap-3">
            <Avatar name={radiologyPatientName(request)} size={avatarSize} />
            {identity}
        </div>
    );
}

/** Exam block — modality over the body part, both quiet and uppercase. */
export function RadiologyExamIdentity({
    request,
}: {
    request: Pick<RadiologyRequestRow, 'imaging_type' | 'body_part'>;
}) {
    return (
        <div className="min-w-0">
            <div className="truncate text-[13px] font-medium text-ink">
                {imagingTypeLabel(request.imaging_type)}
            </div>
            <div className="truncate text-[10.5px] tracking-[0.08em] text-ink-subtle uppercase">
                {humanise(request.body_part)}
            </div>
        </div>
    );
}

interface RadiologyRequestCardProps {
    request: RadiologyRequestRow;
    statusLabel?: string;
    /** Already-localised next step, e.g. "Process Result" or "Open Report". */
    actionLabel: string;
    actionHref: string;
    /** Quiet second line under the exam, e.g. the referring clinician. */
    meta?: ReactNode;
}

/**
 * RadiologyRequestCard — the queue tile for one imaging request. Patient →
 * exam → next step, the same hierarchy on every card.
 */
export function RadiologyRequestCard({
    request,
    statusLabel,
    actionLabel,
    actionHref,
    meta,
}: RadiologyRequestCardProps) {
    return (
        <article className="group flex h-full flex-col rounded-xl border border-line bg-surface p-5 transition-[border-color,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong">
            <div className="mb-3.5 flex items-start justify-between gap-3">
                <Avatar name={radiologyPatientName(request)} size="md" />
                <RadiologyStatusBadge
                    status={request.status}
                    label={statusLabel}
                    dot
                />
            </div>

            <h3 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                {radiologyPatientName(request)}
            </h3>
            <p className="mt-0.5 truncate font-mono text-[10.5px] text-ink-subtle">
                {radiologyPatientUid(request) ?? '—'}
            </p>

            <div className="mt-3.5 rounded-lg bg-surface-muted px-3.5 py-2.5">
                <div className="truncate text-[13px] font-medium text-ink">
                    {imagingTypeLabel(request.imaging_type)}
                </div>
                <div className="mt-0.5 text-[10px] tracking-[0.1em] text-ink-subtle uppercase">
                    {humanise(request.body_part)}
                </div>
            </div>

            {meta && (
                <p className="mt-2.5 truncate text-[11.5px] text-ink-muted">
                    {meta}
                </p>
            )}

            <div className="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                <span className="text-[11px] whitespace-nowrap text-ink-subtle tabular-nums">
                    {formatRadiologyDate(request.request_date)}
                </span>
                <Link
                    href={actionHref}
                    className="text-[12.5px] font-semibold text-accent-ink transition-colors duration-150 hover:underline"
                >
                    {actionLabel}
                </Link>
            </div>
        </article>
    );
}

interface RadiologyRequestTableRowProps {
    request: RadiologyRequestRow;
    statusLabel?: string;
    actionLabel: string;
    actionHref: string;
    /** Adds a date column before the status, as the report archive needs. */
    date?: string | null;
    dateIcon?: ReactNode;
}

/** The table twin of `RadiologyRequestCard`. */
export function RadiologyRequestTableRow({
    request,
    statusLabel,
    actionLabel,
    actionHref,
    date,
    dateIcon,
}: RadiologyRequestTableRowProps) {
    return (
        <TableRow>
            <TableCell>
                <RadiologyPatientIdentity request={request} avatarSize="sm" />
            </TableCell>
            <TableCell muted>
                <RadiologyExamIdentity request={request} />
            </TableCell>
            {date !== undefined && (
                <TableCell muted>
                    <span className="inline-flex items-center gap-2 whitespace-nowrap tabular-nums">
                        {dateIcon}
                        {formatRadiologyDate(date)}
                    </span>
                </TableCell>
            )}
            <TableCell>
                <RadiologyStatusBadge
                    status={request.status}
                    label={statusLabel ?? humanise(request.status)}
                    dot
                />
            </TableCell>
            <TableCell className="text-right">
                <Link
                    href={actionHref}
                    className="text-[12.5px] font-semibold text-accent-ink transition-colors duration-150 hover:underline"
                >
                    {actionLabel}
                </Link>
            </TableCell>
        </TableRow>
    );
}
