import type { ComponentType, InputHTMLAttributes, SVGProps } from 'react';
import { useControlInvalid } from '@/Components/ui/FieldContext';
import { cn } from '@/lib/utils';

/**
 * Shared control geometry so inputs, selects and textareas line up exactly:
 * one height (2.5rem / h-10), one radius, one inset shadow, one focus ring.
 *
 * The inset shadow (`--sg-control-inset`) is what makes a field read as a well
 * in the paper rather than a floating box — it is defined per theme in app.css.
 */
export const controlBase =
    'w-full rounded-md border bg-surface px-3 text-sm text-ink placeholder:text-ink-subtle ' +
    'shadow-[var(--sg-control-inset)] ' +
    'transition-[border-color,box-shadow,background-color] duration-200 ease-premium ' +
    'focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/25 focus:ring-offset-0 ' +
    'disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-ink-subtle disabled:shadow-none ' +
    'read-only:bg-surface-muted';

export const controlState = {
    default: 'border-line hover:border-line-strong',
    invalid:
        'border-critical/70 focus:border-critical focus:ring-critical/25 aria-[invalid=true]:border-critical',
};

export interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    invalid?: boolean;
    /** Decorative icon rendered inside the control. */
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
}

export function Input({
    invalid,
    icon: Icon,
    className,
    ...props
}: InputProps) {
    const isInvalid = useControlInvalid(invalid, props.name, props.id);

    if (!Icon) {
        return (
            <input
                className={cn(
                    controlBase,
                    'h-10',
                    isInvalid ? controlState.invalid : controlState.default,
                    className,
                )}
                aria-invalid={isInvalid || undefined}
                {...props}
            />
        );
    }

    return (
        <div className="relative">
            <Icon className="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
            <input
                className={cn(
                    controlBase,
                    'h-10 pl-9',
                    isInvalid ? controlState.invalid : controlState.default,
                    className,
                )}
                aria-invalid={isInvalid || undefined}
                {...props}
            />
        </div>
    );
}
