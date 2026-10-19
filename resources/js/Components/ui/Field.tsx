import type { FieldsetHTMLAttributes, HTMLAttributes, ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface FieldProps extends Omit<
    HTMLAttributes<HTMLDivElement>,
    'children'
> {
    label?: ReactNode;
    htmlFor?: string;
    required?: boolean;
    optional?: boolean;
    error?: string | null;
    hint?: ReactNode;
    children: ReactNode;
    /** Rendered in the same row as the label, right aligned (counters, links). */
    labelAction?: ReactNode;
}

/**
 * Field — the label / control / message triad every form needs. Keeps spacing,
 * tracking and error tone identical across the app.
 */
export function Field({
    label,
    htmlFor,
    required = false,
    optional = false,
    error,
    hint,
    children,
    labelAction,
    className,
    ...props
}: FieldProps) {
    return (
        <div
            className={cn('flex min-w-0 flex-col gap-1.5', className)}
            {...props}
        >
            {(label || labelAction) && (
                <div className="flex items-baseline justify-between gap-2">
                    {label && (
                        <label
                            htmlFor={htmlFor}
                            className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase"
                        >
                            {label}
                            {required && (
                                <span
                                    aria-hidden="true"
                                    className="ml-1 text-critical-ink"
                                >
                                    *
                                </span>
                            )}
                            {optional && !required && (
                                <span className="ml-1.5 text-[10px] font-medium tracking-normal text-ink-subtle/80 normal-case">
                                    optional
                                </span>
                            )}
                        </label>
                    )}
                    {labelAction}
                </div>
            )}
            {children}
            {error ? (
                <p className="animate-fade-in text-[12px] leading-snug font-medium text-critical-ink">
                    {error}
                </p>
            ) : (
                hint && (
                    <p className="text-[12px] leading-snug text-ink-subtle">
                        {hint}
                    </p>
                )
            )}
        </div>
    );
}

export interface FieldSetProps extends Omit<
    FieldsetHTMLAttributes<HTMLFieldSetElement>,
    'children'
> {
    title?: string;
    description?: string;
    children: ReactNode;
    /** Grid template for the fields inside. */
    columns?: 1 | 2 | 3 | 4;
}

const columnClasses: Record<1 | 2 | 3 | 4, string> = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 sm:grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
};

/**
 * FieldSet — grouped form section with an optional heading and a responsive
 * field grid. Replaces ad-hoc `grid grid-cols-2 gap-4` blocks.
 */
export function FieldSet({
    title,
    description,
    children,
    columns = 2,
    className,
    ...props
}: FieldSetProps) {
    return (
        <fieldset className={cn('min-w-0', className)} {...props}>
            {(title || description) && (
                <div className="mb-3.5">
                    {title && (
                        <legend className="text-[13px] font-semibold tracking-tight text-ink">
                            {title}
                        </legend>
                    )}
                    {description && (
                        <p className="mt-0.5 text-[12px] text-ink-subtle">
                            {description}
                        </p>
                    )}
                </div>
            )}
            <div className={cn('grid gap-x-4 gap-y-4', columnClasses[columns])}>
                {children}
            </div>
        </fieldset>
    );
}
