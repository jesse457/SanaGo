import { cn } from '@/lib/utils';

type BadgeColor =
    | 'gray'
    | 'blue'
    | 'emerald'
    | 'amber'
    | 'red'
    | 'rose'
    | 'purple';

interface StatusBadgeProps {
    label: string;
    color?: BadgeColor;
    className?: string;
}

const colorMap: Record<BadgeColor, string> = {
    gray: 'bg-surface-inset text-ink-muted',
    blue: 'bg-accent-soft text-accent-ink',
    emerald: 'bg-positive-soft text-positive-ink',
    amber: 'bg-warning-soft text-warning-ink',
    red: 'bg-critical-soft text-critical-ink',
    rose: 'bg-critical-soft text-critical-ink ',
    purple: 'bg-accent-soft text-accent-ink ',
};

/**
 * StatusBadge — legacy status marker kept for existing pages.
 *
 * New code should use `<Badge tone="positive | warning | critical | accent |
 * neutral" />` from `@/Components/ui`, which supports soft/outline/solid and a
 * dot indicator.
 */
export default function StatusBadge({
    label,
    color = 'gray',
    className = '',
}: StatusBadgeProps) {
    return (
        <span
            className={cn(
                'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium tracking-[0.1em] whitespace-nowrap uppercase',
                colorMap[color],
                className,
            )}
        >
            {label}
        </span>
    );
}

export type { BadgeColor as StatusBadgeColor };
