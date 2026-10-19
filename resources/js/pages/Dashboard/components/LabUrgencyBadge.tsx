import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

const TONE_BY_LEVEL: Record<string, BadgeTone> = {
    urgent: 'critical',
    stat: 'critical',
    high: 'warning',
    normal: 'accent',
    routine: 'neutral',
};

/**
 * LabUrgencyBadge — the urgency marker used by the lab dashboards. Unknown
 * levels fall back to the neutral accent tone.
 */
export function LabUrgencyBadge({ level }: { level: string }) {
    const { t } = useTranslation();
    const tone = TONE_BY_LEVEL[(level ?? '').toLowerCase()] ?? 'accent';

    return (
        <Badge tone={tone} dot>
            {t(level)}
        </Badge>
    );
}
