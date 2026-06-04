import type { ComponentType, SVGProps } from 'react';

interface DetailItemProps {
    label: string;
    value: string | number | null | undefined;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    className?: string;
}

export default function DetailItem({
    label,
    value,
    icon: Icon,
    className = '',
}: DetailItemProps) {
    return (
        <div
            className={`rounded-xl bg-gray-50 p-4 dark:bg-neutral-700/50 ${className}`}
        >
            <div className="flex items-center gap-2">
                {Icon && (
                    <Icon className="h-4 w-4 text-gray-400 dark:text-neutral-500" />
                )}
                <span className="text-[10px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                    {label}
                </span>
            </div>
            <p
                className={`mt-1 text-sm font-bold text-gray-900 dark:text-neutral-100 ${Icon ? 'ml-6' : ''}`}
            >
                {value ?? '—'}
            </p>
        </div>
    );
}
