import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export interface BreadcrumbItem {
    label: string;
    href?: string;
    /** Optional leading glyph (a module icon). Rendered before the label. */
    icon?: ReactNode;
}

export interface BreadcrumbsProps {
    items: BreadcrumbItem[];
    className?: string;
    /**
     * Prepend the canonical Home crumb. Callers may ALSO pass a Home crumb of
     * their own — the duplicate is dropped automatically so the trail can
     * never read "Home / Home / …".
     */
    showHome?: boolean;
    homeHref?: string;
    /** Label for the injected home crumb; defaults to the locale's Home. */
    homeLabel?: string;
}

const DEFAULT_HOME_HREF = '/dashboard';

/**
 * Hrefs that all mean "the dashboard". A crumb pointing at any of these is the
 * home crumb regardless of the route helper that produced it.
 */
const HOME_HREFS = new Set([
    '/',
    '/dashboard',
    '/admin',
    '/super-admin',
    '/super-admin/dashboard',
    '/home',
    '/portal',
    '/app',
]);

/** Labels (case/accents aside) that mean "home" in the shipped locales. */
const HOME_LABELS = new Set([
    'home',
    'accueil',
    'dashboard',
    'tableau de bord',
    'acceuil',
    'inicio',
]);

function normaliseHref(href?: string): string | undefined {
    if (!href) {
        return undefined;
    }

    const stripped = href.split('?')[0].split('#')[0];
    const trimmed =
        stripped.length > 1 ? stripped.replace(/\/+$/, '') : stripped;

    return trimmed || '/';
}

function normaliseLabel(label: string): string {
    return label
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();
}

/**
 * True when the item is a Home/dashboard crumb. A crumb with no href counts as
 * home only when its label matches, since several pages render a bare
 * `{ label: t('common.home') }` as their first item.
 */
export function isHomeCrumb(item: BreadcrumbItem): boolean {
    const href = normaliseHref(item.href);

    if (href && HOME_HREFS.has(href)) {
        return true;
    }

    return HOME_LABELS.has(normaliseLabel(item.label));
}

/**
 * Normalises a caller-supplied trail so it renders correctly under ANY
 * breadcrumb implementation:
 *
 * - the leading Home crumb is removed (it is injected by `showHome`, or by the
 *   page header, or not wanted at all);
 * - consecutive crumbs with the same label AND the same href are collapsed
 *   (an exact duplicate, which is always an authoring mistake);
 * - the last crumb loses its href: a current page is not a link to itself.
 */
export function normaliseBreadcrumbs(
    items: BreadcrumbItem[],
): BreadcrumbItem[] {
    const trimmed = [...items];

    // Drop the leading home crumb — at most one, but loop to be safe.
    while (trimmed.length > 1 && isHomeCrumb(trimmed[0])) {
        trimmed.shift();
    }

    // A single home crumb is not a trail.
    if (trimmed.length === 1 && isHomeCrumb(trimmed[0])) {
        return [];
    }

    const deduped: BreadcrumbItem[] = [];

    trimmed.forEach((item) => {
        const previous = deduped[deduped.length - 1];

        if (
            previous &&
            normaliseLabel(previous.label) === normaliseLabel(item.label) &&
            normaliseHref(previous.href) === normaliseHref(item.href)
        ) {
            return;
        }

        deduped.push({ ...item });
    });

    return deduped.map((item, index) => ({
        ...item,
        href: index === deduped.length - 1 ? undefined : item.href,
    }));
}

/**
 * Breadcrumbs — sentence-case trail with a quiet current page.
 *
 * `showHome` injects one canonical Home crumb; because caller-supplied home
 * crumbs are stripped first, the output can never repeat it. Every trail ends
 * on a non-link current page for `aria-current`.
 */
export function Breadcrumbs({
    items,
    className,
    showHome = false,
    homeHref = DEFAULT_HOME_HREF,
    homeLabel = 'Home',
}: BreadcrumbsProps) {
    const trail = normaliseBreadcrumbs(items);

    const home: BreadcrumbItem | null =
        showHome && trail.length > 0
            ? { label: homeLabel, href: homeHref }
            : null;

    const all = home ? [home, ...trail] : trail;

    if (all.length === 0) {
        return null;
    }

    return (
        <nav aria-label="Breadcrumb" className={cn('min-w-0', className)}>
            <ol className="flex flex-wrap items-center gap-1.5 text-[12px] text-ink-subtle">
                {all.map((item, index) => {
                    const isLast = index === all.length - 1;

                    return (
                        <li
                            key={`${item.label}-${index}`}
                            className="flex min-w-0 items-center gap-1.5"
                        >
                            {isLast || !item.href ? (
                                <span
                                    aria-current={isLast ? 'page' : undefined}
                                    className={cn(
                                        'truncate',
                                        isLast
                                            ? 'font-medium text-ink-muted'
                                            : 'text-ink-subtle',
                                    )}
                                >
                                    {item.label}
                                </span>
                            ) : (
                                <Link
                                    href={item.href}
                                    className="flex min-w-0 items-center gap-1.5 transition-colors duration-150 hover:text-ink"
                                >
                                    {item.icon && (
                                        <span
                                            aria-hidden="true"
                                            className="shrink-0 opacity-70"
                                        >
                                            {item.icon}
                                        </span>
                                    )}
                                    <span className="truncate">
                                        {item.label}
                                    </span>
                                </Link>
                            )}
                            {!isLast && <Separator />}
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}

function Separator() {
    return (
        <span aria-hidden="true" className="text-[11px] text-ink-subtle/60">
            /
        </span>
    );
}
