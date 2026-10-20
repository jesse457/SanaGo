import { createContext, useContext } from 'react';
import type { ComponentType, ReactNode } from 'react';
import { Breadcrumbs } from '@/Components/ui/Breadcrumbs';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';
import { cn } from '@/lib/utils';

/**
 * Layout-derived breadcrumb trail.
 * --------------------------------
 * Pages that never pass `breadcrumbs` would otherwise have no way back and no
 * indication of depth. `AppLayout` derives a trail from the current URL plus
 * `NAV_CONFIG` and publishes it through this context, so `<PageHeader>` can
 * fall back to it without a single page being touched.
 *
 * Precedence inside PageHeader:
 *   1. the `breadcrumbs` prop (explicit, always wins)
 *   2. the nearest `BreadcrumbTrailProvider` value (layout-derived)
 *   3. nothing — the header simply omits the trail
 */
const BreadcrumbTrailContext = createContext<BreadcrumbItem[] | null>(null);

export const BreadcrumbTrailProvider = BreadcrumbTrailContext.Provider;

/** The layout-derived trail for the current URL, if any. */
export function useDerivedBreadcrumbs(): BreadcrumbItem[] | null {
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
     * that never declares breadcrumbs still shows module → page.
     */
    breadcrumbs?: BreadcrumbItem[];
    /** Set to false to suppress the trail entirely, derived or explicit. */
    showBreadcrumbs?: boolean;
    /** Inline meta row under the description (counts, timestamps, badges). */
    meta?: ReactNode;
    icon?: ComponentType<React.SVGProps<SVGSVGElement>>;
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
    icon: Icon,
    className,
    bordered = true,
}: PageHeaderProps) {
    const derived = useDerivedBreadcrumbs();
    const trail = breadcrumbs ?? derived;
    const hasTrail = showBreadcrumbs && trail && trail.length > 0;

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
                    {Icon && (
                        <span className="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                            <Icon className="h-5 w-5" />
                        </span>
                    )}
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
