import type { ComponentType, ReactNode, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export interface KeyValueProps {
    label: string;
    value: ReactNode;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    className?: string;
    /** Render as a bordered tile instead of a bare definition pair. */
    tile?: boolean;
    mono?: boolean;
}

/**
 * KeyValue — one label/value pair for detail views. Uses `dl` semantics so
 * screen readers announce the relationship.
 */
export function KeyValue({
    label,
    value,
    icon: Icon,
    className,
    tile = false,
    mono = false,
}: KeyValueProps) {
    return (
        <div
            className={cn(
                'min-w-0',
                tile && 'rounded-lg bg-surface-muted px-3.5 py-3',
                className,
            )}
        >
            <dt className="flex items-center gap-1.5 text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                {Icon && <Icon className="h-3.5 w-3.5" />}
                {label}
            </dt>
            <dd
                className={cn(
                    'mt-1 truncate text-[13px] font-medium text-ink',
                    mono && 'font-mono text-[12.5px] tracking-tight',
                )}
            >
                {value === null || value === undefined || value === ''
                    ? '—'
                    : value}
            </dd>
        </div>
    );
}

export interface KeyValueGridProps {
    children: ReactNode;
    columns?: 1 | 2 | 3 | 4;
    className?: string;
    tile?: boolean;
}

const columnClasses: Record<1 | 2 | 3 | 4, string> = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 sm:grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-2 lg:grid-cols-4',
};

export function KeyValueGrid({
    children,
    columns = 2,
    className,
    tile = true,
}: KeyValueGridProps) {
    return (
        <dl
            className={cn(
                'grid gap-3',
                columnClasses[columns],
                tile && 'grid-flow-row',
                className,
            )}
        >
            {children}
        </dl>
    );
}
