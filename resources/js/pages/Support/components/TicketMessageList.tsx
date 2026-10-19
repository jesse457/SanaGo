import { LockClosedIcon } from '@heroicons/react/24/outline';
import { Avatar, Badge, EmptyState } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface TicketMessage {
    id: number;
    message: string;
    is_internal: boolean;
    user_name: string;
    created_at: string;
}

/**
 * Relative "3 hours ago" label. Falls back to the absolute date once a thread
 * is older than a week, where relative phrasing stops being useful.
 */
function relativeTime(value: string): string {
    const timestamp = new Date(value).getTime();

    if (Number.isNaN(timestamp)) {
        return value;
    }

    const seconds = Math.round((timestamp - Date.now()) / 1000);
    const units: [Intl.RelativeTimeFormatUnit, number][] = [
        ['year', 60 * 60 * 24 * 365],
        ['month', 60 * 60 * 24 * 30],
        ['week', 60 * 60 * 24 * 7],
        ['day', 60 * 60 * 24],
        ['hour', 60 * 60],
        ['minute', 60],
        ['second', 1],
    ];

    const formatter = new Intl.RelativeTimeFormat(undefined, {
        numeric: 'auto',
    });

    for (const [unit, secondsInUnit] of units) {
        if (Math.abs(seconds) >= secondsInUnit || unit === 'second') {
            return formatter.format(Math.round(seconds / secondsInUnit), unit);
        }
    }

    return formatter.format(seconds, 'second');
}

interface TicketMessageListProps {
    messages: TicketMessage[];
    /** Signed-in author: their own messages read as outbound. */
    currentUserName?: string | null;
    /** Internal notes render with a warning tint instead of the normal bubble. */
    showInternalNotes?: boolean;
    /** Total notes rendered before the list starts scrolling. */
    maxHeightClassName?: string;
    emptyTitle?: string;
}

/**
 * TicketMessageList — the conversation body shared by both ticket detail
 * screens. Hairline-separated bubbles: inbound sits on plain surface, outbound
 * on the accent-soft tint. It is deliberately not a chat bubble layout.
 */
export function TicketMessageList({
    messages,
    currentUserName = null,
    showInternalNotes = false,
    maxHeightClassName = 'max-h-[28rem]',
    emptyTitle = 'No messages yet',
}: TicketMessageListProps) {
    const visible = messages.filter(
        (message) => showInternalNotes || !message.is_internal,
    );

    if (visible.length === 0) {
        return (
            <EmptyState
                title={emptyTitle}
                description="Messages will appear here as the conversation develops."
                panel={false}
                className="py-10"
            />
        );
    }

    return (
        <ul
            className={cn('space-y-3 overflow-y-auto pr-1', maxHeightClassName)}
        >
            {visible.map((message) => {
                const outbound =
                    message.is_internal ||
                    (currentUserName !== null &&
                        message.user_name === currentUserName);

                return (
                    <li key={message.id} className="flex gap-3">
                        <Avatar
                            name={message.user_name}
                            size="sm"
                            className="mt-0.5"
                        />
                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                <span className="text-[13px] font-semibold tracking-tight text-ink">
                                    {message.user_name}
                                </span>
                                <span
                                    className="text-[11px] text-ink-subtle tabular-nums"
                                    title={new Date(
                                        message.created_at,
                                    ).toLocaleString()}
                                >
                                    {relativeTime(message.created_at)}
                                </span>
                                {message.is_internal && (
                                    <Badge tone="warning" dot>
                                        <LockClosedIcon className="h-3 w-3" />
                                        Internal note
                                    </Badge>
                                )}
                            </div>
                            <div
                                className={cn(
                                    'mt-1.5 rounded-lg border px-3.5 py-2.5 text-[13px] leading-relaxed whitespace-pre-line',
                                    message.is_internal
                                        ? 'border-warning/25 bg-warning-soft text-warning-ink'
                                        : outbound
                                          ? 'border-accent/20 bg-accent-soft text-ink'
                                          : 'border-line bg-surface text-ink',
                                )}
                            >
                                {message.message}
                            </div>
                        </div>
                    </li>
                );
            })}
        </ul>
    );
}
