import { CheckIcon } from '@heroicons/react/24/outline';
import { Badge, ProgressBar } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface IntakeStep {
    label: string;
    description?: string;
}

interface StepProgressProps {
    steps: IntakeStep[];
    current: number;
    columns?: 2 | 3;
}

/**
 * StepProgress — the numbered progress rail above a multi-step intake form.
 * Steps already visited become checkmarks, the current step is ink-solid, and
 * the bar underneath reports how far through the flow the user is.
 */
export function StepProgress({
    steps,
    current,
    columns = 3,
}: StepProgressProps) {
    const percent = ((current + 1) / steps.length) * 100;
    const active = steps[current]?.label ?? '';

    return (
        <div className="space-y-3.5">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <span className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {active}
                </span>
                <Badge tone="muted" variant="soft">
                    {current + 1} / {steps.length}
                </Badge>
            </div>

            <ProgressBar
                value={percent}
                size="sm"
                tone="accent"
                label={active}
            />

            <ol
                className={cn(
                    'grid gap-2',
                    columns === 2
                        ? 'grid-cols-1 sm:grid-cols-2'
                        : 'grid-cols-1 sm:grid-cols-3',
                )}
            >
                {steps.map((step, index) => {
                    const done = index < current;
                    const isCurrent = index === current;

                    return (
                        <li
                            key={step.label}
                            aria-current={isCurrent ? 'step' : undefined}
                            className={cn(
                                'flex min-w-0 items-center gap-2.5 rounded-md border px-3 py-2 transition-colors duration-200 ease-premium',
                                isCurrent
                                    ? 'border-accent/40 bg-accent-soft'
                                    : 'border-line bg-surface',
                            )}
                        >
                            <span
                                className={cn(
                                    'flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10.5px] font-semibold tabular-nums',
                                    isCurrent
                                        ? 'bg-accent text-white'
                                        : done
                                          ? 'bg-positive-soft text-positive-ink'
                                          : 'bg-surface-inset text-ink-subtle',
                                )}
                            >
                                {done ? (
                                    <CheckIcon className="h-3 w-3" />
                                ) : (
                                    index + 1
                                )}
                            </span>
                            <span className="min-w-0">
                                <span
                                    className={cn(
                                        'block truncate text-[12.5px] font-medium',
                                        isCurrent
                                            ? 'text-ink'
                                            : done
                                              ? 'text-ink-muted'
                                              : 'text-ink-subtle',
                                    )}
                                >
                                    {step.label}
                                </span>
                            </span>
                        </li>
                    );
                })}
            </ol>
        </div>
    );
}
