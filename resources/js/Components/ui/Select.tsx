import type { SelectHTMLAttributes } from 'react';
import { controlBase, controlState } from '@/Components/ui/Input';
import { cn } from '@/lib/utils';

export interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    invalid?: boolean;
    placeholder?: string;
}

const chevron =
    "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238b8c85' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E\")";

export function Select({
    invalid = false,
    placeholder,
    className,
    children,
    ...props
}: SelectProps) {
    return (
        <div className="relative">
            <select
                className={cn(
                    controlBase,
                    'h-10 cursor-pointer appearance-none pr-9',
                    invalid ? controlState.invalid : controlState.default,
                    className,
                )}
                style={{
                    backgroundImage: chevron,
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: 'right 0.65rem center',
                }}
                aria-invalid={invalid || undefined}
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
