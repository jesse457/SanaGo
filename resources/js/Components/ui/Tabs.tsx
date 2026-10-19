import { motion } from 'framer-motion';
import type { ComponentType, ReactNode, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export interface TabItem {
    value: string;
    label: ReactNode;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    count?: number;
}

export interface TabsProps {
    items: TabItem[];
    value: string;
    onChange: (value: string) => void;
    className?: string;
    /** `underline` for page-level, `pill` for in-card filters. */
    variant?: 'underline' | 'pill';
}

/**
 * Tabs — controlled segmented navigation. The active marker animates with a
 * shared layout id so the change reads as one continuous object.
 */
export function Tabs({
    items,
    value,
    onChange,
    className,
    variant = 'underline',
}: TabsProps) {
    const isPill = variant === 'pill';

    return (
        <div
            role="tablist"
            aria-orientation="horizontal"
            className={cn(
                isPill
                    ? 'inline-flex items-center gap-0.5 rounded-lg border border-line bg-surface-muted p-1'
                    : 'flex items-center gap-5 border-b border-line',
                className,
            )}
        >
            {items.map((item) => {
                const active = item.value === value;
                const Icon = item.icon;

                return (
                    <button
                        key={item.value}
                        role="tab"
                        type="button"
                        aria-selected={active}
                        onClick={() => onChange(item.value)}
                        className={cn(
                            'relative flex items-center gap-1.5 text-[13px] font-medium whitespace-nowrap transition-colors duration-200 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                            isPill
                                ? 'rounded-md px-2.5 py-1.5'
                                : '-mb-px px-0.5 pt-1 pb-2.5',
                            active
                                ? 'text-ink'
                                : 'text-ink-muted hover:text-ink',
                        )}
                    >
                        {Icon && <Icon className="h-4 w-4" />}
                        {item.label}
                        {typeof item.count === 'number' && (
                            <span
                                className={cn(
                                    'rounded-full px-1.5 text-[10px] font-semibold tabular-nums',
                                    active
                                        ? 'bg-accent-soft text-accent-ink'
                                        : 'bg-surface-inset text-ink-subtle',
                                )}
                            >
                                {item.count}
                            </span>
                        )}
                        {active && !isPill && (
                            <motion.span
                                layoutId="sg-tab-underline"
                                className="absolute inset-x-0 -bottom-px h-[1.5px] rounded-full bg-ink"
                                transition={{
                                    duration: 0.28,
                                    ease: [0.16, 1, 0.3, 1],
                                }}
                            />
                        )}
                        {active && isPill && (
                            <motion.span
                                layoutId="sg-tab-pill"
                                className="absolute inset-0 -z-10 rounded-md bg-surface shadow-surface"
                                transition={{
                                    duration: 0.28,
                                    ease: [0.16, 1, 0.3, 1],
                                }}
                            />
                        )}
                    </button>
                );
            })}
        </div>
    );
}
