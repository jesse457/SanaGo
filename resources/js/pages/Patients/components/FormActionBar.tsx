import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

interface FormActionBarProps {
    /** Secondary actions (cancel, back). Left aligned on wide screens. */
    secondary?: ReactNode;
    /** Primary action(s). Right aligned. */
    children: ReactNode;
    className?: string;
}

/**
 * FormActionBar — the sticky footer every patient form closes with: hairline
 * top edge, translucent chrome so the form scrolls underneath, one primary
 * action on the right. Shared by registration and admission intake.
 */
export function FormActionBar({
    secondary,
    children,
    className,
}: FormActionBarProps) {
    return (
        <div
            className={cn(
                'chrome-blur sticky bottom-0 z-20 -mx-5 mt-2 border-t border-line px-5 py-3.5 sm:-mx-6 sm:px-6',
                className,
            )}
        >
            <div className="flex flex-col-reverse items-stretch gap-2 sm:flex-row sm:items-center sm:justify-end">
                {secondary}
                <div className="flex flex-col gap-2 sm:ml-auto sm:flex-row sm:items-center">
                    {children}
                </div>
            </div>
        </div>
    );
}
