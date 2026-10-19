import type { ComponentType, ReactNode, SVGProps } from 'react';
import { SectionCard } from '@/Components/ui/Card';

interface PageSectionProps {
    title?: string;
    description?: string;
    icon?: ComponentType<SVGProps<SVGSVGElement>> | ReactNode;
    children: ReactNode;
    className?: string;
    contentClassName?: string;
    actions?: ReactNode;
    footer?: ReactNode;
}

/**
 * PageSection — legacy section panel kept for existing pages.
 *
 * New code should use `<SectionCard>` / `<Card>` from `@/Components/ui`
 * directly.
 */
export default function PageSection({
    title,
    description,
    icon,
    children,
    className,
    contentClassName,
    actions,
    footer,
}: PageSectionProps) {
    return (
        <SectionCard
            title={title}
            description={description}
            icon={icon as ComponentType<SVGProps<SVGSVGElement>>}
            actions={actions}
            footer={footer}
            className={className}
            contentClassName={contentClassName}
        >
            {children}
        </SectionCard>
    );
}
