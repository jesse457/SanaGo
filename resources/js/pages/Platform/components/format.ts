import type { BadgeTone } from '@/Components/ui';
import {
    formatCurrency as formatSharedCurrency,
    formatCurrencyCompact as formatSharedCurrencyCompact,
    formatNumber as formatSharedNumber,
} from '@/lib/format';

/**
 * Formatting + tone helpers shared by the landlord ("platform control") pages.
 *
 * Money always goes through `@/lib/format`, which owns the app-wide money
 * grammar (`1 500 000 FCFA`, no hardcoded "$"). These wrappers only add the
 * landlord-specific presentational extras — currency option lists, backend
 * tone → badge tone mapping and quota meters.
 */

export interface CurrencyOption {
    value: string;
    label: string;
}

/** Selectable currencies in the platform settings form. */
export const CURRENCY_OPTIONS: CurrencyOption[] = [
    { value: 'XAF', label: 'XAF — Central African CFA franc (FCFA)' },
    { value: 'XOF', label: 'XOF — West African CFA franc (FCFA)' },
    { value: 'EUR', label: 'EUR — Euro' },
    { value: 'USD', label: 'USD — US dollar' },
    { value: 'GBP', label: 'GBP — Pound sterling' },
    { value: 'NGN', label: 'NGN — Nigerian naira' },
    { value: 'KES', label: 'KES — Kenyan shilling' },
    { value: 'GHS', label: 'GHS — Ghanaian cedi' },
    { value: 'ZAR', label: 'ZAR — South African rand' },
    { value: 'MAD', label: 'MAD — Moroccan dirham' },
    { value: 'INR', label: 'INR — Indian rupee' },
    { value: 'CAD', label: 'CAD — Canadian dollar' },
];

/**
 * `formatCurrency(1500000, 'XAF')` → `1 500 000 FCFA`
 * The optional `symbol` overrides the trailing unit (e.g. the platform's
 * configured `currency_symbol`).
 */
export function formatCurrency(
    amount: number | string | null | undefined,
    currency?: string | null,
    symbol?: string,
): string {
    return formatSharedCurrency(amount ?? 0, currency ?? 'XAF', symbol);
}

/** Compact money for stat tiles: `1,5 M FCFA`. */
export function formatCurrencyCompact(
    amount: number | string | null | undefined,
    currency?: string | null,
    symbol?: string,
): string {
    return formatSharedCurrencyCompact(amount, currency ?? 'XAF', symbol);
}

/** `1 234` — grouped integer for counters and quotas. */
export function formatNumber(value: number | null | undefined): string {
    return formatSharedNumber(value);
}

/** `12/03/2025` — date-only values, `—` when absent. */
export function formatDate(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(date);
}

/** `12/03/2025 14:32` — timestamps. */
export function formatDateTime(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

/**
 * Backend `status_tone` values (`info`, `positive`, `warning`, `critical`,
 * `neutral`) mapped onto the Badge tone vocabulary. `info` has no badge
 * equivalent, so it reads as `accent` — never as a palette step.
 */
export function toneFromBackend(
    tone: string | null | undefined,
    fallback: BadgeTone = 'neutral',
): BadgeTone {
    switch (tone) {
        case 'positive':
        case 'success':
            return 'positive';
        case 'warning':
            return 'warning';
        case 'critical':
        case 'danger':
        case 'error':
            return 'critical';
        case 'info':
        case 'accent':
            return 'accent';
        case 'muted':
            return 'muted';
        case 'neutral':
            return 'neutral';
        default:
            return fallback;
    }
}

/** Percentage of a quota already used, clamped to 0–100 for the meter. */
export function usagePercentage(
    used: number | null | undefined,
    limit: number | null | undefined,
): number | null {
    if (used === null || used === undefined || Number.isNaN(used)) {
        return null;
    }

    if (limit === null || limit === undefined || limit <= 0) {
        return null;
    }

    return Math.max(0, Math.min(100, Math.round((used / limit) * 100)));
}

/** Meter colour for a quota: calm until it is genuinely tight. */
export function usageTone(
    percentage: number | null,
): 'accent' | 'warning' | 'critical' {
    if (percentage === null) {
        return 'accent';
    }

    if (percentage >= 95) {
        return 'critical';
    }

    if (percentage >= 80) {
        return 'warning';
    }

    return 'accent';
}

/** Short expiry label used by several panels: `12 d`, `Today`, `3 d late`. */
export function daysLabel(days: number | null | undefined): string {
    if (days === null || days === undefined) {
        return '—';
    }

    if (days < 0) {
        return `${formatNumber(Math.abs(days))} d late`;
    }

    if (days === 0) {
        return 'Today';
    }

    return `${formatNumber(days)} d`;
}

/** Human label for a plan feature key (`cash_register` → `Cash register`). */
export function humaniseKey(key: string): string {
    const spaced = key.replace(/[_-]+/g, ' ').trim();

    return spaced.charAt(0).toUpperCase() + spaced.slice(1);
}
