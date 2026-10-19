import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

const REQUEST_STATUS_TONES: Record<string, BadgeTone> = {
    pending: 'warning',
    in_progress: 'accent',
    completed: 'positive',
    validated: 'accent',
};

interface RadiologyStatusBadgeProps {
    /** Raw enum value, e.g. `in_progress`. */
    status: string;
    /** Already-localised label; defaults to the raw status. */
    label?: string;
    dot?: boolean;
}

/**
 * RadiologyStatusBadge — one lifecycle marker for radiology requests and their
 * results, so the index and the detail screen never disagree on meaning.
 */
export function RadiologyStatusBadge({
    status,
    label,
    dot,
}: RadiologyStatusBadgeProps) {
    return (
        <Badge tone={REQUEST_STATUS_TONES[status] ?? 'neutral'} dot={dot}>
            {label ?? status}
        </Badge>
    );
}
