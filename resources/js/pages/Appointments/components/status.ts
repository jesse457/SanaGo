import type { BadgeTone } from '@/Components/ui';

/** Appointment statuses are title-cased on the index/booking screens. */
export type AppointmentStatus =
    | 'Scheduled'
    | 'Waiting'
    | 'In Consultation'
    | 'Completed'
    | 'Canceled';

/** …and snake_cased on the calendar payload. */
export type CalendarAppointmentStatus =
    | 'scheduled'
    | 'confirmed'
    | 'completed'
    | 'cancelled'
    | 'no_show';

/**
 * One status → one tone. Kept in one place so the index table, the index cards,
 * the calendar dots and the calendar detail dialog never disagree.
 */
export function statusTone(status: string): BadgeTone {
    switch (status) {
        case 'Waiting':
        case 'waiting':
        case 'in_consultation':
        case 'In Consultation':
            return 'warning';
        case 'Completed':
        case 'completed':
        case 'confirmed':
            return 'positive';
        case 'Canceled':
        case 'Cancelled':
        case 'cancelled':
        case 'no_show':
            return 'critical';
        case 'Scheduled':
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
};

export function statusDotClass(status: string): string {
    return statusDot[statusTone(status)] ?? statusDot.neutral;
}
