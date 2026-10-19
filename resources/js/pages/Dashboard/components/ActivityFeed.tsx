import {
    BoltIcon,
    CheckCircleIcon,
    ClockIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import type { ComponentType, SVGProps } from 'react';
import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface DashboardActivity {
    id?: number | string;
    event?: string | null;
    description?: string;
    causer_name?: string;
    created_at?: string;
}

const EVENT_TONE: Record<string, BadgeTone> = {
    created: 'positive',
    updated: 'accent',
    deleted: 'critical',
};

const EVENT_ICON: Record<string, ComponentType<SVGProps<SVGSVGElement>>> = {
    created: CheckCircleIcon,
    updated: BoltIcon,
    deleted: XCircleIcon,
    default: ClockIcon,
};

/**
 * ActivityFeed — hairline timeline of recent audit events. Each entry carries a
 * badge type marker instead of a coloured block.
 */
export function ActivityFeed({ items }: { items: DashboardActivity[] }) {
    return (
        <ol className="divide-y divide-line">
            {items.map((activity, index) => {
                const event = (activity.event ?? 'updated').toLowerCase();
                const tone = EVENT_TONE[event] ?? 'accent';
                const Icon = EVENT_ICON[event] ?? EVENT_ICON.default;

                return (
                    <li
                        key={activity.id ?? index}
                        className="flex items-start gap-3 py-3"
                    >
                        <span
                            className={cn(
                                'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md',
                                tone === 'positive' &&
                                    'bg-positive-soft text-positive-ink',
                                tone === 'accent' &&
                                    'bg-accent-soft text-accent-ink',
                                tone === 'critical' &&
                                    'bg-critical-soft text-critical-ink',
                            )}
                        >
                            <Icon className="h-3.5 w-3.5" />
                        </span>
                        <div className="min-w-0 flex-1">
                            <p className="text-[12.5px] leading-relaxed text-ink-muted">
                                {activity.description}
                            </p>
                            <div className="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                                <Badge tone={tone} variant="outline">
                                    {event}
                                </Badge>
                                <span className="text-[10.5px] text-ink-subtle tabular-nums">
                                    {activity.causer_name} ·{' '}
                                    {activity.created_at}
                                </span>
                            </div>
                        </div>
                    </li>
                );
            })}
        </ol>
    );
}
