import type { SelectHTMLAttributes } from 'react';
import { useControlInvalid } from '@/Components/ui/FieldContext';
import { controlBase, controlState } from '@/Components/ui/Input';
import { cn } from '@/lib/utils';

export interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    invalid?: boolean;
    placeholder?: string;
}

/*
 * The chevron is drawn as a data URI so the control keeps a native <select>
 * (real mobile pickers, real keyboard behaviour). Light and dark variants are
 * swapped through `--sg-select-chevron`, which app.css sets per theme.
 */
const chevronLight =
    "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c7077' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E\")";

// Exposed so app.css can define the dark variant once for every <select>.
export const selectChevronDark =
    "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237d838a' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E\")";

export function Select({
    invalid,
    placeholder,
    className,
    children,
    ...props
}: SelectProps) {
    const isInvalid = useControlInvalid(invalid, props.name, props.id);

    return (
        <div className="relative min-w-0">
            <select
                className={cn(
                    controlBase,
                    'h-10 cursor-pointer appearance-none pr-9',
                    isInvalid ? controlState.invalid : controlState.default,
                    className,
                )}
                style={{
                    backgroundImage: `var(--sg-select-chevron, ${chevronLight})`,
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: 'right 0.65rem center',
                }}
                aria-invalid={isInvalid || undefined}
                {...props}
            >
                {placeholder && (
                    <option value="" disabled>
                        {placeholder}
                    </option>
                )}
                {children}
            </select>
        </div>
    );
}
