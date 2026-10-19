import { ChevronDownIcon } from '@heroicons/react/24/outline';
import { Avatar, Badge } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface UserChipProps {
    name?: string | null;
    className?: string;
}

/**
 * UserChip — the header identity chip (avatar + first name + chevron). It is a
 * plain visual block; the surrounding button owns the click behaviour.
 */
export function UserChip({ name, className }: UserChipProps) {
    const firstName = name?.trim().split(/\s+/)[0];

    return (
        <span
            className={cn(
                'flex items-center gap-2 rounded-md border border-line bg-surface py-1 pr-2 pl-1 transition-colors duration-200 ease-premium group-hover:border-line-strong group-hover:bg-surface-hover',
                className,
            )}
        >
            <Avatar name={name} size="xs" />
            <span className="hidden max-w-[9rem] truncate text-[12.5px] font-medium text-ink md:block">
                {firstName}
            </span>
            <ChevronDownIcon className="hidden h-3.5 w-3.5 shrink-0 text-ink-subtle md:block" />
        </span>
    );
}

export interface UserMetaProps {
    name?: string | null;
    role?: string;
    className?: string;
}

/**
 * UserMeta — the header name + role badge used beside an avatar. The role is a
 * `Badge`, never a hand-rolled uppercase span with a palette step.
 */
export function UserMeta({ name, role, className }: UserMetaProps) {
    return (
        <div className={cn('hidden min-w-0 text-right md:block', className)}>
            <p className="max-w-[11rem] truncate text-[12.5px] leading-tight font-medium text-ink">
                {name}
            </p>
            {role && (
                <div className="mt-1 flex justify-end">
                    <Badge tone="accent">{role.replace(/[_-]/g, ' ')}</Badge>
                </div>
            )}
        </div>
    );
}
