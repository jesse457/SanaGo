/** Currency label used across the finance module. */
export const CURRENCY = 'FCFA';

/**
 * Money formatter shared by every finance surface: grouped thousands, no
 * decimals, always suffixed with the currency label. Kept out of components so
 * tables, cards and print views never disagree on formatting.
 */
export function formatMoney(value: number | string | null | undefined): string {
    const amount = Number(value ?? 0);

    if (!Number.isFinite(amount)) {
        return `0 ${CURRENCY}`;
    }

    return `${amount.toLocaleString()} ${CURRENCY}`;
}

/** Signed amount, used for profit/loss lines. */
export function formatSignedMoney(
    value: number | string | null | undefined,
): string {
    const amount = Number(value ?? 0);

    if (!Number.isFinite(amount)) {
        return `0 ${CURRENCY}`;
    }

    return `${amount >= 0 ? '+' : ''}${amount.toLocaleString()} ${CURRENCY}`;
}

export function formatDate(value: string | null | undefined): string {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleDateString();
}

/** Turns `snake_case` enum values into `Sentence case` labels. */
export function humanize(value: string | null | undefined): string {
    if (!value) {
        return '—';
    }

    const spaced = value.replace(/_/g, ' ');

    return spaced.charAt(0).toUpperCase() + spaced.slice(1);
}
