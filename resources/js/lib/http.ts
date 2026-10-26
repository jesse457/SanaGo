/**
 * Small JSON client for the app's non-Inertia endpoints.
 *
 * Inertia covers page visits and form posts; a handful of calls (notification
 * counters, analyzer previews) are plain JSON, and they all need the same
 * three things: same-origin credentials, the XSRF cookie Laravel sets, and the
 * `XMLHttpRequest` header Laravel's `expectsJson()` looks for.
 */

/** The XSRF cookie name Laravel sets, used outside Inertia's own client. */
export function xsrfToken(): string {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

export function fetchJson(
    url: string,
    init: RequestInit = {},
): Promise<Response> {
    return fetch(url, {
        credentials: 'same-origin',
        ...init,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': xsrfToken(),
            ...(init.headers ?? {}),
        },
    });
}

/**
 * POST JSON and return the parsed body, or throw with the server's message.
 *
 * Errors are shaped so a caller can hand them straight to `swal.error` without
 * unwrapping a `422 {message, errors}` envelope per call site.
 */
export async function postJson<T>(url: string, body: unknown = {}): Promise<T> {
    const response = await fetchJson(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });

    const payload = await response.json().catch(() => null);

    if (!response.ok) {
        const message =
            (payload as { message?: string } | null)?.message ??
            `The request failed (${response.status}).`;

        throw new Error(message);
    }

    return payload as T;
}
