import type { ComponentType, ReactNode, SVGProps } from 'react';
import { EmptyState as UiEmptyState } from '@/Components/ui/EmptyState';

interface EmptyStateAction {
    label: string;
    href: string;
}

interface EmptyStateProps {
    icon?: ComponentType<SVGProps<SVGSVGElement>> | ReactNode;
    title: string;
    description?: ReactNode;
    action?: EmptyStateAction | ReactNode;
    /** Legacy flat props, still accepted. */
    actionLabel?: string;
    actionHref?: string;
    secondaryAction?: ReactNode;
    className?: string;
    panel?: boolean;
}

/**
 * EmptyState — legacy wrapper kept for existing pages.
 *
 * New code should import `<EmptyState>` from `@/Components/ui` directly.
 */
export default function EmptyState({
    icon,
    title,
    description,
    action,
    actionLabel,
    actionHref,
    secondaryAction,
    className,
    panel = true,
}: EmptyStateProps) {
    const resolvedAction =
        action ??
        (actionLabel && actionHref
            ? { label: actionLabel, href: actionHref }
            : undefined);

    return (
        <UiEmptyState
            icon={icon}
            title={title}
            description={description}
            action={resolvedAction}
            secondaryAction={secondaryAction}
            className={className}
            panel={panel}
        />
    );
}
