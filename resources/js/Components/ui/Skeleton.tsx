import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

interface SkeletonProps extends HTMLAttributes<HTMLDivElement> {
    rounded?: 'sm' | 'md' | 'lg' | 'full';
}

/** Skeleton — shaped loading placeholders. Never a bare spinner. */
export function Skeleton({
    className,
    rounded = 'md',
    ...props
}: SkeletonProps) {
    return (
        <div
            aria-hidden="true"
            className={cn(
                'skeleton',
                rounded === 'sm' && 'rounded-sm',
                rounded === 'md' && 'rounded-md',
                rounded === 'lg' && 'rounded-lg',
                rounded === 'full' && 'rounded-full',
                className,
            )}
            {...props}
        />
    );
}

export function SkeletonText({
    lines = 3,
    className,
}: {
    lines?: number;
    className?: string;
}) {
    return (
        <div className={cn('flex flex-col gap-2', className)}>
            {Array.from({ length: lines }).map((_, index) => (
                <Skeleton
                    key={index}
                    className={cn(
                        'h-3',
                        index === lines - 1 ? 'w-2/3' : 'w-full',
                    )}
                />
            ))}
        </div>
    );
}

export function SkeletonRows({
    rows = 5,
    columns = 4,
}: {
    rows?: number;
    columns?: number;
}) {
    return (
        <div className="flex flex-col gap-px overflow-hidden rounded-xl border border-line bg-line">
            <div
                className="grid gap-4 bg-surface-muted px-4 py-3"
                style={gridStyle(columns)}
            >
                {Array.from({ length: columns }).map((_, index) => (
                    <Skeleton
                        key={index}
                        className="h-2.5 w-full"
                        rounded="sm"
                    />
                ))}
            </div>
            {Array.from({ length: rows }).map((_, row) => (
                <div
                    key={row}
                    className="grid gap-4 bg-surface px-4 py-4"
                    style={gridStyle(columns)}
                >
                    {Array.from({ length: columns }).map((_, column) => (
                        <Skeleton
                            key={column}
                            className={cn(
                                'h-3',
                                column === 0 ? 'w-4/5' : 'w-3/5',
                            )}
                            rounded="sm"
                        />
                    ))}
                </div>
            ))}
        </div>
    );
}

function gridStyle(columns: number) {
    return {
        gridTemplateColumns: `repeat(${Math.max(columns, 1)}, minmax(0, 1fr))`,
    };
}

export function SkeletonCards({ count = 4 }: { count?: number }) {
    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {Array.from({ length: count }).map((_, index) => (
                <div
                    key={index}
                    className="rounded-xl border border-line bg-surface p-5"
                >
                    <Skeleton className="h-8 w-8" rounded="lg" />
                    <Skeleton className="mt-4 h-6 w-1/2" />
                    <Skeleton className="mt-2 h-3 w-3/4" />
                </div>
            ))}
        </div>
    );
}
