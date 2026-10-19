import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

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
    className?: string;
}

type RenderItem =
    | { type: 'link'; link: LinkItem; originalIndex: number }
    | { type: 'ellipsis'; key: string };

const baseButton =
    'relative flex h-8 min-w-8 items-center justify-center rounded-md border px-2.5 text-[12.5px] font-medium tabular-nums transition-[background-color,border-color,color,transform] duration-200 ease-premium active:scale-[0.97]';

/**
 * Pagination — Laravel paginator renderer. Quiet hairline buttons, ink-solid
 * current page, tabular figures.
 */
export const Pagination = ({ links, meta, className }: Props) => {
    const { t } = useTranslation();

    if (!links || links.length <= 3) {
        return null;
    }

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

    // Safely formats translation text or provides a fallback to prevent raw translation keys
    const getShowingText = () => {
        if (!meta) {
            return '';
        }

        let text = t('pagination.showing', {
            from: meta.from,
            to: meta.to,
            total: meta.total,
        });

        // 1. Fallback if translation key doesn't exist in locale files
        if (text === 'pagination.showing') {
            return `Showing ${meta.from} to ${meta.to} of ${meta.total} results`;
        }

        const replacements = {
            from: String(meta.from),
            to: String(meta.to),
            total: String(meta.total),
        };

        // 2. Safe-replacement for common localization styles (:param, {param}, or {{param}})
        Object.entries(replacements).forEach(([key, value]) => {
            text = text
                .replace(`:${key}`, value)
                .replace(`{${key}}`, value)
                .replace(`{{${key}}}`, value);
        });

        return text;
    };

    // links[0] is typically the "Previous" link
    // links[links.length - 1] is typically the "Next" link
    const pages = links.slice(1, -1);
    const activeIndex = pages.findIndex((p) => p.active);

    const indicesToShow = new Set<number>();

    // If there are 8 or fewer page numbers, display them all
    if (pages.length <= 8) {
        for (let i = 0; i < pages.length; i++) {
            indicesToShow.add(i);
        }
    } else {
        // Always include first and last page
        indicesToShow.add(0);
        indicesToShow.add(pages.length - 1);

        // Include pages around the active page (range of 1 on each side)
        const active = activeIndex === -1 ? 0 : activeIndex;
        const range = 1;

        for (
            let i = Math.max(0, active - range);
            i <= Math.min(pages.length - 1, active + range);
            i++
        ) {
            indicesToShow.add(i);
        }
    }

    const sortedIndices = Array.from(indicesToShow).sort((a, b) => a - b);
    const renderedItems: RenderItem[] = [];

    // Add the "Previous" link
    renderedItems.push({ type: 'link', link: links[0], originalIndex: 0 });

    let lastIndex = -1;

    for (const currentIndex of sortedIndices) {
        if (lastIndex !== -1) {
            const diff = currentIndex - lastIndex;

            if (diff === 2) {
                // If only one page is missing in between, display it instead of using an ellipsis
                const missingIndex = lastIndex + 1;
                renderedItems.push({
                    type: 'link',
                    link: pages[missingIndex],
                    originalIndex: missingIndex + 1,
                });
            } else if (diff > 2) {
                // Render ellipsis for larger gaps
                renderedItems.push({
                    type: 'ellipsis',
                    key: `ellipsis-${lastIndex}-${currentIndex}`,
                });
            }
        }

        renderedItems.push({
            type: 'link',
            link: pages[currentIndex],
            originalIndex: currentIndex + 1,
        });
        lastIndex = currentIndex;
    }

    // Add the "Next" link
    renderedItems.push({
        type: 'link',
        link: links[links.length - 1],
        originalIndex: links.length - 1,
    });

    return (
        <div
            className={cn(
                'flex flex-col items-center justify-between gap-3 sm:flex-row',
                className,
            )}
        >
            {meta && (
                <p className="text-[12px] text-ink-subtle tabular-nums">
                    {getShowingText()}
                </p>
            )}

            <nav className="flex items-center gap-1" aria-label="Pagination">
                {renderedItems.map((item, index) => {
                    if (item.type === 'ellipsis') {
                        return (
                            <span
                                key={item.key}
                                aria-hidden="true"
                                className="flex h-8 min-w-8 cursor-default items-center justify-center px-1 text-[12.5px] text-ink-subtle select-none"
                            >
                                …
                            </span>
                        );
                    }

                    const { link } = item;

                    // If the URL is null (disabled navigation or is the current active page)
                    if (link.url === null) {
                        return (
                            <span
                                key={`${link.label}-${index}`}
                                aria-disabled="true"
                                className={cn(
                                    baseButton,
                                    link.active
                                        ? 'border-transparent bg-ink text-ink-inverse'
                                        : 'cursor-not-allowed border-line bg-surface-muted text-ink-subtle/70',
                                )}
                            >
                                {getLabel(link.label)}
                            </span>
                        );
                    }

                    return (
                        <Link
                            key={`${link.label}-${index}`}
                            href={link.url}
                            preserveScroll
                            className={cn(
                                baseButton,
                                link.active
                                    ? 'border-transparent bg-ink text-ink-inverse shadow-surface'
                                    : 'border-line bg-surface text-ink-muted hover:border-line-strong hover:bg-surface-hover hover:text-ink',
                            )}
                        >
                            {getLabel(link.label)}
                        </Link>
                    );
                })}
            </nav>
        </div>
    );
};
