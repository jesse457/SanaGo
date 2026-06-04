import { cn } from '@/lib/utils';

interface FeatureCardProps {
    icon: React.ReactNode;
    title: string;
    description: string;
    color: string;
    className?: string;
}

export default function FeatureCard({
    icon,
    title,
    description,
    color,
    className,
}: FeatureCardProps) {
    return (
        <div
            className={cn(
                'group rounded-xl border border-slate-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-900',
                className,
            )}
        >
            <div
                className={cn(
                    'mb-6 flex h-12 w-12 items-center justify-center rounded-2xl transition duration-300 group-hover:scale-110',
                    color,
                )}
            >
                {icon}
            </div>
            <h3 className="mb-3 text-lg font-bold text-slate-900 transition group-hover:text-blue-600 dark:text-neutral-100">
                {title}
            </h3>
            <p className="text-sm leading-relaxed text-slate-500 dark:text-neutral-400">
                {description}
            </p>
        </div>
    );
}
