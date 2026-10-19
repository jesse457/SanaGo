import { Link } from '@inertiajs/react';
import type { ComponentType, ReactNode, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export interface EmptyStateAction {
    label: string;
    href: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
}

export interface EmptyStateProps {
    title: string;
    description?: ReactNode;
    /** Accepts an icon component or a ready-made node. */
    icon?: ComponentType<SVGProps<SVGSVGElement>> | ReactNode;
    action?: EmptyStateAction | ReactNode;
    secondaryAction?: ReactNode;
    className?: string;
    /** `false` keeps it as a plain block instead of a dashed panel. */
    panel?: boolean;
}

/**
 * EmptyState — the composed "nothing here yet" view. Calm icon well, direct
 * copy, one primary next step.
 */
export function EmptyState({
    title,
    description,
    icon,
    action,
    secondaryAction,
    className,
    panel = true,
}: EmptyStateProps) {
    const iconNode = (() => {
        if (!icon) {
            return null;
        }

        if (typeof icon === 'function') {
            const Icon = icon as ComponentType<SVGProps<SVGSVGElement>>;

            return <Icon className="h-5 w-5" />;
        }

        return icon;
    })();

    const actionNode = (() => {
        if (!action) {
            return null;
        }

        if (typeof action === 'object' && action !== null && 'href' in action) {
            const item = action as EmptyStateAction;
            const Icon = item.icon;

            return (
                <Link
                    href={item.href}
                    className="inline-flex h-10 items-center gap-2 rounded-md bg-ink px-4 text-[13px] font-semibold text-ink-inverse shadow-surface transition-[background-color,transform] duration-200 ease-premium hover:bg-ink/88 active:scale-[0.985]"
                >
                    {Icon && <Icon className="h-4 w-4" />}
                    {item.label}
                </Link>
            );
        }

        return action as ReactNode;
    })();

    return (
        <div
            className={cn(
                'animate-fade-in flex flex-col items-center justify-center px-6 py-14 text-center',
                panel &&
                    'rounded-xl border border-dashed border-line-strong bg-surface',
                className,
            )}
        >
            {iconNode && (
                <span className="mb-4 flex h-11 w-11 items-center justify-center rounded-xl border border-line bg-surface-muted text-ink-subtle">
                    {iconNode}
                </span>
            )}
            <h3 className="text-[15px] font-semibold tracking-tight text-ink">
                {title}
            </h3>
            {description && (
                <p className="mt-1.5 max-w-[46ch] text-[13px] leading-relaxed text-ink-muted">
                    {description}
                </p>
            )}
            {(actionNode || secondaryAction) && (
                <div className="mt-5 flex flex-wrap items-center justify-center gap-2">
                    {actionNode}
                    {secondaryAction}
                </div>
            )}
        </div>
    );
}
