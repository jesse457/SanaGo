import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface SectionLabelProps {
    children: ReactNode;
    className?: string;
}

/** SectionLabel — the shared eyebrow/label style (footer headings, captions). */
export function SectionLabel({ children, className }: SectionLabelProps) {
    return (
        <p
            className={cn(
                'text-[10.5px] font-semibold tracking-[0.14em] text-ink-subtle uppercase',
                className,
            )}
        >
            {children}
        </p>
    );
}

export interface DrawerBackdropProps {
    open: boolean;
    onClose: () => void;
    className?: string;
}

/** DrawerBackdrop — animated scrim shared by the mobile drawers. */
export function DrawerBackdrop({
    open,
    onClose,
    className,
}: DrawerBackdropProps) {
    return (
        <div
            aria-hidden={!open}
            onClick={onClose}
            className={cn(
                'fixed inset-0 z-40 bg-ink/25 backdrop-blur-[2px] transition-opacity duration-300 ease-premium lg:hidden',
                open ? 'opacity-100' : 'pointer-events-none opacity-0',
                className,
            )}
        />
    );
}
