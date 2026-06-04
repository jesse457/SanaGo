import type { ComponentType, ReactNode, SVGProps } from 'react';

interface PageSectionProps {
    title?: string;
    description?: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    children: ReactNode;
}

export default function PageSection({
    title,
    description,
    icon: Icon,
    children,
}: PageSectionProps) {
    return (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            {title && (
                <div className="flex items-center gap-4 border-b border-gray-100 bg-gray-50/50 p-6 dark:border-neutral-700 dark:bg-neutral-700/30">
                    {Icon && (
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600">
                            <Icon className="h-6 w-6 text-white" />
                        </div>
                    )}
                    <div>
                        <h2 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                            {title}
                        </h2>
                        {description && (
                            <p className="mt-0.5 text-sm text-gray-500 dark:text-neutral-400">
                                {description}
                            </p>
                        )}
                    </div>
                </div>
            )}
            {children}
        </div>
    );
}
