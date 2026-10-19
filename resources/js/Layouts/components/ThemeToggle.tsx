import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface ThemeToggleProps {
    isDark: boolean;
    onToggle: () => void;
    className?: string;
    label?: string;
}

/**
 * ThemeToggle — the single dark-mode switch. It only flips the `.dark` class on
 * <html>; the icons are swapped through CSS (``) so there is no second
 * theme mechanism and no state duplication beyond the existing `isDark`.
 */
export function ThemeToggle({
    isDark,
    onToggle,
    className,
    label = 'Toggle dark mode',
}: ThemeToggleProps) {
    return (
        <button
            type="button"
            onClick={onToggle}
            role="switch"
            aria-checked={isDark}
            aria-label={label}
            className={cn(
                'relative inline-flex h-[22px] w-[38px] shrink-0 cursor-pointer items-center rounded-full border transition-colors duration-200 ease-premium',
                'focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas focus-visible:outline-none',
                'data-[dark=false]:border-line-strong data-[dark=false]:bg-surface-inset data-[dark=true]:border-accent data-[dark=true]:bg-accent',
                className,
            )}
            data-dark={isDark}
        >
            <span
                className={cn(
                    'pointer-events-none absolute left-0.5 h-4 w-4 rounded-full bg-white shadow-[0_1px_2px_rgba(0,0,0,0.2)] transition-transform duration-200 ease-spring',
                    isDark ? 'translate-x-[16px]' : 'translate-x-0',
                )}
            />
            <span className="pointer-events-none relative flex w-full items-center px-[3px]">
                <SunIcon className="h-2.5 w-2.5 text-warning transition-opacity duration-200 ease-premium dark:opacity-0" />
                <MoonIcon className="ml-auto h-2.5 w-2.5 text-accent-ink opacity-0 transition-opacity duration-200 ease-premium" />
            </span>
        </button>
    );
}

/** Small wrappers keep the icon imports in one place for both shells. */
function SunIcon({ className }: { className?: string }) {
    return (
        <svg
            className={className}
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2Zm0 13a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15Zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm0-1.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9ZM3.05 4.11a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 1 1-1.06 1.06L3.05 5.17a.75.75 0 0 1 0-1.06Zm11.78 11.78a.75.75 0 0 1 1.06 0l1.06 1.06a.75.75 0 1 1-1.06 1.06l-1.06-1.06a.75.75 0 0 1 0-1.06Zm0-11.78a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 1 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0ZM4.11 15.89a.75.75 0 0 1 0 1.06l-1.06 1.06a.75.75 0 0 1-1.06-1.06l1.06-1.06a.75.75 0 0 1 1.06 0ZM2 10a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 2 10Zm13 0a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 15 10Z" />
        </svg>
    );
}

function MoonIcon({ className }: { className?: string }) {
    return (
        <svg
            className={className}
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path d="M17.29 13.02a.75.75 0 0 0-.86-.24 6.5 6.5 0 0 1-8.21-8.2.75.75 0 0 0-.98-.94A7.96 7.96 0 0 0 3 11.09a8 8 0 0 0 12.08 6.75.75.75 0 0 0 .21-1.06 2.6 2.6 0 0 0-1.02.24h.02Z" />
        </svg>
    );
}

export interface ThemeToggleSlotProps {
    isDark: boolean;
    onToggle: () => void;
    /** Text toggle for marketing headers. */
    children?: ReactNode;
}

/** Icon-only variant for marketing chrome (larger tap target). */
export function IconThemeToggle({ isDark, onToggle, label }: ThemeToggleProps) {
    return (
        <button
            type="button"
            onClick={onToggle}
            aria-label={label ?? 'Toggle dark mode'}
            aria-pressed={isDark}
            className="relative inline-flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-md border border-line bg-surface text-ink-muted transition-colors duration-200 ease-premium hover:border-line-strong hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
        >
            <SunIcon
                className={cn(
                    'absolute h-4 w-4 transition-transform duration-300 ease-premium',
                    isDark
                        ? 'translate-y-6 rotate-90 opacity-0'
                        : 'translate-y-0 rotate-0 opacity-100',
                )}
            />
            <MoonIcon
                className={cn(
                    'absolute h-4 w-4 transition-transform duration-300 ease-premium',
                    isDark
                        ? 'translate-y-0 rotate-0 text-accent-ink opacity-100'
                        : '-translate-y-6 -rotate-90 opacity-0',
                )}
            />
        </button>
    );
}
