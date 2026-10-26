import Swal from 'sweetalert2';
import type { SweetAlertOptions as SweetAlert2Options } from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

/**
 * swal — SanaGo's SweetAlert.
 *
 * The one notification and confirmation surface in the app. `sweetalert2` is
 * the engine (see the `SweetAlert2` block in `resources/css/app.css`, which
 * re-binds its palette to the SanaGo tokens so the modal and its toasts flip
 * with light/dark exactly like every other surface).
 *
 * Any module — a page, a dialog, a hook, a plain `.ts` file — can call these:
 *
 *   if (await swal.confirm({ title: 'Delete this ward?', danger: true })) { … }
 *   swal.toast('Ward deleted');
 *   swal.validation(errors);
 *
 * Never use `window.confirm` / `window.alert` / `window.prompt`, and never
 * render a page-local "Success" banner: the server flash already lands here.
 */

export type SweetAlertTone = 'info' | 'success' | 'warning' | 'critical';

export interface SweetAlertOptions {
    title: string;
    /** Plain text under the title. */
    message?: string;
    /** Bulleted lines under the message — the validation-error case. */
    items?: string[];
    tone?: SweetAlertTone;
    confirmLabel?: string;
    /** Supplying a label renders the quiet Cancel and makes this a question. */
    cancelLabel?: string;
    /** Paints Confirm in the critical tone. Use for destructive actions. */
    danger?: boolean;
    /** Auto-dismiss after this many ms. Alerts only; never for questions. */
    timeout?: number;
    /** Extra class on the popup, for one-off sizing. */
    className?: string;
}

const MAX_LISTED_ITEMS = 5;

const TONE_ICON: Record<
    SweetAlertTone,
    NonNullable<SweetAlert2Options['icon']>
> = {
    info: 'info',
    success: 'success',
    warning: 'warning',
    critical: 'error',
};

/** Minimal HTML escaping: field messages are user-facing text, not markup. */
function escapeHtml(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function buildHtml(options: SweetAlertOptions): string | undefined {
    const items = options.items ?? [];
    const listed = items.slice(0, MAX_LISTED_ITEMS);
    const overflow = items.length - listed.length;

    if (listed.length === 0) {
        return options.message;
    }

    const head = options.message
        ? `<p style="margin:0 0 8px">${escapeHtml(options.message)}</p>`
        : '';

    const rows = listed.map((item) => `<li>${escapeHtml(item)}</li>`).join('');

    const more =
        overflow > 0
            ? `<li class="sg-swal-more">and ${overflow} more</li>`
            : '';

    return `${head}<ul class="sg-swal-list">${rows}${more}</ul>`;
}

const POPUP_CLASS = 'sg-swal';

/**
 * One SweetAlert2 call, themed. `danger` repaints the confirm button and
 * switches the tone to warning unless the caller chose one.
 */
function fire(options: SweetAlertOptions): Promise<boolean> {
    const tone = options.tone ?? 'info';
    const isQuestion = Boolean(options.cancelLabel);

    return Swal.fire({
        title: options.title,
        html: buildHtml(options),
        icon: TONE_ICON[tone],
        confirmButtonText:
            options.confirmLabel ?? (isQuestion ? 'Continue' : 'Got it'),
        showCancelButton: isQuestion,
        cancelButtonText: options.cancelLabel,
        reverseButtons: true,
        /* A destructive question lands focus on Cancel, not on the red button. */
        focusCancel: isQuestion && Boolean(options.danger),
        timer: options.timeout,
        timerProgressBar: Boolean(options.timeout),
        customClass: {
            container: 'sg-swal-container',
            popup: `${POPUP_CLASS}${options.danger ? ' sg-swal-danger' : ''}${
                options.className ? ` ${options.className}` : ''
            }`,
            title: 'sg-swal-title',
            htmlContainer: 'sg-swal-body',
            confirmButton: 'sg-swal-confirm',
            cancelButton: 'sg-swal-cancel',
            actions: 'sg-swal-actions',
        },
    }).then((result) => result.isConfirmed);
}

/* ── Toast: the quiet channel ───────────────────────────────────────────── */

const TONE_ICON_COLOR = {
    success: 'var(--sg-positive)',
    warning: 'var(--sg-warning)',
    info: 'var(--sg-accent)',
} as const;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3400,
    timerProgressBar: true,
    customClass: {
        container: 'sg-swal-container',
        popup: `${POPUP_CLASS} sg-swal-toast`,
        title: 'sg-swal-toast-title',
        htmlContainer: 'sg-swal-toast-body',
    },
    didOpen: (element) => {
        // Pause while the pointer rests on it, so a message is never missed.
        element.addEventListener('mouseenter', Swal.stopTimer);
        element.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

export const swal = {
    /** Raise an alert; resolves `true` on confirm, `false` on cancel/dismiss. */
    fire,

    /**
     * Yes/no question. Resolves `false` unless the user explicitly confirms,
     * so `if (await swal.confirm(...))` is always safe to gate destructive work.
     */
    confirm(
        options: Omit<SweetAlertOptions, 'cancelLabel' | 'tone'> & {
            cancelLabel?: string;
            tone?: SweetAlertTone;
        },
    ): Promise<boolean> {
        return fire({
            ...options,
            tone: options.tone ?? (options.danger ? 'warning' : 'info'),
            cancelLabel: options.cancelLabel ?? 'Cancel',
        });
    },

    /**
     * Animated toast — the default for a successful save. Non-blocking, and
     * the only success channel, so nothing stacks.
     */
    toast(
        message: string,
        tone: 'success' | 'info' | 'warning' = 'success',
        detail?: string,
    ): void {
        void Toast.fire({
            icon: tone,
            title: message,
            html: detail
                ? `<span class="sg-swal-toast-detail">${escapeHtml(detail)}</span>`
                : undefined,
            iconColor: TONE_ICON_COLOR[tone],
        });
    },

    /** A success worth stopping for: the full modal instead of a toast. */
    success(title: string, message?: string): Promise<boolean> {
        return fire({ title, message, tone: 'success' });
    },

    error(title: string, message?: string): Promise<boolean> {
        return fire({ title, message, tone: 'critical' });
    },

    warning(title: string, message?: string): Promise<boolean> {
        return fire({ title, message, tone: 'warning' });
    },

    info(title: string, message?: string): Promise<boolean> {
        return fire({ title, message, tone: 'info' });
    },

    /**
     * Lists field validation messages in one modal. Accepts the raw Inertia
     * `errors` record or a plain array of sentences.
     */
    validation(
        errors: Record<string, string | undefined> | readonly string[] | null,
        title = 'Some fields need attention',
    ): Promise<boolean> {
        const items = Array.isArray(errors)
            ? [...errors]
            : Object.values(errors ?? {}).filter(
                  (value): value is string =>
                      typeof value === 'string' && value.length > 0,
              );

        return fire({ title, items, tone: 'warning' });
    },

    isOpen(): boolean {
        return Swal.isVisible();
    },

    close(): void {
        Swal.close();
    },
};

export { escapeHtml };
