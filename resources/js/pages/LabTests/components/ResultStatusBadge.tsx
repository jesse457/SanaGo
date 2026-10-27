import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

/**
 * Maps a lab lifecycle status onto the kit's badge vocabulary, so the same
 * status reads the same everywhere (request queues, result archive, entry form).
 */
const toneByStatus: Record<string, BadgeTone> = {
    Completed: 'positive',
    completed: 'positive',
    validated: 'positive',
    normal: 'positive',
    abnormal: 'warning',
    critical: 'critical',
    In_Progress: 'accent',
    'In Progress': 'accent',
    in_progress: 'accent',
    Pending: 'warning',
    pending: 'warning',
    Cancelled: 'critical',
    cancelled: 'critical',
};

export function resultStatusTone(status: string): BadgeTone {
    return toneByStatus[status] ?? 'neutral';
}

interface ResultStatusBadgeProps {
    status: string;
    /** Render the raw status instead of the underscore-free label. */
    label?: string;
    dot?: boolean;
}

export function ResultStatusBadge({
    status,
    label,
    dot = true,
}: ResultStatusBadgeProps) {
    return (
        <Badge tone={resultStatusTone(status)} dot={dot}>
            {label ?? status.replace(/_/g, ' ')}
        </Badge>
    );
}
