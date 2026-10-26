import {
    Dialog as HeadlessDialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/react';
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import { AnimatePresence, motion } from 'framer-motion';
import type { ComponentType, ReactElement, ReactNode, SVGProps } from 'react';
import { Button } from '@/Components/ui/Button';
import { cn } from '@/lib/utils';

export type AlertModalTone = 'info' | 'success' | 'warning' | 'critical';

/**
 * An icon may be given either as a component (`DocumentIcon`) or as an element
 * already built by the caller (`<DocumentIcon className="…" />`). Both are
 * normalised to a fixed 24px glyph, so callers never have to know.
 */
export type AlertModalIcon =
    | ComponentType<SVGProps<SVGSVGElement>>
    | ReactElement
    | null;

export interface AlertModalProps {
    open: boolean;
    onClose: () => void;
    tone?: AlertModalTone;
    /** Overrides the tone's default icon. Component or ready-made element. */
    icon?: AlertModalIcon;
    title: ReactNode;
    /** Supporting copy under the bold title. */
    message?: ReactNode;
    /** Label for the primary acknowledgement button. */
    confirmLabel?: string;
    onConfirm?: () => void;
    /**
     * Adding a label turns the alert into a two-button confirmation: the panel
     * gains a quiet Cancel beside the confirm.
     */
    cancelLabel?: string;
    onCancel?: () => void;
    /** Paints the confirm button in the critical tone (destructive actions). */
    danger?: boolean;
    /** An optional takes-you-there action, drawn bold next to the confirm. */
    action?: {
        label: string;
        onClick: () => void;
        icon?: ComponentType<SVGProps<SVGSVGElement>>;
    };
    className?: string;
}

const REACT_ELEMENT = Symbol.for('react.element');

/** Distinguishes a ready-made element from a component reference. */
function isElement(value: AlertModalIcon): value is ReactElement {
    return (
        typeof value === 'object' &&
        value !== null &&
        (value as { $$typeof?: symbol }).$$typeof === REACT_ELEMENT
    );
}

const TONES: Record<
    AlertModalTone,
    {
        well: string;
        icon: string;
        iconWrap: string;
        ring: string;
        fallbackIcon: ComponentType<SVGProps<SVGSVGElement>>;
    }
> = {
    info: {
        well: 'bg-accent-soft',
        icon: 'text-accent-ink',
        iconWrap: 'bg-accent',
        ring: 'bg-accent',
        fallbackIcon: InformationCircleIcon,
    },
    success: {
        well: 'bg-positive-soft',
        icon: 'text-positive-ink',
        iconWrap: 'bg-positive',
        ring: 'bg-positive',
        fallbackIcon: CheckCircleIcon,
    },
    warning: {
        well: 'bg-warning-soft',
        icon: 'text-warning-ink',
        iconWrap: 'bg-warning',
        ring: 'bg-warning',
        fallbackIcon: ExclamationTriangleIcon,
    },
    critical: {
        well: 'bg-critical-soft',
        icon: 'text-critical-ink',
        iconWrap: 'bg-critical',
        ring: 'bg-critical',
        fallbackIcon: XCircleIcon,
    },
};

/* SweetAlert-style emphasis: the panel overshoots slightly on the way in,
 * the icon bounces once, then a slow pulse keeps the eye anchored. Motion
 * stays transform + opacity only. */
const bounce = {
    type: 'spring',
    stiffness: 320,
    damping: 22,
} as const;

/**
 * AlertModal — the big, bold, animated alert (the SweetAlert pattern Livewire
 * apps borrow): dimmed blurred stage, a bouncing tone icon, heavy title and
 * centred actions. Used for events worth stopping the user for — a live
 * notification arriving over Reverb, a destructive confirmation — while the
 * small chrome Dialog stays for ordinary forms.
 *
 * Callers rarely mount it directly: `swal` (see `sweetAlert.tsx`) drives one
 * host-mounted instance imperatively and awaits the user's answer.
 */
export function AlertModal({
    open,
    onClose,
    tone = 'info',
    icon: Icon,
    title,
    message,
    confirmLabel = 'Got it',
    onConfirm,
    cancelLabel,
    onCancel,
    danger = false,
    action,
    className,
}: AlertModalProps) {
    const toneStyle = TONES[tone];
    const hasCancel = Boolean(cancelLabel);

    // A tone icon is a component; a caller's icon may arrive as either shape.
    const iconNode: ReactNode = Icon ? (
        isElement(Icon) ? (
            <span className="relative [&>svg]:h-6 [&>svg]:w-6">{Icon}</span>
        ) : (
            <Icon className="relative h-6 w-6" />
        )
    ) : (
        <toneStyle.fallbackIcon className="relative h-6 w-6" />
    );

    const cancel = () => {
        if (onCancel) {
            onCancel();
        } else {
            onClose();
        }
    };

    return (
        <AnimatePresence>
            {open && (
                <HeadlessDialog
                    static
                    open={open}
                    onClose={onClose}
                    className="relative z-[60]"
                >
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.22 }}
                        className="fixed inset-0 bg-ink/40 backdrop-blur-[6px] backdrop-saturate-[115%]"
                        aria-hidden="true"
                    />

                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex min-h-full items-center justify-center p-3 sm:p-6">
                            <motion.div
                                initial={{ opacity: 0, y: 40, scale: 0.8 }}
                                animate={{ opacity: 1, y: 0, scale: 1 }}
                                exit={{ opacity: 0, y: 24, scale: 0.9 }}
                                transition={bounce}
                                className="w-full max-w-[26rem] motion-reduce:transition-none"
                            >
                                <DialogPanel
                                    className={cn(
                                        'rounded-xl border border-line bg-surface px-7 pt-8 pb-6 text-center shadow-float',
                                        className,
                                    )}
                                >
                                    <div className="relative mx-auto flex h-16 w-16 items-center justify-center">
                                        <motion.span
                                            aria-hidden="true"
                                            className={cn(
                                                'absolute inset-0 rounded-full',
                                                toneStyle.well,
                                            )}
                                        />
                                        <motion.span
                                            aria-hidden="true"
                                            className={cn(
                                                'absolute inset-0 rounded-full opacity-25',
                                                toneStyle.ring,
                                            )}
                                            animate={{
                                                scale: [1, 1.45],
                                                opacity: [0.25, 0],
                                            }}
                                            transition={{
                                                duration: 1.8,
                                                repeat: Infinity,
                                                ease: 'easeOut',
                                            }}
                                        />
                                        <motion.span
                                            className="relative flex h-12 w-12 items-center justify-center rounded-full text-ink-inverse"
                                            initial={{ scale: 0.5, y: 8 }}
                                            animate={{ scale: 1, y: 0 }}
                                            transition={{
                                                ...bounce,
                                                delay: 0.08,
                                            }}
                                        >
                                            <span
                                                className={cn(
                                                    'absolute inset-0 rounded-full',
                                                    toneStyle.iconWrap,
                                                )}
                                            />
                                            <span
                                                className={cn(
                                                    'relative',
                                                    toneStyle.icon,
                                                )}
                                            >
                                                {iconNode}
                                            </span>
                                        </motion.span>
                                    </div>

                                    <DialogTitle
                                        as="div"
                                        className="mt-4 text-[17px] leading-snug font-bold tracking-tight text-ink"
                                    >
                                        {title}
                                    </DialogTitle>

                                    {message && (
                                        <div className="mt-2 text-[13px] leading-relaxed text-ink-muted">
                                            {message}
                                        </div>
                                    )}

                                    <div className="mt-6 flex items-center justify-center gap-2">
                                        {action && (
                                            <Button
                                                variant="accent"
                                                size="sm"
                                                trailingIcon={action.icon}
                                                onClick={action.onClick}
                                            >
                                                {action.label}
                                            </Button>
                                        )}
                                        {hasCancel && (
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={cancel}
                                            >
                                                {cancelLabel}
                                            </Button>
                                        )}
                                        <Button
                                            variant={
                                                action
                                                    ? 'outline'
                                                    : danger
                                                      ? 'danger'
                                                      : 'accent'
                                            }
                                            size="sm"
                                            onClick={onConfirm ?? onClose}
                                        >
                                            {confirmLabel}
                                        </Button>
                                    </div>
                                </DialogPanel>
                            </motion.div>
                        </div>
                    </div>
                </HeadlessDialog>
            )}
        </AnimatePresence>
    );
}
