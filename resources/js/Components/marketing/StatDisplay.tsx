interface StatDisplayProps {
    value: string;
    label: string;
}

export default function StatDisplay({ value, label }: StatDisplayProps) {
    return (
        <div className="border-l-2 border-blue-600 pl-4">
            <p className="text-2xl font-black text-slate-900 dark:text-neutral-100">
                {value}
            </p>
            <p className="mt-1 text-xs font-medium text-slate-400 dark:text-neutral-500">
                {label}
            </p>
        </div>
    );
}
