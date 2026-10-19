import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export type AlertTone = 'info' | 'positive' | 'warning' | 'critical';

export interface AlertProps {
    tone?: AlertTone;
    title?: ReactNode;
    children?: ReactNode;
    action?: ReactNode;
    className?: string;
    /** Shows a close affordance; pair with your own state or `onDismiss`. */
    dismissible?: boolean;
    onDismiss?: () => void;
}

const toneClasses: Record<
    AlertTone,
    { wrapper: string; icon: string; title: string }
> = {
    info: {
        wrapper: 'border-accent/25 bg-accent-soft',
        icon: 'text-accent-ink',
        title: 'text-accent-ink',
    },
    positive: {
        wrapper: 'border-positive/25 bg-positive-soft',
        icon: 'text-positive-ink',
        title: 'text-positive-ink',
    },
    warning: {
        wrapper: 'border-warning/30 bg-warning-soft',
        icon: 'text-warning-ink',
        title: 'text-warning-ink',
    },
    critical: {
        wrapper: 'border-critical/25 bg-critical-soft',
        icon: 'text-critical-ink',
        title: 'text-critical-ink',
    },
};

const toneIcons = {
    info: InformationCircleIcon,
    positive: CheckCircleIcon,
    warning: ExclamationTriangleIcon,
    critical: XCircleIcon,
};

/**
 * Alert — in-page messaging for validation summaries, permission notices and
 * empty/error states. Never a native alert dialog.
 */
export function Alert({
    tone = 'info',
    title,
    children,
    action,
    className,
    dismissible = false,
    onDismiss,
}: AlertProps) {
    const Icon = toneIcons[tone];
    const t = toneClasses[tone];

    return (
        <div
            role={tone === 'critical' ? 'alert' : 'status'}
            className={cn(
                'animate-fade-in flex items-start gap-3 rounded-lg border px-3.5 py-3',
                t.wrapper,
                className,
            )}
        >
            <Icon className={cn('mt-0.5 h-[18px] w-[18px] shrink-0', t.icon)} />
            <div className="min-w-0 flex-1">
                {title && (
                    <p className={cn('text-[13px] font-semibold', t.title)}>
                        {title}
                    </p>
                )}
                {children && (
                    <div className="mt-0.5 text-[12.5px] leading-relaxed text-ink-muted">
                        {children}
                    </div>
                )}
            </div>
            {(action || dismissible) && (
                <div className="flex shrink-0 items-center gap-1">
                    {action}
                    {dismissible && (
                        <button
                            type="button"
                            onClick={onDismiss}
                            aria-label="Dismiss message"
                            className={cn(
                                '-mt-0.5 -mr-1 rounded p-1 opacity-60 transition-[opacity,color] duration-150 ease-premium hover:opacity-100 focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                                t.icon,
                            )}
                        >
                            <XMarkIcon className="h-3.5 w-3.5" />
                        </button>
                    )}
                </div>
            )}
        </div>
    );
}
