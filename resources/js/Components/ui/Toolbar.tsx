import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export function Toolbar({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'flex flex-wrap items-center gap-2 rounded-xl border border-line bg-surface px-3 py-2.5',
                className,
            )}
        >
            {children}
        </div>
    );
}

export function ToolbarGroup({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <div className={cn('flex flex-wrap items-center gap-2', className)}>
            {children}
        </div>
    );
}

export function ToolbarSpacer() {
    return <div className="hidden flex-1 sm:block" />;
}

export function ToolbarDivider() {
    return (
        <span
            aria-hidden="true"
            className="hidden h-6 w-px shrink-0 bg-line sm:block"
        />
    );
}

export interface ToolbarSearchProps {
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    className?: string;
    busy?: boolean;
    name?: string;
    /** Clears the field; shown only when there is a value. */
    clearable?: boolean;
}

/**
 * ToolbarSearch — the debounced-looking search control used above every index
 * table. Icon, focus ring and clear affordance are part of the component.
 */
export function ToolbarSearch({
    value,
    onChange,
    placeholder = 'Search…',
    className,
    busy = false,
    name = 'search',
    clearable = true,
}: ToolbarSearchProps) {
    return (
        <div className={cn('relative min-w-0 flex-1 sm:max-w-xs', className)}>
            <MagnifyingGlassIcon
                className={cn(
                    'pointer-events-none absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2 transition-colors duration-200',
                    busy ? 'text-accent' : 'text-ink-subtle',
                )}
            />
            <input
                type="search"
                name={name}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                className="h-9 w-full rounded-md border border-line bg-surface-muted/60 pr-8 pl-8 text-[13px] text-ink transition-[border-color,box-shadow,background-color] duration-200 ease-premium placeholder:text-ink-subtle hover:border-line-strong focus:border-accent focus:bg-surface focus:ring-2 focus:ring-accent/25 focus:outline-none"
            />
            {busy && (
                <span className="absolute top-1/2 right-2.5 h-3 w-3 -translate-y-1/2 animate-spin rounded-full border-[1.5px] border-accent/30 border-t-accent" />
            )}
            {!busy && clearable && value !== '' && (
                <button
                    type="button"
                    onClick={() => onChange('')}
                    aria-label="Clear search"
                    className="absolute top-1/2 right-1.5 -translate-y-1/2 rounded p-1 text-ink-subtle transition-colors duration-150 hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                >
                    <XMarkIcon className="h-3.5 w-3.5" />
                </button>
            )}
        </div>
    );
}
