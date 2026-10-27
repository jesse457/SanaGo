import { isHomeCrumb, normaliseBreadcrumbs } from '@/Components/ui/Breadcrumbs';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';

/**
 * One breadcrumb vocabulary for the whole app.
 * -------------------------------------------
 * Every screen reads the same way:
 *
 *   module › page › deeper step
 *   Patient Management › Patient Directory › Amine Ibekwe
 *
 * The trail is derived from the URL plus the layout's own navigation config, so
 * the sidebar and the breadcrumb can never disagree about what a page is
 * called. A page may still pass its own crumbs, but they are treated as a
 * *continuation*: a missing module crumb is prepended and any crumb that points
 * at a nav item is relabelled with that item's nav label.
 */

export type BreadcrumbTranslate = (key: string, fallback?: string) => string;

export interface TrailNavItem {
    label: string;
    labelKey: string;
    href: string;
}

export interface TrailNavGroup {
    category: string;
    categoryKey: string;
    items: TrailNavItem[];
}

export interface BreadcrumbTrail {
    /** Module › page › … for the current URL. */
    items: BreadcrumbItem[];
    /** Flattened nav entries, used to complete and relabel page-supplied crumbs. */
    nav: FlatNavEntry[];
}

interface FlatNavEntry extends TrailNavItem {
    moduleLabel: string;
    moduleKey: string;
    moduleHref: string;
}

/** Segments that read better translated than title-cased. */
const SEGMENT_KEYS: Record<string, { key: string; fallback: string }> = {
    create: { key: 'breadcrumb.create', fallback: 'New' },
    edit: { key: 'breadcrumb.edit', fallback: 'Edit' },
    show: { key: 'breadcrumb.details', fallback: 'Details' },
    view: { key: 'breadcrumb.details', fallback: 'Details' },
    print: { key: 'breadcrumb.print', fallback: 'Print' },
    calendar: { key: 'breadcrumb.calendar', fallback: 'Calendar' },
    checkin: { key: 'breadcrumb.checkin', fallback: 'Check-in' },
    profile: { key: 'breadcrumb.profile', fallback: 'Profile' },
    enter: { key: 'breadcrumb.enter', fallback: 'Enter results' },
    receipt: { key: 'breadcrumb.receipt', fallback: 'Receipt' },
};

function pathOf(url: string): string {
    return url.split('?')[0].split('#')[0];
}

function segmentsOf(path: string): string[] {
    return path.split('/').filter(Boolean);
}

/** Numeric keys, UUIDs and long hex ids identify a record, not a step. */
function isRecordSegment(segment: string): boolean {
    return /^\d+$/.test(segment) || /^[0-9a-f-]{8,}$/i.test(segment);
}

export function normaliseLabel(label: string): string {
    return label
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase();
}

function sameHref(a?: string, b?: string): boolean {
    if (!a || !b) {
        return false;
    }

    const clean = (href: string) => pathOf(href).replace(/\/+$/, '');

    return clean(a) === clean(b);
}

function titleCase(segment: string): string {
    return segment
        .split('-')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

function segmentLabel(segment: string, t: BreadcrumbTranslate): string {
    const known = SEGMENT_KEYS[segment];

    return known ? t(known.key, known.fallback) : titleCase(segment);
}

/**
 * Final pass shared by every trail: contiguous crumbs with the same label are
 * collapsed, and the current page is never a link to itself.
 */
function finalise(items: BreadcrumbItem[]): BreadcrumbItem[] {
    const collapsed: BreadcrumbItem[] = [];

    items.forEach((item) => {
        const previous = collapsed[collapsed.length - 1];

        if (
            previous &&
            normaliseLabel(previous.label) === normaliseLabel(item.label)
        ) {
            return;
        }

        collapsed.push({ ...item });
    });

    return collapsed.map((item, index) => ({
        ...item,
        href: index === collapsed.length - 1 ? undefined : item.href,
    }));
}

function knownModuleLabels(entries: FlatNavEntry[]): Set<string> {
    return new Set(entries.map((entry) => normaliseLabel(entry.moduleLabel)));
}

/** Flattens the layout's nav config, remembering which module owns each entry. */
export function flattenNav(
    groups: TrailNavGroup[],
    t: BreadcrumbTranslate,
): FlatNavEntry[] {
    return groups.flatMap((group) => {
        const moduleLabel = t(group.categoryKey, group.category);
        const moduleHref = group.items[0]?.href ?? '/';

        return group.items.map((item) => ({
            label: t(item.labelKey, item.label),
            labelKey: item.labelKey,
            href: item.href,
            moduleLabel,
            moduleKey: group.categoryKey,
            moduleHref,
        }));
    });
}

function trailForEntry(
    entry: FlatNavEntry,
    segments: string[],
    t: BreadcrumbTranslate,
): BreadcrumbItem[] {
    const base = segmentsOf(entry.href);
    const trail: BreadcrumbItem[] = [
        { label: entry.moduleLabel, href: entry.moduleHref },
        { label: entry.label, href: entry.href },
    ];

    segments.slice(base.length).forEach((segment, index) => {
        if (isRecordSegment(segment) || segment === 'index') {
            return;
        }

        trail.push({
            label: segmentLabel(segment, t),
            href: `/${segments.slice(0, base.length + index + 1).join('/')}`,
        });
    });

    return trail;
}

/**
 * Module › page › … derived from the current URL.
 *
 * `extras` are pages that exist but live outside the sidebar (settings,
 * notifications, profile …): passing them keeps their trail from collapsing to
 * a single, module-less crumb.
 */
export function deriveBreadcrumbTrail(
    url: string,
    groups: TrailNavGroup[],
    t: BreadcrumbTranslate,
    extras: TrailNavGroup[] = [],
): BreadcrumbTrail {
    const path = pathOf(url);
    const segments = segmentsOf(path);
    const nav = flattenNav([...groups, ...extras], t);

    if (segments.length === 0) {
        return { items: [], nav };
    }

    const entry = nav
        .filter(
            (candidate) =>
                path === candidate.href ||
                path.startsWith(candidate.href + '/'),
        )
        .sort((a, b) => b.href.length - a.href.length)[0];

    if (entry) {
        return { items: trailForEntry(entry, segments, t), nav };
    }

    // Off-nav page: build module › page from the raw segments.
    const trail: BreadcrumbItem[] = [
        { label: titleCase(segments[0]), href: `/${segments[0]}` },
    ];

    segments.slice(1).forEach((segment, index) => {
        if (isRecordSegment(segment) || segment === 'index') {
            return;
        }

        trail.push({
            label: segmentLabel(segment, t),
            href: `/${segments.slice(0, index + 2).join('/')}`,
        });
    });

    return { items: trail.length > 1 ? trail : [], nav };
}

/**
 * Completes a page-supplied trail with the module crumb and with the nav label
 * of every crumb that points at a nav page.
 *
 * A page therefore only has to describe what is specific to it (a record name,
 * a step); the layout supplies the module › page skeleton.
 */
export function completeBreadcrumbTrail(
    explicit: BreadcrumbItem[],
    trail: BreadcrumbTrail,
): BreadcrumbItem[] {
    const derived = trail.items;
    const cleaned = normaliseBreadcrumbs(explicit).filter(
        (crumb) => !isHomeCrumb(crumb),
    );

    if (cleaned.length === 0) {
        return derived;
    }

    // "Just the page's own title" adds nothing to a module › page trail — the
    // header already prints that name as the h1.
    if (cleaned.length === 1 && !cleaned[0].href && derived.length >= 2) {
        return derived;
    }

    // A page that opens with a module name ("Finance › Claims › #7") has
    // already chosen its module; never stack a second one in front of it.
    const moduleLabels = knownModuleLabels(trail.nav);
    const opensWithModule = moduleLabels.has(normaliseLabel(cleaned[0].label));

    const relabelled = cleaned.map((crumb) => {
        const entry = trail.nav.find((candidate) =>
            sameHref(candidate.href, crumb.href),
        );

        // Never rename a crumb that already names a module: "Patient Management"
        // pointing at /patients stays the module, "Finance" stays Finance.
        if (!entry || moduleLabels.has(normaliseLabel(crumb.label))) {
            return { ...crumb };
        }

        return { ...crumb, label: entry.label };
    });

    if (opensWithModule) {
        return finalise(relabelled);
    }

    const firstEntry = trail.nav.find((candidate) =>
        sameHref(candidate.href, cleaned[0].href),
    );
    const module: BreadcrumbItem | null = firstEntry
        ? { label: firstEntry.moduleLabel, href: firstEntry.moduleHref }
        : (derived[0] ?? null);

    const hasModule =
        !!module &&
        normaliseLabel(cleaned[0].label) === normaliseLabel(module.label);

    return finalise(
        hasModule || !module ? relabelled : [module, ...relabelled],
    );
}
