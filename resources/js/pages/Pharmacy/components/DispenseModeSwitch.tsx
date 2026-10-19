import { ShoppingBagIcon, UserGroupIcon } from '@heroicons/react/24/outline';
import { cn } from '@/lib/utils';
import type { DispenseViewMode } from './types';

interface DispenseModeSwitchProps {
    value: DispenseViewMode;
    onChange: (mode: DispenseViewMode) => void;
    prescriptionLabel: string;
    walkInLabel: string;
}

const options = [
    {
        mode: 'prescription' as const,
        label: 'prescription',
        icon: UserGroupIcon,
    },
    { mode: 'walkin' as const, label: 'walkin', icon: ShoppingBagIcon },
];

/**
 * DispenseModeSwitch — the segmented control that picks the dispensation flow.
 * Two equal halves on phones, auto width from `sm` up.
 */
export function DispenseModeSwitch({
    value,
    onChange,
    prescriptionLabel,
    walkInLabel,
}: DispenseModeSwitchProps) {
    const labels: Record<DispenseViewMode, string> = {
        prescription: prescriptionLabel,
        walkin: walkInLabel,
    };

    return (
        <div
            role="tablist"
            aria-label={walkInLabel}
            className="flex w-full rounded-lg border border-line bg-surface-inset p-1 sm:w-fit"
        >
            {options.map(({ mode, icon: Icon }) => {
                const active = value === mode;

                return (
                    <button
                        key={mode}
                        type="button"
                        role="tab"
                        aria-selected={active}
                        onClick={() => onChange(mode)}
                        className={cn(
                            'flex flex-1 items-center justify-center gap-2 rounded-md px-5 py-2 text-[13px] font-semibold whitespace-nowrap',
                            'transition-[background-color,color,box-shadow] duration-200 ease-premium sm:flex-none',
                            'focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:ring-offset-1 focus-visible:ring-offset-canvas focus-visible:outline-none',
                            active
                                ? 'bg-surface text-ink shadow-surface'
                                : 'text-ink-subtle hover:text-ink-muted',
                        )}
                    >
                        <Icon className="h-4 w-4" />
                        {labels[mode]}
                    </button>
                );
            })}
        </div>
    );
}
