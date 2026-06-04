import { Link } from '@inertiajs/react';
import type { ComponentType, SVGProps } from 'react';

interface EmptyStateAction {
    label: string;
    href: string;
}

interface EmptyStateProps {
    icon: ComponentType<SVGProps<SVGSVGElement>>;
    title: string;
    description?: string;
    action?: EmptyStateAction;
}

export default function EmptyState({
    icon: Icon,
    title,
    description,
    action,
}: EmptyStateProps) {
    return (
        <div className="rounded-2xl border-2 border-dashed border-gray-200 bg-white p-16 text-center dark:border-neutral-700 dark:bg-neutral-800">
            <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-neutral-700">
                <Icon className="h-8 w-8 text-gray-400 dark:text-neutral-500" />
            </div>
            <h3 className="mt-4 text-lg font-bold text-gray-900 dark:text-neutral-100">
                {title}
            </h3>
            {description && (
                <p className="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                    {description}
                </p>
            )}
            {action && (
                <Link
                    href={action.href}
                    className="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-700"
                >
                    {action.label}
                </Link>
            )}
        </div>
    );
}
