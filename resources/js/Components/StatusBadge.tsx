type BadgeColor =
    | 'gray'
    | 'blue'
    | 'emerald'
    | 'amber'
    | 'red'
    | 'rose'
    | 'purple';

interface StatusBadgeProps {
    label: string;
    color?: BadgeColor;
    className?: string;
}

const colorMap: Record<BadgeColor, string> = {
    gray: 'border-gray-200 bg-gray-50 text-gray-600 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-300',
    blue: 'border-blue-200 bg-blue-50 text-blue-600 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    emerald:
        'border-emerald-200 bg-emerald-50 text-emerald-600 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
    amber: 'border-amber-200 bg-amber-50 text-amber-600 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
    red: 'border-red-200 bg-red-50 text-red-600 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400',
    rose: 'border-rose-200 bg-rose-50 text-rose-600 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-400',
    purple: 'border-purple-200 bg-purple-50 text-purple-600 dark:border-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
};

export default function StatusBadge({
    label,
    color = 'gray',
    className = '',
}: StatusBadgeProps) {
    return (
        <span
            className={`inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase ${colorMap[color]} ${className}`}
        >
            {label}
        </span>
    );
}
