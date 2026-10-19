import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

/**
 * Triage priority vocabulary for the emergency screens. Priority is carried by
 * the badge tone only — never by a red section background, so a whole ward view
 * stays readable while the critical case still reads as critical.
 */
const triageTone: Record<string, BadgeTone> = {
    emergency: 'critical',
    urgent: 'warning',
    non_urgent: 'positive',
    normal: 'neutral',
};

const statusTone: Record<string, BadgeTone> = {
    triage: 'warning',
    active: 'critical',
    stabilized: 'positive',
    discharged: 'neutral',
};

export function triageToneFor(level: string): BadgeTone {
    return triageTone[level?.toLowerCase()] ?? 'neutral';
}

export function caseStatusTone(status: string): BadgeTone {
    return statusTone[status?.toLowerCase()] ?? 'neutral';
}

/** Triage priority marker (emergency / urgent / normal). */
export function TriageBadge({
    level,
    label,
}: {
    level: string;
    label?: string;
}) {
    return (
        <Badge tone={triageToneFor(level)} dot>
            {label ?? level.replace(/_/g, ' ')}
        </Badge>
    );
}

/** Case lifecycle marker (triage / active / stabilized / discharged). */
export function CaseStatusBadge({
    status,
    label,
}: {
    status: string;
    label?: string;
}) {
    return (
        <Badge tone={caseStatusTone(status)} dot>
            {label ?? status.replace(/_/g, ' ')}
        </Badge>
    );
}
