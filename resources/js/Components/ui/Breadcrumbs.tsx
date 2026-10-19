import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export interface BreadcrumbItem {
    label: string;
    href?: string;
}

export interface BreadcrumbsProps {
    items: BreadcrumbItem[];
    className?: string;
    /** Hide the leading home crumb when the parent already implies it. */
    showHome?: boolean;
    homeHref?: string;
}

/**
 * Breadcrumbs — sentence-case trail with a quiet current page. Every nested
 * screen gets a way back.
 */
export function Breadcrumbs({
    items,
    className,
    showHome = false,
    homeHref = '/dashboard',
}: BreadcrumbsProps) {
    return (
        <nav aria-label="Breadcrumb" className={cn('min-w-0', className)}>
            <ol className="flex flex-wrap items-center gap-1.5 text-[12px] text-ink-subtle">
                {showHome && (
                    <li className="flex items-center gap-1.5">
                        <Link
                            href={homeHref}
                            className="transition-colors duration-150 hover:text-ink"
                        >
                            Home
                        </Link>
                        <Separator />
                    </li>
                )}
                {items.map((item, index) => {
                    const isLast = index === items.length - 1;

                    return (
                        <li
                            key={`${item.label}-${index}`}
                            className="flex min-w-0 items-center gap-1.5"
                        >
                            {isLast || !item.href ? (
                                <span
                                    aria-current={isLast ? 'page' : undefined}
                                    className={cn(
                                        'truncate',
                                        isLast
                                            ? 'font-medium text-ink-muted'
                                            : 'text-ink-subtle',
                                    )}
                                >
                                    {item.label}
                                </span>
                            ) : (
                                <Link
                                    href={item.href}
                                    className="truncate transition-colors duration-150 hover:text-ink"
                                >
                                    {item.label}
                                </Link>
                            )}
                            {!isLast && <Separator />}
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}

function Separator() {
    return (
        <span aria-hidden="true" className="text-[11px] text-ink-subtle/60">
            /
        </span>
    );
}
