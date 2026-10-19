import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

const INVOICE_STATUS_TONES: Record<string, BadgeTone> = {
    draft: 'muted',
    pending: 'warning',
    paid: 'positive',
    overdue: 'critical',
    cancelled: 'neutral',
};

const CLAIM_STATUS_TONES: Record<string, BadgeTone> = {
    draft: 'muted',
    submitted: 'warning',
    approved: 'positive',
    rejected: 'critical',
    paid: 'positive',
};

const PAYER_TYPE_TONES: Record<string, BadgeTone> = {
    government: 'accent',
    insurance: 'neutral',
    mutual: 'positive',
    employer: 'warning',
};

const SERVICE_CATEGORY_TONES: Record<string, BadgeTone> = {
    consultation: 'accent',
    procedure: 'neutral',
    lab: 'positive',
    radiology: 'warning',
    imaging: 'accent',
    medication: 'critical',
    bed: 'warning',
    nursing: 'positive',
    emergency: 'critical',
    other: 'muted',
};

export interface StatusBadgeProps {
    /** Raw enum value, e.g. `overdue`. Also used as the fallback label. */
    status: string;
    /** Already-localised label; defaults to the raw status. */
    label?: string;
    dot?: boolean;
}

/** Invoice lifecycle marker — paid/overdue/pending map to positive/critical/warning. */
export function InvoiceStatusBadge({ status, label, dot }: StatusBadgeProps) {
    return (
        <Badge tone={INVOICE_STATUS_TONES[status] ?? 'neutral'} dot={dot}>
            {label ?? status}
        </Badge>
    );
}

/** Insurance claim lifecycle marker. */
export function ClaimStatusBadge({ status, label, dot }: StatusBadgeProps) {
    return (
        <Badge tone={CLAIM_STATUS_TONES[status] ?? 'neutral'} dot={dot}>
            {label ?? status}
        </Badge>
    );
}

/** Payer classification marker (government / insurance / mutual / employer). */
export function PayerTypeBadge({ status, label, dot }: StatusBadgeProps) {
    return (
        <Badge tone={PAYER_TYPE_TONES[status] ?? 'neutral'} dot={dot}>
            {label ?? status}
        </Badge>
    );
}

/** Service catalogue category marker. */
export function ServiceCategoryBadge({ status, label, dot }: StatusBadgeProps) {
    return (
        <Badge tone={SERVICE_CATEGORY_TONES[status] ?? 'neutral'} dot={dot}>
            {label ?? status}
        </Badge>
    );
}
