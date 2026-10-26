import type { BadgeTone } from '@/Components/ui';

/** Appointment statuses are stored as the lowercase Enum values. */
export type AppointmentStatus =
    | 'scheduled'
    | 'in_consultation'
    | 'pending'
    | 'completed'
    | 'cancelled';

/**
 * One status → one tone. Kept in one place so the index table, the index cards,
 * the calendar chips and the detail dialogs never disagree.
 */
export function statusTone(status: string): BadgeTone {
    switch (status.toLowerCase()) {
        case 'in_consultation':
        case 'pending':
            return 'warning';
        case 'completed':
        case 'confirmed':
            return 'positive';
        case 'canceled':
        case 'cancelled':
        case 'no_show':
            return 'critical';
        case 'scheduled':
            return 'accent';
        default:
            return 'neutral';
    }
}

/** Dot colour for the calendar chips — mirrors `statusTone`. */
export const statusDot: Record<string, string> = {
    warning: 'bg-warning',
    positive: 'bg-positive',
    critical: 'bg-critical',
    accent: 'bg-accent',
    neutral: 'bg-ink-subtle',
    muted: 'bg-ink-subtle',
};

export function statusDotClass(status: string): string {
    return statusDot[statusTone(status)] ?? statusDot.neutral;
}

/** Calendar chips: tinted fill plus a left status bar, Google Calendar style. */
export const statusChip: Record<string, string> = {
    accent: 'border-accent/70 bg-accent-soft text-accent-ink hover:border-accent',
    positive:
        'border-positive/60 bg-positive-soft text-positive-ink hover:border-positive',
    warning:
        'border-warning/60 bg-warning-soft text-warning-ink hover:border-warning',
    critical:
        'border-critical/60 bg-critical-soft text-critical-ink hover:border-critical',
    neutral:
        'border-line-strong bg-surface-inset text-ink-muted hover:border-ink-subtle',
    muted: 'border-line bg-surface-muted text-ink-subtle hover:border-line-strong',
};

export function statusChipClass(status: string): string {
    return statusChip[statusTone(status)] ?? statusChip.neutral;
}
