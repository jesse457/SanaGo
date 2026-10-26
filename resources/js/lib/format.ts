/**
 * Formatting helpers for money and dates.
 *
 * The app is deployed in Cameroon (XAF / FCFA) with French and English copy, so
 * both locales share the same money grammar: no decimals, thousands separated
 * by a (narrow no-break) space, and the currency label trailing the number.
 *
 *   1500000  ->  "1 500 000 FCFA"
 *   -25000   ->  "-25 000 FCFA"
 *
 * `Intl.NumberFormat` handles the grouping, then we normalise its output so the
 * separator is the French typographic space (U+202F) rather than a comma.
 */

/** Narrow no-break space — the French thousands separator. */
const NNBSP = '\u202F';

/**
 * `Intl` formatters are comparatively expensive to construct, and these helpers
 * run for every table row and stat card. Reuse one instance per locale/options
 * combination instead of allocating a new one on every call.
 */
const numberFormats = new Map<string, Intl.NumberFormat>();
const dateFormats = new Map<string, Intl.DateTimeFormat>();
const relativeFormats = new Map<string, Intl.RelativeTimeFormat>();

function numberFormat(
    locale: string,
    options: Intl.NumberFormatOptions,
): Intl.NumberFormat {
    const key = `${locale}|${JSON.stringify(options)}`;
    let formatter = numberFormats.get(key);

    if (!formatter) {
        formatter = new Intl.NumberFormat(locale, options);
        numberFormats.set(key, formatter);
    }

    return formatter;
}

function dateFormat(
    locale: string,
    options: Intl.DateTimeFormatOptions,
): Intl.DateTimeFormat {
    const key = `${locale}|${JSON.stringify(options)}`;
    let formatter = dateFormats.get(key);

    if (!formatter) {
        formatter = new Intl.DateTimeFormat(locale, options);
        dateFormats.set(key, formatter);
    }

    return formatter;
}

function relativeFormat(locale: string): Intl.RelativeTimeFormat {
    let formatter = relativeFormats.get(locale);

    if (!formatter) {
        formatter = new Intl.RelativeTimeFormat(locale, { numeric: 'auto' });
        relativeFormats.set(locale, formatter);
    }

    return formatter;
}

/** Currency labels that render as a trailing unit instead of a symbol. */
const SUFFIX_CURRENCIES: Record<string, string> = {
    XAF: 'FCFA',
    XOF: 'FCFA',
    CFA: 'FCFA',
};

const PREFIX_SYMBOLS: Record<string, string> = {
    USD: '$',
    EUR: '€',
    GBP: '£',
    CAD: '$',
    NGN: '₦',
    GHS: 'GH₵',
    KES: 'KSh',
};

/**
 * Formats a monetary amount with tabular-friendly grouping and no decimals.
 *
 * @param value    Amount in major units (not cents).
 * @param currency ISO code, defaulting to XAF.
 * @param symbol   Explicit unit label, overriding the currency's default.
 */
export function formatCurrency(
    value: number | string | null | undefined,
    currency = 'XAF',
    symbol?: string,
): string {
    const amount = typeof value === 'string' ? Number(value) : value;

    if (amount === null || amount === undefined || Number.isNaN(amount)) {
        return '—';
    }

    const code = (currency || 'XAF').toUpperCase();
    const hasFraction = !Number.isInteger(amount);
    const grouped = numberFormat('fr-FR', {
        minimumFractionDigits: hasFraction ? 2 : 0,
        maximumFractionDigits: hasFraction ? 2 : 0,
        useGrouping: true,
    })
        .format(amount)
        // Normalise the (non-breaking) group separator to the narrow NBSP.
        // The decimal comma must survive, so only spaces are rewritten.
        .replace(/[\s\u00A0\u202F]/g, NNBSP);

    const unit = symbol ?? SUFFIX_CURRENCIES[code];

    if (unit) {
        return `${grouped}${NNBSP}${unit}`;
    }

    const prefix = PREFIX_SYMBOLS[code];

    return prefix ? `${prefix}${grouped}` : `${grouped}${NNBSP}${code}`;
}

/**
 * Compact money for tight spaces (stat cards, chips): `1,5 M FCFA`.
 * Falls back to the full format below 10 000.
 */
export function formatCurrencyCompact(
    value: number | string | null | undefined,
    currency = 'XAF',
    symbol?: string,
): string {
    const amount = typeof value === 'string' ? Number(value) : value;

    if (amount === null || amount === undefined || Number.isNaN(amount)) {
        return '—';
    }

    const unit =
        symbol ??
        SUFFIX_CURRENCIES[(currency || 'XAF').toUpperCase()] ??
        (currency || 'XAF').toUpperCase();

    if (Math.abs(amount) < 10_000) {
        return formatCurrency(amount, currency, symbol);
    }

    const scaled =
        Math.abs(amount) >= 1_000_000
            ? { value: amount / 1_000_000, suffix: 'M' }
            : { value: amount / 1_000, suffix: 'k' };

    const rounded =
        Math.abs(scaled.value) >= 100
            ? Math.round(scaled.value).toString()
            : scaled.value.toFixed(1).replace('.0', '').replace('.', ',');

    return `${rounded}${NNBSP}${scaled.suffix}${NNBSP}${unit}`;
}

function toDate(value: Date | string | number | null | undefined): Date | null {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const date = value instanceof Date ? value : new Date(value);

    return Number.isNaN(date.getTime()) ? null : date;
}

const DATE_PRESETS: Record<
    'short' | 'long' | 'numeric',
    Intl.DateTimeFormatOptions
> = {
    numeric: { day: '2-digit', month: '2-digit', year: 'numeric' },
    short: { day: 'numeric', month: 'short', year: 'numeric' },
    long: {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    },
};

const DATE_TIME_OPTIONS: Intl.DateTimeFormatOptions = {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
};

/**
 * Absolute, human date. `short` gives `14 mars 2026` style output; `long` adds
 * the weekday. Locale defaults to French because that is the primary language
 * for the deployment, but any BCP-47 tag works.
 */
export function formatDate(
    value: Date | string | number | null | undefined,
    options: { locale?: string; style?: 'short' | 'long' | 'numeric' } = {},
): string {
    const date = toDate(value);

    if (!date) {
        // Already a display string (e.g. a pre-formatted API date): pass it
        // through rather than swallowing it as an em dash.
        return typeof value === 'string' && value.trim() !== '' ? value : '—';
    }

    const { locale = 'fr-FR', style = 'short' } = options;

    return dateFormat(locale, DATE_PRESETS[style]).format(date);
}

/** Date + time, e.g. `14 mars 2026, 09:30`. */
export function formatDateTime(
    value: Date | string | number | null | undefined,
    options: { locale?: string } = {},
): string {
    const date = toDate(value);

    if (!date) {
        return '—';
    }

    return dateFormat(options.locale ?? 'fr-FR', DATE_TIME_OPTIONS).format(
        date,
    );
}

const RELATIVE_UNITS: [Intl.RelativeTimeFormatUnit, number][] = [
    ['year', 1000 * 60 * 60 * 24 * 365],
    ['month', 1000 * 60 * 60 * 24 * 30],
    ['week', 1000 * 60 * 60 * 24 * 7],
    ['day', 1000 * 60 * 60 * 24],
    ['hour', 1000 * 60 * 60],
    ['minute', 1000 * 60],
    ['second', 1000],
];

/**
 * Relative time for notification rows and activity feeds: `à l'instant`,
 * `il y a 5 min`, `hier`. Falls back to an absolute date beyond a month so the
 * copy never says "il y a 4 mois" where a date is more useful.
 */
export function formatDateRelative(
    value: Date | string | number | null | undefined,
    options: { locale?: string } = {},
): string {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const date = toDate(value);

    if (!date) {
        // The API sometimes hands us an already-humanised string from PHP's
        // `diffForHumans()` (e.g. "3 months ago"). It cannot be parsed as a
        // date, but it is already exactly the copy we want to show.
        return typeof value === 'string' ? value : '—';
    }

    const locale = options.locale ?? 'fr-FR';
    const diffMs = date.getTime() - Date.now();
    const absMs = Math.abs(diffMs);
    const rtf = relativeFormat(locale);

    if (absMs < 45_000) {
        return locale.startsWith('fr') ? "à l'instant" : 'just now';
    }

    if (absMs > 1000 * 60 * 60 * 24 * 30) {
        return formatDate(date, { locale, style: 'short' });
    }

    for (const [unit, ms] of RELATIVE_UNITS) {
        if (absMs >= ms || unit === 'second') {
            return rtf.format(Math.round(diffMs / ms), unit);
        }
    }

    return formatDate(date, { locale, style: 'short' });
}

/**
 * Short duration label used by subscription chips: `24 days left`,
 * `24 jours restants`.
 */
export function formatDaysRemaining(
    days: number | null | undefined,
    locale = 'en',
): string {
    if (days === null || days === undefined || Number.isNaN(days)) {
        return '';
    }

    const isFrench = locale.startsWith('fr');

    if (days <= 0) {
        return isFrench ? 'terminé' : 'ended';
    }

    if (isFrench) {
        return `${days} jour${days > 1 ? 's' : ''} restant${days > 1 ? 's' : ''}`;
    }

    return `${days} day${days > 1 ? 's' : ''} left`;
}

/** Plain number with narrow-space grouping — counts, dosages, percentages. */
export function formatNumber(
    value: number | string | null | undefined,
    locale = 'fr-FR',
): string {
    const amount = typeof value === 'string' ? Number(value) : value;

    if (amount === null || amount === undefined || Number.isNaN(amount)) {
        return '—';
    }

    return numberFormat(locale, {})
        .format(amount)
        .replace(/[\s\u00A0\u202F]/g, NNBSP);
}
