import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

export type AdmissionStatus = 'Admitted' | 'Discharged' | 'Pending';

const statusTone: Record<AdmissionStatus, BadgeTone> = {
    Admitted: 'positive',
    Pending: 'warning',
    Discharged: 'muted',
};

export function admissionStatusTone(status: string): BadgeTone {
    return statusTone[status as AdmissionStatus] ?? 'neutral';
}

interface AdmissionStatusBadgeProps {
    status: string;
    label?: string;
    className?: string;
}

/**
 * AdmissionStatusBadge — the one mapping from an admission status string to a
 * semantic badge tone. Every admissions screen renders status through this.
 */
export function AdmissionStatusBadge({
    status,
    label,
    className,
}: AdmissionStatusBadgeProps) {
    return (
        <Badge
            tone={admissionStatusTone(status)}
            variant="soft"
            dot
            className={className}
        >
            {label ?? status}
        </Badge>
    );
}
