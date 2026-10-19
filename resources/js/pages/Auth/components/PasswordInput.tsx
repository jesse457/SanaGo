import { EyeIcon, EyeSlashIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import type { InputHTMLAttributes } from 'react';
import { Input } from '@/Components/ui';
import { cn } from '@/lib/utils';

interface PasswordInputProps extends Omit<
    InputHTMLAttributes<HTMLInputElement>,
    'type'
> {
    invalid?: boolean;
    /** Screen-reader label for the reveal control, e.g. "Show password". */
    showLabel?: string;
    /** Screen-reader label for the conceal control. */
    hideLabel?: string;
}

/**
 * PasswordInput — the password control with the reveal toggle every auth screen
 * needs. Type switches inside the control; the trailing button stays put so the
 * field never reflows.
 */
export function PasswordInput({
    invalid = false,
    className,
    showLabel = 'Show password',
    hideLabel = 'Hide password',
    ...props
}: PasswordInputProps) {
    const [revealed, setRevealed] = useState(false);

    return (
        <div className="relative">
            <Input
                {...props}
                type={revealed ? 'text' : 'password'}
                invalid={invalid}
                className={cn('pr-10', className)}
            />
            <button
                type="button"
                onClick={() => setRevealed((value) => !value)}
                aria-label={revealed ? hideLabel : showLabel}
                aria-pressed={revealed}
                className="absolute top-1/2 right-1.5 -translate-y-1/2 rounded-md p-1.5 text-ink-subtle transition-colors duration-150 ease-premium hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
            >
                {revealed ? (
                    <EyeSlashIcon className="h-4 w-4" />
                ) : (
                    <EyeIcon className="h-4 w-4" />
                )}
            </button>
        </div>
    );
}
