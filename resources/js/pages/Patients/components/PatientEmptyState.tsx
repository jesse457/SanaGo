import type { ComponentType, SVGProps } from 'react';
import { EmptyState } from '@/Components/ui';

interface PatientEmptyStateProps {
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    title: string;
    description?: string;
    actionLabel?: string;
    actionHref?: string;
    actionIcon?: ComponentType<SVGProps<SVGSVGElement>>;
    secondaryAction?: React.ReactNode;
}

/**
 * PatientEmptyState — the dashed "nothing on file" panel used by all six
 * patient screens, so the calm copy and the single next step stay consistent.
 */
export function PatientEmptyState({
    icon,
    title,
    description,
    actionLabel,
    actionHref,
    actionIcon,
    secondaryAction,
}: PatientEmptyStateProps) {
    const action =
        actionLabel && actionHref
            ? { label: actionLabel, href: actionHref, icon: actionIcon }
            : undefined;

    return (
        <EmptyState
            icon={icon}
            title={title}
            description={description}
            action={action}
            secondaryAction={secondaryAction}
        />
    );
}
