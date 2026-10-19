import { StarIcon } from '@heroicons/react/24/solid';
import { cn } from '@/lib/utils';

export interface RatingScaleProps {
    value: number;
    onChange: (value: number) => void;
    max?: number;
    labels?: string[];
    className?: string;
    disabled?: boolean;
}

const DEFAULT_LABELS = ['Very poor', 'Poor', 'Acceptable', 'Good', 'Excellent'];

/**
 * RatingScale — the one rating control in the app. Real buttons with labels so
 * keyboard users and screen readers get the same information as the stars.
 */
export function RatingScale({
    value,
    onChange,
    max = 5,
    labels = DEFAULT_LABELS,
    className,
    disabled = false,
}: RatingScaleProps) {
    return (
        <div className={cn('flex flex-col gap-2', className)}>
            <div
                role="radiogroup"
                aria-label="Rating"
                className="flex items-center gap-1.5"
            >
                {Array.from({ length: max }).map((_, index) => {
                    const score = index + 1;
                    const active = score <= value;
                    const label = labels[index] ?? `Rating ${score}`;

                    return (
                        <button
                            key={score}
                            type="button"
                            role="radio"
                            aria-checked={value === score}
                            aria-label={`${score} of ${max} — ${label}`}
                            disabled={disabled}
                            onClick={() => onChange(score)}
                            className={cn(
                                'group flex h-9 w-9 items-center justify-center rounded-md border transition-[background-color,border-color,transform] duration-200 ease-premium active:scale-95',
                                'focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                                active
                                    ? 'border-warning/40 bg-warning-soft text-warning-ink'
                                    : 'border-line bg-surface text-ink-subtle hover:border-line-strong hover:text-ink-muted',
                                disabled && 'cursor-not-allowed opacity-50',
                            )}
                        >
                            <StarIcon
                                className={cn(
                                    'h-4 w-4 transition-transform duration-200 ease-spring',
                                    active
                                        ? 'scale-105'
                                        : 'opacity-60 group-hover:scale-105',
                                )}
                            />
                        </button>
                    );
                })}
            </div>
            <p className="text-[12px] text-ink-subtle">
                {value > 0
                    ? `${value} of ${max} — ${labels[value - 1] ?? ''}`
                    : 'Select a rating'}
            </p>
        </div>
    );
}
