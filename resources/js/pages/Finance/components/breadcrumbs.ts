/** Locale-neutral helpers used by every finance page's header. */

export interface FinanceBreadcrumbLabels {
    home: string;
    finance: string;
    page: string;
}

/**
 * Finance breadcrumb trail: Home / Finance / <page>. Kept in one place so the
 * two trailing crumbs never drift between index, create and detail screens.
 */
export function financeBreadcrumbs(
    labels: FinanceBreadcrumbLabels,
    pageHref?: string,
): { label: string; href?: string }[] {
    const trail: { label: string; href?: string }[] = [
        { label: labels.home, href: route('dashboard') },
    ];

    if (pageHref) {
        trail.push({ label: labels.finance, href: pageHref });
    } else {
        trail.push({ label: labels.finance });
    }

    trail.push({ label: labels.page });

    return trail;
}
