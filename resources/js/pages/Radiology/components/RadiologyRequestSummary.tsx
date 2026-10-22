import { CameraIcon } from '@heroicons/react/24/outline';
import { Avatar, KeyValue } from '@/Components/ui';
import {
    humanise,
    imagingTypeLabel,
} from '@/pages/Radiology/components/RadiologyRequestCard';
import { RadiologyStatusBadge } from '@/pages/Radiology/components/RadiologyStatusBadge';

interface RadiologyRequestSummaryProps {
    /** Null when the patient record was removed after the request was filed. */
    patient: {
        first_name: string;
        last_name: string;
        patient_uid?: string;
    } | null;
    imagingType?: string | null;
    bodyPart?: string | null;
    status: string;
    labels: {
        patient: string;
        exam: string;
        lifecycle: string;
    };
    /** Already-localised status label for the lifecycle tile. */
    statusLabel?: string;
}

/**
 * RadiologyRequestSummary — the identity strip above the report form: whose
 * exam this is, which study was ordered, and where the order sits in its
 * lifecycle.
 */
export function RadiologyRequestSummary({
    patient,
    imagingType,
    bodyPart,
    status,
    labels,
    statusLabel,
}: RadiologyRequestSummaryProps) {
    const patientName = patient
        ? `${patient.first_name} ${patient.last_name}`.trim() || '—'
        : '—';

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
                        {patient?.patient_uid ?? '—'}
                    </p>
                </div>
            </div>

            <KeyValue
                tile
                icon={CameraIcon}
                label={labels.exam}
                value={`${imagingTypeLabel(imagingType)} · ${humanise(bodyPart)}`}
                className="self-stretch px-3.5 py-3"
            />

            <div className="rounded-lg bg-surface-muted px-3.5 py-3">
                <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {labels.lifecycle}
                </p>
                <div className="mt-1.5">
                    <RadiologyStatusBadge
                        status={status}
                        label={statusLabel ?? humanise(status)}
                        dot
                    />
                </div>
            </div>
        </div>
    );
}
