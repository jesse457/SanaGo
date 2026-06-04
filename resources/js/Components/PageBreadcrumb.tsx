import { ChevronRightIcon, HomeIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';

export interface BreadcrumbItem {
    label: string;
    href?: string;
}

interface PageBreadcrumbProps {
    items: BreadcrumbItem[];
}

export default function PageBreadcrumb({ items }: PageBreadcrumbProps) {
    return (
        <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
            <ol className="flex items-center gap-1.5">
                {items.map((item, index) => (
                    <li key={index} className="flex items-center gap-1.5">
                        {index > 0 && (
                            <ChevronRightIcon className="h-3 w-3 text-gray-400 dark:text-neutral-500" />
                        )}
                        {index === 0 && (
                            <HomeIcon className="mr-0.5 h-4 w-4 text-gray-400 dark:text-neutral-500" />
                        )}
                        {item.href ? (
                            <Link
                                href={item.href}
                                className="transition-colors hover:text-blue-600 dark:hover:text-blue-400"
                            >
                                {item.label}
                            </Link>
                        ) : (
                            <span className="font-medium text-blue-600 dark:text-blue-400">
                                {item.label}
                            </span>
                        )}
                    </li>
                ))}
            </ol>
        </nav>
    );
}
