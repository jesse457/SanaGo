import { motion } from 'framer-motion';
import type { ComponentType, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export interface SegmentedOption<T extends string = string> {
    value: T;
    label?: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    ariaLabel?: string;
}

export interface SegmentedControlProps<T extends string = string> {
    options: SegmentedOption<T>[];
    value: T;
    onChange: (value: T) => void;
    size?: 'sm' | 'md';
    className?: string;
}

/**
 * SegmentedControl — compact mutually exclusive switch (grid/list views,
 * date ranges). Replaces hand-rolled toggle buttons.
 */
export function SegmentedControl<T extends string = string>({
    options,
    value,
    onChange,
    size = 'md',
    className,
}: SegmentedControlProps<T>) {
    return (
        <div
            role="group"
            className={cn(
                'inline-flex items-center gap-0.5 rounded-lg border border-line bg-surface-muted p-0.5',
                className,
            )}
        >
            {options.map((option) => {
                const active = option.value === value;
                const Icon = option.icon;

                return (
                    <button
                        key={option.value}
                        type="button"
                        aria-pressed={active}
                        aria-label={option.ariaLabel ?? option.label}
                        onClick={() => onChange(option.value)}
                        className={cn(
                            'relative flex items-center justify-center gap-1.5 rounded-[6px] font-medium transition-colors duration-200 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                            size === 'sm'
                                ? 'h-7 px-2 text-[12px]'
                                : 'h-8 px-2.5 text-[13px]',
                            active
                                ? 'text-ink'
                                : 'text-ink-subtle hover:text-ink',
                        )}
                    >
                        {active && (
                            <motion.span
                                layoutId="sg-segmented-active"
                                className="absolute inset-0 -z-10 rounded-[6px] bg-surface shadow-surface"
                                transition={{
                                    duration: 0.26,
                                    ease: [0.16, 1, 0.3, 1],
                                }}
                            />
                        )}
                        {Icon && <Icon className="h-4 w-4" />}
                        {option.label}
                    </button>
                );
            })}
        </div>
    );
}
