import { XMarkIcon } from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface DrawerHeaderProps {
    /** Usually a `BrandMark`. */
    children: ReactNode;
    onClose: () => void;
    closeLabel?: string;
    className?: string;
}

/**
 * DrawerHeader — the title/close strip shared by the mobile drawers. The close
 * affordance sits in normal flow so nothing overlaps with arbitrary offsets.
 */
export function DrawerHeader({
    children,
    onClose,
    closeLabel = 'Close navigation',
    className,
}: DrawerHeaderProps) {
    return (
        <div
            className={cn(
                'flex h-14 shrink-0 items-center justify-between gap-3 border-b border-line px-3.5',
                className,
            )}
        >
            {children}
            <button
                type="button"
                onClick={onClose}
                aria-label={closeLabel}
                className="shrink-0 rounded-md p-1.5 text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
            >
                <XMarkIcon className="h-[18px] w-[18px]" />
            </button>
        </div>
    );
}
