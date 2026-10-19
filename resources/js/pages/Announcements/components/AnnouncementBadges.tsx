import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';

const TONE_BY_TYPE: Record<string, BadgeTone> = {
    info: 'accent',
    warning: 'warning',
    success: 'positive',
};

/**
 * AnnouncementTypeBadge — the one place the info/warning/success taxonomy maps
 * to a status tone, shared by the announcements list and its form.
 */
export function AnnouncementTypeBadge({ type }: { type: string }) {
    return (
        <Badge tone={TONE_BY_TYPE[type] ?? 'neutral'} variant="soft" dot>
            {type}
        </Badge>
    );
}

/**
 * AnnouncementStatusBadge — published vs draft, the only other state an
 * announcement carries.
 */
export function AnnouncementStatusBadge({
    isPublished,
}: {
    isPublished: boolean;
}) {
    return (
        <Badge tone={isPublished ? 'positive' : 'muted'} variant="outline" dot>
            {isPublished ? 'Published' : 'Draft'}
        </Badge>
    );
}
