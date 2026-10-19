import { Breadcrumbs } from '@/Components/ui/Breadcrumbs';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';

interface PageBreadcrumbProps {
    items: BreadcrumbItem[];
    className?: string;
}

/**
 * PageBreadcrumb — legacy breadcrumb trail kept for existing pages.
 *
 * New code should use `<Breadcrumbs>` from `@/Components/ui`, or pass
 * `breadcrumbs` straight to `<PageHeader>`.
 */
export default function PageBreadcrumb({
    items,
    className,
}: PageBreadcrumbProps) {
    return <Breadcrumbs items={items} showHome className={className} />;
}

export type { BreadcrumbItem };
