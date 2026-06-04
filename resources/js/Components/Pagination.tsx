import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import React from 'react';
import { useTranslation } from '@/hooks/useTranslation';

interface LinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    links: LinkItem[];
    meta?: {
        from: number;
        to: number;
        total: number;
    };
}

export const Pagination = ({ links, meta }: Props) => {
    const { t } = useTranslation();

    // Don't render if there's only one page
    if (links?.length <= 3) {
        return null;
    }

    // Helper to clean up Laravel's default pagination labels
    const getLabel = (label: string) => {
        if (label.includes('Previous')) {
            return (
                <ChevronLeftIcon
                    className="h-4 w-4"
                    aria-label={t('pagination.previous')}
                />
            );
        }

        if (label.includes('Next')) {
            return (
                <ChevronRightIcon
                    className="h-4 w-4"
                    aria-label={t('pagination.next')}
                />
            );
        }

        return label;
    };

    return (
        <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
            {/* Results Info (Meta) */}
            {meta && (
                <div className="text-sm text-slate-500 dark:text-neutral-400">
                    {t('pagination.showing', {
                        from: meta.from,
                        to: meta.to,
                        total: meta.total,
                    })}
                </div>
            )}

            {/* Links */}
            <nav
                className="flex items-center -space-x-px"
                aria-label="Pagination"
            >
                {links?.map((link, key) => {
                    const isFirst = key === 0;
                    const isLast = key === links.length - 1;

                    // If the URL is null (disabled link)
                    if (link.url === null) {
                        return (
                            <div
                                key={key}
                                className={`flex cursor-not-allowed items-center justify-center border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-600 ${isFirst ? 'rounded-l-lg' : ''} ${isLast ? 'rounded-r-lg' : ''} `}
                            >
                                {getLabel(link.label)}
                            </div>
                        );
                    }

                    return (
                        <Link
                            key={key}
                            href={link.url}
                            preserveScroll
                            className={`relative flex items-center justify-center border px-3 py-2 text-sm font-bold transition-all duration-200 ${isFirst ? 'rounded-l-lg' : ''} ${isLast ? 'rounded-r-lg' : ''} ${
                                link.active
                                    ? 'z-10 border-blue-600 bg-blue-600 text-white'
                                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-blue-400'
                            } `}
                        >
                            {getLabel(link.label)}
                        </Link>
                    );
                })}
            </nav>
        </div>
    );
};
