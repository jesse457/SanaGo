import {
    Dialog as HeadlessDialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/react';
import { XMarkIcon } from '@heroicons/react/24/outline';
import { AnimatePresence, motion } from 'framer-motion';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export type DialogSize = 'sm' | 'md' | 'lg' | 'xl' | '2xl';

export interface DialogProps {
    open: boolean;
    onClose: () => void;
    title?: ReactNode;
    description?: ReactNode;
    children: ReactNode;
    footer?: ReactNode;
    size?: DialogSize;
    /** Sheet slides from the bottom on mobile, centres on desktop. */
    variant?: 'center' | 'sheet';
    className?: string;
    bodyClassName?: string;
    hideClose?: boolean;
}

const sizeClasses: Record<DialogSize, string> = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-2xl',
    '2xl': 'sm:max-w-4xl',
};

const ease = [0.16, 1, 0.3, 1] as const;

/**
 * Dialog — the app's modal primitive. Hairline panel, diffused float shadow,
 * spring-ish entry, scrollable body with sticky header and footer actions.
 */
export function Dialog({
    open,
    onClose,
    title,
    description,
    children,
    footer,
    size = 'lg',
    variant = 'center',
    className,
    bodyClassName,
    hideClose = false,
}: DialogProps) {
    return (
        <AnimatePresence>
            {open && (
                <HeadlessDialog
                    static
                    open={open}
                    onClose={onClose}
                    className="relative z-50"
                >
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.22, ease }}
                        className="fixed inset-0 bg-ink/25 backdrop-blur-[3px]"
                    />

                    <div className="fixed inset-0 overflow-y-auto">
                        <div
                            className={cn(
                                'flex min-h-full justify-center p-3 sm:p-6',
                                variant === 'sheet'
                                    ? 'items-end sm:items-center'
                                    : 'items-center',
                            )}
                        >
                            <motion.div
                                initial={{ opacity: 0, y: 18, scale: 0.98 }}
                                animate={{ opacity: 1, y: 0, scale: 1 }}
                                exit={{ opacity: 0, y: 12, scale: 0.985 }}
                                transition={{ duration: 0.3, ease }}
                                className={cn('w-full', sizeClasses[size])}
                            >
                                <DialogPanel
                                    className={cn(
                                        'flex max-h-[calc(100dvh-1.5rem)] w-full flex-col overflow-hidden rounded-2xl border border-line bg-surface shadow-float sm:max-h-[calc(100dvh-3rem)]',
                                        className,
                                    )}
                                >
                                    {(title || !hideClose) && (
                                        <div className="flex items-start justify-between gap-4 border-b border-line px-5 py-4 sm:px-6">
                                            <div className="min-w-0">
                                                {title && (
                                                    <DialogTitle className="text-[15px] leading-tight font-semibold tracking-tight text-ink">
                                                        {title}
                                                    </DialogTitle>
                                                )}
                                                {description && (
                                                    <p className="mt-1 text-[12.5px] leading-relaxed text-ink-muted">
                                                        {description}
                                                    </p>
                                                )}
                                            </div>
                                            {!hideClose && (
                                                <button
                                                    type="button"
                                                    onClick={onClose}
                                                    aria-label="Close dialog"
                                                    className="-mt-1 -mr-1 shrink-0 rounded-md p-1.5 text-ink-subtle transition-colors duration-150 hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                                >
                                                    <XMarkIcon className="h-4.5 w-4.5" />
                                                </button>
                                            )}
                                        </div>
                                    )}

                                    <div
                                        className={cn(
                                            'min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6',
                                            bodyClassName,
                                        )}
                                    >
                                        {children}
                                    </div>

                                    {footer && (
                                        <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line bg-surface-muted/60 px-5 py-3.5 sm:px-6">
                                            {footer}
                                        </div>
                                    )}
                                </DialogPanel>
                            </motion.div>
                        </div>
                    </div>
                </HeadlessDialog>
            )}
        </AnimatePresence>
    );
}
