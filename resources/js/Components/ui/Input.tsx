import type { ComponentType, InputHTMLAttributes, SVGProps } from 'react';
import { cn } from '@/lib/utils';

/** Shared control geometry so inputs, selects and textareas line up exactly. */
export const controlBase =
    'w-full rounded-md border bg-surface px-3 text-sm text-ink placeholder:text-ink-subtle/85 ' +
    'transition-[border-color,box-shadow,background-color] duration-200 ease-premium ' +
    'focus:outline-none focus-visible:outline-none focus:border-accent focus:ring-2 focus:ring-accent/25 ' +
    'disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-ink-subtle';

export const controlState = {
    default: 'border-line hover:border-line-strong',
    invalid: 'border-critical/70 focus:border-critical focus:ring-critical/20',
};

export interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    invalid?: boolean;
    /** Decorative icon rendered inside the control. */
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
}

export function Input({
    invalid = false,
    icon: Icon,
    className,
    ...props
}: InputProps) {
    if (!Icon) {
        return (
            <input
                className={cn(
                    controlBase,
                    'h-10',
                    invalid ? controlState.invalid : controlState.default,
                    className,
                )}
                aria-invalid={invalid || undefined}
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
                    invalid ? controlState.invalid : controlState.default,
                    className,
                )}
                aria-invalid={invalid || undefined}
                {...props}
            />
        </div>
    );
}
