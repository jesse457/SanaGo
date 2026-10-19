import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface NavLinkProps {
    href: string;
    /** Receives the resolved active state so every shell marks it the same way. */
    children: (state: { isActive: boolean }) => ReactNode;
    /** Provide the router `Link` for this shell. */
    linkComponent: (props: {
        href: string;
        className: string;
        onClick?: () => void;
        children: ReactNode;
    }) => ReactNode;
    isActive: boolean;
    /** `sidebar` is the full-width admin row, `inline` is a marketing nav item. */
    variant?: 'sidebar' | 'inline';
    onNavigate?: () => void;
    className?: string;
}

const base =
    'group flex items-center gap-2.5 rounded-md px-2.5 py-2 transition-[background-color,border-color,color] duration-200 ease-premium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40';

const variants = {
    sidebar: {
        active: 'bg-accent-soft font-semibold text-accent-ink',
        idle: 'text-ink-muted hover:bg-surface-hover hover:text-ink',
    },
    inline: {
        active: 'bg-accent-soft font-semibold text-accent-ink',
        idle: 'text-ink-muted hover:bg-surface-hover hover:text-ink',
    },
} as const;

/**
 * NavLink — one navigation row, shared by the admin and marketing shells.
 * The active state is `bg-accent-soft / text-accent-ink` everywhere, so a
 * route never looks "current" in two different ways.
 */
export function NavLink({
    href,
    children,
    linkComponent: LinkComponent,
    isActive,
    variant = 'sidebar',
    onNavigate,
    className,
}: NavLinkProps) {
    const tones = variants[variant];

    return (
        <>
            {LinkComponent({
                href,
                className: cn(
                    base,
                    'text-[13px] font-medium',
                    isActive ? tones.active : tones.idle,
                    className,
                ),
                onClick: onNavigate,
                children: children({ isActive }),
            })}
        </>
    );
}

export interface NavRailButtonProps {
    onClick: () => void;
    isActive?: boolean;
    className?: string;
    children: ReactNode;
}

/** NavRailButton — the collapsible group toggle that pairs with `NavLink`. */
export function NavRailButton({
    onClick,
    isActive = false,
    className,
    children,
}: NavRailButtonProps) {
    return (
        <button
            type="button"
            onClick={onClick}
            className={cn(
                base,
                'w-full text-left text-[12.5px] font-medium tracking-tight',
                isActive
                    ? 'text-ink'
                    : 'text-ink-muted hover:bg-surface-hover hover:text-ink',
                className,
            )}
        >
            {children}
        </button>
    );
}
