import type { ComponentType, SVGProps } from 'react';

type StatColor = 'blue' | 'emerald' | 'purple' | 'amber' | 'red';

interface StatCardProps {
    icon: ComponentType<SVGProps<SVGSVGElement>>;
    label: string;
    value: string | number;
    color?: StatColor;
}

const colorMap: Record<
    StatColor,
    { bg: string; text: string; darkBg: string; darkText: string }
> = {
    blue: {
        bg: 'bg-blue-50',
        text: 'text-blue-600',
        darkBg: 'dark:bg-blue-900/20',
        darkText: 'dark:text-blue-400',
    },
    emerald: {
        bg: 'bg-emerald-50',
        text: 'text-emerald-600',
        darkBg: 'dark:bg-emerald-900/20',
        darkText: 'dark:text-emerald-400',
    },
    purple: {
        bg: 'bg-purple-50',
        text: 'text-purple-600',
        darkBg: 'dark:bg-purple-900/20',
        darkText: 'dark:text-purple-400',
    },
    amber: {
        bg: 'bg-amber-50',
        text: 'text-amber-600',
        darkBg: 'dark:bg-amber-900/20',
        darkText: 'dark:text-amber-400',
    },
    red: {
        bg: 'bg-red-50',
        text: 'text-red-600',
        darkBg: 'dark:bg-red-900/20',
        darkText: 'dark:text-red-400',
    },
};

export default function StatCard({
    icon: Icon,
    label,
    value,
    color = 'blue',
}: StatCardProps) {
    const c = colorMap[color];

    return (
        <div className="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div className={`rounded-lg p-3 ${c.bg} ${c.darkBg}`}>
                <Icon className={`h-6 w-6 ${c.text} ${c.darkText}`} />
            </div>
            <div>
                <div className="text-xl leading-none font-bold text-gray-900 dark:text-neutral-100">
                    {value}
                </div>
                <div className="mt-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    {label}
                </div>
            </div>
        </div>
    );
}
