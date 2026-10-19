import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Avatar, Button, TableCell, TableRow } from '@/Components/ui';
import { ResultStatusBadge } from '@/pages/LabTests/components/ResultStatusBadge';

/** The request shape both the queue and the result archive render. */
export interface LabRequestRow {
    id: number;
    status: string;
    request_date?: string;
    result_date?: string;
    patient: {
        first_name: string;
        last_name: string;
        patient_uid?: string;
    };
    test_definition: {
        test_name: string;
        code?: string;
    };
}

export function formatLabDate(value?: string | null): string {
    return value ? new Date(value).toLocaleDateString() : '—';
}

export function labPatientName(request: LabRequestRow): string {
    return `${request.patient.first_name} ${request.patient.last_name}`;
}

/** Patient identity block — name over a monospaced health id. */
export function LabPatientIdentity({
    request,
    avatarSize,
}: {
    request: LabRequestRow;
    avatarSize?: 'sm' | 'md' | 'lg';
}) {
    const identity = (
        <div className="min-w-0">
            <div className="truncate text-[13px] font-semibold text-ink">
                {labPatientName(request)}
            </div>
            <div className="truncate font-mono text-[10.5px] text-ink-subtle">
                {request.patient.patient_uid ?? '—'}
            </div>
        </div>
    );

    if (!avatarSize) {
        return identity;
    }

    return (
        <div className="flex min-w-0 items-center gap-3">
            <Avatar name={labPatientName(request)} size={avatarSize} />
            {identity}
        </div>
    );
}

/** Test definition block — name plus its quiet code. */
export function LabTestIdentity({ request }: { request: LabRequestRow }) {
    return (
        <div className="min-w-0">
            <div className="truncate text-[13px] font-medium text-ink">
                {request.test_definition.test_name}
            </div>
            {request.test_definition.code && (
                <div className="truncate text-[10.5px] tracking-[0.08em] text-ink-subtle uppercase">
                    {request.test_definition.code}
                </div>
            )}
        </div>
    );
}

interface LabRequestCardProps {
    request: LabRequestRow;
    /** Pre-formatted date line at the card foot. */
    dateLabel: string;
    actionLabel: string;
    actionHref: string;
    statusLabel?: string;
}

/**
 * LabRequestCard — the queue tile for a single lab request. Keeps the
 * patient → test → next-step hierarchy identical on every render.
 */
export function LabRequestCard({
    request,
    dateLabel,
    actionLabel,
    actionHref,
    statusLabel,
}: LabRequestCardProps) {
    return (
        <article className="group flex h-full flex-col rounded-xl border border-line bg-surface p-5 transition-[border-color,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong">
            <div className="mb-3.5 flex items-start justify-between gap-3">
                <Avatar name={labPatientName(request)} size="md" />
                <ResultStatusBadge
                    status={request.status}
                    label={statusLabel}
                />
            </div>

            <h3 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                {labPatientName(request)}
            </h3>
            <p className="mt-0.5 truncate font-mono text-[10.5px] text-ink-subtle">
                {request.patient.patient_uid ?? '—'}
            </p>

            <div className="mt-3.5 rounded-lg bg-surface-muted px-3.5 py-2.5">
                <div className="truncate text-[13px] font-medium text-ink">
                    {request.test_definition.test_name}
                </div>
                {request.test_definition.code && (
                    <div className="mt-0.5 text-[10px] tracking-[0.1em] text-ink-subtle uppercase">
                        {request.test_definition.code}
                    </div>
                )}
            </div>

            <div className="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                <span className="text-[11px] whitespace-nowrap text-ink-subtle tabular-nums">
                    {dateLabel}
                </span>
                <Button href={actionHref} variant="secondary" size="sm">
                    {actionLabel}
                </Button>
            </div>
        </article>
    );
}

interface LabRequestTableRowProps {
    request: LabRequestRow;
    actionLabel: string;
    actionHref: string;
    statusLabel?: string;
    /** Adds a date column before the status, as the result archive needs. */
    date?: string;
    dateIcon?: ReactNode;
}

/** The table twin of `LabRequestCard`. */
export function LabRequestTableRow({
    request,
    actionLabel,
    actionHref,
    statusLabel,
    date,
    dateIcon,
}: LabRequestTableRowProps) {
    return (
        <TableRow>
            <TableCell>
                <LabPatientIdentity request={request} avatarSize="sm" />
            </TableCell>
            <TableCell muted>
                <LabTestIdentity request={request} />
            </TableCell>
            {date !== undefined && (
                <TableCell muted>
                    <span className="inline-flex items-center gap-2 whitespace-nowrap tabular-nums">
                        {dateIcon}
                        {formatLabDate(date)}
                    </span>
                </TableCell>
            )}
            <TableCell>
                <ResultStatusBadge
                    status={request.status}
                    label={statusLabel ?? request.status.replace(/_/g, ' ')}
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
