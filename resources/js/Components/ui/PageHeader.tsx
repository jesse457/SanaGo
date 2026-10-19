import type { ComponentType, ReactNode } from 'react';
import { Breadcrumbs } from '@/Components/ui/Breadcrumbs';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';
import { cn } from '@/lib/utils';

export interface PageHeaderProps {
    title: ReactNode;
    /** Small uppercase label above the title. */
    eyebrow?: ReactNode;
    description?: ReactNode;
    actions?: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
    /** Inline meta row under the description (counts, timestamps, badges). */
    meta?: ReactNode;
    icon?: ComponentType<React.SVGProps<SVGSVGElement>>;
    className?: string;
    bordered?: boolean;
}

/**
 * PageHeader — the opening statement of every screen: optional breadcrumbs, an
 * eyebrow, a tight display title, supporting description and right-aligned
 * actions. One implementation, used by every page.
 */
export function PageHeader({
    title,
    eyebrow,
    description,
    actions,
    breadcrumbs,
    meta,
    icon: Icon,
    className,
    bordered = true,
}: PageHeaderProps) {
    return (
        <header
            className={cn(
                'flex flex-col gap-4',
                bordered && 'border-b border-line pb-5',
                className,
            )}
        >
            {breadcrumbs && breadcrumbs.length > 0 && (
                <Breadcrumbs items={breadcrumbs} />
            )}

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
                        <h1 className="text-[22px] leading-[1.15] font-semibold tracking-[-0.02em] text-ink sm:text-[26px]">
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
