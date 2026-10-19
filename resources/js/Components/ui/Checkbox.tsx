import { useId } from 'react';
import type { InputHTMLAttributes, ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface CheckboxProps extends Omit<
    InputHTMLAttributes<HTMLInputElement>,
    'type'
> {
    label?: ReactNode;
    description?: ReactNode;
}

export function Checkbox({
    label,
    description,
    className,
    id,
    ...props
}: CheckboxProps) {
    const autoId = useId();
    const inputId = id ?? autoId;

    const control = (
        <input
            id={inputId}
            type="checkbox"
            className={cn(
                'mt-0.5 h-4 w-4 shrink-0 cursor-pointer rounded-xs border border-line-strong bg-surface accent-accent',
                'transition-colors duration-150 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-1 focus-visible:ring-offset-surface focus-visible:outline-none',
                'disabled:cursor-not-allowed disabled:opacity-50',
                className,
            )}
            {...props}
        />
    );

    if (!label && !description) {
        return control;
    }

    return (
        <label
            htmlFor={inputId}
            className="group flex cursor-pointer items-start gap-2.5"
        >
            {control}
            <span className="min-w-0">
                {label && (
                    <span className="block text-sm font-medium text-ink transition-colors group-hover:text-ink">
                        {label}
                    </span>
                )}
                {description && (
                    <span className="mt-0.5 block text-[12px] leading-snug text-ink-subtle">
                        {description}
                    </span>
                )}
            </span>
        </label>
    );
}

export interface SwitchProps extends Omit<
    InputHTMLAttributes<HTMLInputElement>,
    'type' | 'size'
> {
    label?: ReactNode;
    description?: ReactNode;
}

export function Switch({
    label,
    description,
    className,
    id,
    checked,
    ...props
}: SwitchProps) {
    const autoId = useId();
    const inputId = id ?? autoId;

    const control = (
        <span
            className={cn(
                'relative inline-flex h-[22px] w-[38px] shrink-0 items-center rounded-full border transition-colors duration-200 ease-premium',
                checked
                    ? 'border-accent bg-accent'
                    : 'border-line-strong bg-surface-inset',
                className,
            )}
        >
            <input
                id={inputId}
                type="checkbox"
                role="switch"
                checked={checked}
                className="peer sr-only"
                {...props}
            />
            <span
                aria-hidden="true"
                className={cn(
                    'pointer-events-none absolute left-0.5 h-[16px] w-[16px] rounded-full bg-white shadow-[0_1px_2px_rgba(0,0,0,0.18)] transition-transform duration-200 ease-spring',
                    checked && 'translate-x-[16px]',
                )}
            />
            <span className="absolute inset-0 rounded-full peer-focus-visible:ring-2 peer-focus-visible:ring-accent/40 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-offset-surface" />
        </span>
    );

    if (!label && !description) {
        return <label htmlFor={inputId}>{control}</label>;
    }

    return (
        <label
            htmlFor={inputId}
            className="group flex cursor-pointer items-start gap-3"
        >
            {control}
            <span className="min-w-0">
                {label && (
                    <span className="block text-sm font-medium text-ink">
                        {label}
                    </span>
                )}
                {description && (
                    <span className="mt-0.5 block text-[12px] leading-snug text-ink-subtle">
                        {description}
                    </span>
                )}
            </span>
        </label>
    );
}
