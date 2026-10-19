import { BeakerIcon } from '@heroicons/react/24/outline';
import { Avatar, Badge, KeyValue } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

/** Status vocabulary for a lab result record. */
const statusTone: Record<string, BadgeTone> = {
    'In Progress': 'accent',
    In_Progress: 'accent',
    in_progress: 'accent',
    Completed: 'positive',
    completed: 'positive',
    validated: 'positive',
    Cancelled: 'critical',
    cancelled: 'critical',
};

export function collectionTone(status: string): BadgeTone {
    return statusTone[status] ?? 'muted';
}

/**
 * CollectionStatusBadge — the specimen/lifecycle state of the request the
 * result is being entered against. Distinct from the result's own status.
 */
export function CollectionStatusBadge({
    status,
    label,
}: {
    status: string;
    label?: string;
}) {
    return (
        <Badge tone={collectionTone(status)} dot>
            {label ?? status.replace(/_/g, ' ')}
        </Badge>
    );
}

interface ResultRequestSummaryProps {
    patient: {
        first_name: string;
        last_name: string;
        patient_uid?: string;
    };
    testName: string;
    status: string;
    labels: {
        patient: string;
        test: string;
        lifecycle: string;
    };
}

/**
 * ResultRequestSummary — the identity strip above the entry form: who the
 * specimen belongs to, which test it is, and where the request sits in its
 * lifecycle.
 */
export function ResultRequestSummary({
    patient,
    testName,
    status,
    labels,
}: ResultRequestSummaryProps) {
    const patientName = `${patient.first_name} ${patient.last_name}`;

    return (
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div className="flex min-w-0 items-center gap-3 rounded-lg bg-surface-muted px-3.5 py-3">
                <Avatar name={patientName} size="md" />
                <div className="min-w-0">
                    <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                        {labels.patient}
                    </p>
                    <p className="truncate text-[13px] font-semibold text-ink">
                        {patientName}
                    </p>
                    <p className="truncate font-mono text-[10.5px] text-ink-subtle">
                        {patient.patient_uid ?? '—'}
                    </p>
                </div>
            </div>

            <KeyValue
                tile
                icon={BeakerIcon}
                label={labels.test}
                value={testName}
                className="self-stretch px-3.5 py-3"
            />

            <div className="rounded-lg bg-surface-muted px-3.5 py-3">
                <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {labels.lifecycle}
                </p>
                <div className="mt-1.5">
                    <CollectionStatusBadge status={status} />
                </div>
            </div>
        </div>
    );
}
