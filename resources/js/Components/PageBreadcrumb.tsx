import { Breadcrumbs } from '@/Components/ui/Breadcrumbs';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface PageBreadcrumbProps {
    items: BreadcrumbItem[];
    className?: string;
}

/**
 * PageBreadcrumb — legacy breadcrumb trail kept for pages that render the
 * trail themselves instead of passing `breadcrumbs` to `<PageHeader>`.
 *
 * It forwards to the single `<Breadcrumbs>` implementation with `showHome`, and
 * `Breadcrumbs` strips any caller-supplied Home crumb first. A page whose items
 * start with `{ label: t('common.home'), href: '/dashboard' }` therefore
 * renders exactly one Home crumb instead of two.
 *
 * @deprecated Prefer `<PageHeader breadcrumbs={…} />` — the page header owns the
 * single title *and* the trail, which keeps depth consistent across the app.
 */
export default function PageBreadcrumb({
    items,
    className,
}: PageBreadcrumbProps) {
    const { t } = useTranslation();

    return (
        <Breadcrumbs
            items={items}
            showHome
            homeLabel={t('common.home', 'Home')}
            className={cn(className)}
        />
    );
}

export type { BreadcrumbItem };
