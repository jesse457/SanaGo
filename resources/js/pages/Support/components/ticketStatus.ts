import type { BadgeTone } from '@/Components/ui';

/** Status keys the ticket screens render, keyed by their raw backend value. */
export const TICKET_STATUSES = [
    'open',
    'in_progress',
    'resolved',
    'closed',
] as const;

export type TicketStatus = (typeof TICKET_STATUSES)[number];

/**
 * Status tone map. Colors carry meaning only: open is the accent, in progress
 * warns, resolved is positive, everything closed or unknown stays neutral.
 */
const STATUS_TONES: Record<string, BadgeTone> = {
    open: 'accent',
    in_progress: 'warning',
    resolved: 'positive',
    closed: 'neutral',
};

export function statusTone(status: string): BadgeTone {
    return STATUS_TONES[status] ?? 'neutral';
}

/** Human label for a status: `in_progress` → `In progress`. */
export function statusLabel(status: string): string {
    const words = status.replace(/_/g, ' ');

    return words.charAt(0).toUpperCase() + words.slice(1);
}

const PRIORITY_TONES: Record<string, BadgeTone> = {
    low: 'muted',
    medium: 'neutral',
    high: 'warning',
    urgent: 'critical',
};

export function priorityTone(priority: string): BadgeTone {
    return PRIORITY_TONES[priority] ?? 'neutral';
}
