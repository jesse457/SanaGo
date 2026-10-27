import { createContext, useContext } from 'react';
import type { ReactNode } from 'react';
import { Breadcrumbs } from '@/Components/ui/Breadcrumbs';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';
import { completeBreadcrumbTrail } from '@/lib/breadcrumbs';
import type { BreadcrumbTrail } from '@/lib/breadcrumbs';
import { cn } from '@/lib/utils';

/**
 * Layout-derived breadcrumb trail.
 * --------------------------------
 * Pages that never pass `breadcrumbs` would otherwise have no way back and no
 * indication of depth. Each layout derives the trail from the current URL plus
 * its own navigation config and publishes it through this context, so
 * `<PageHeader>` renders `module › page` without a single page being touched.
 *
 * Precedence inside PageHeader:
 *   1. the `breadcrumbs` prop (explicit, completed with module › page)
 *   2. the nearest `BreadcrumbTrailProvider` value (layout-derived)
 *   3. nothing — the header simply omits the trail
 */
const BreadcrumbTrailContext = createContext<BreadcrumbTrail | null>(null);

export const BreadcrumbTrailProvider = BreadcrumbTrailContext.Provider;

/** The layout-derived trail for the current URL, if any. */
export function useDerivedBreadcrumbs(): BreadcrumbTrail | null {
    return useContext(BreadcrumbTrailContext);
}

export interface PageHeaderProps {
    title: ReactNode;
    /** Small uppercase label above the title. */
    eyebrow?: ReactNode;
    description?: ReactNode;
    actions?: ReactNode;
    /**
     * Explicit trail. When omitted the layout-derived trail is used, so a page
     * that never declares breadcrumbs still shows module → page. An explicit
     * trail is a *continuation*: the module crumb is prepended when missing and
     * crumbs that point at a nav page inherit that page's nav label.
     */
    breadcrumbs?: BreadcrumbItem[];
    /** Set to false to suppress the trail entirely, derived or explicit. */
    showBreadcrumbs?: boolean;
    /** Inline meta row under the description (counts, timestamps, badges). */
    meta?: ReactNode;
    className?: string;
    bordered?: boolean;
}

/**
 * PageHeader — the opening statement of every screen and the SINGLE owner of a
 * page's title: optional breadcrumbs, an eyebrow, a tight display title,
 * supporting description and right-aligned actions.
 *
 * The layout's top bar is a utility strip only and never renders the page
 * title; that is what removes the old duplicated-heading problem.
 */
export function PageHeader({
    title,
    eyebrow,
    description,
    actions,
    breadcrumbs,
    showBreadcrumbs = true,
    meta,
    className,
    bordered = true,
}: PageHeaderProps) {
    const derived = useDerivedBreadcrumbs();
    const trail = breadcrumbs
        ? derived
            ? completeBreadcrumbTrail(breadcrumbs, derived)
            : breadcrumbs
        : (derived?.items ?? []);
    const hasTrail = showBreadcrumbs && trail.length > 0;

    return (
        <header
            className={cn(
                'flex flex-col gap-4',
                bordered && 'border-b border-line pb-5',
                className,
            )}
        >
            {hasTrail && <Breadcrumbs items={trail} />}

            <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div className="flex min-w-0 items-start gap-3">
                    <div className="min-w-0">
                        {eyebrow && (
                            <p className="mb-1 text-[10.5px] font-semibold tracking-[0.16em] text-ink-subtle uppercase">
                                {eyebrow}
                            </p>
                        )}
                        <h1 className="text-[22px] leading-[1.15] font-semibold tracking-[-0.022em] text-ink sm:text-[26px]">
                            {title}
                        </h1>
                        {description && (
                            <p className="mt-1.5 max-w-[68ch] text-[13px] leading-relaxed text-ink-muted">
                                {description}
                            </p>
                        )}
                        {meta && (
                            <div className="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-[12px] text-ink-subtle">
                                {meta}
                            </div>
                        )}
                    </div>
                </div>

                {actions && (
                    <div className="flex shrink-0 flex-wrap items-center gap-2">
                        {actions}
                    </div>
                )}
            </div>
        </header>
    );
}
