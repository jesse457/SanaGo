import { usePage } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { swal } from '@/Components/ui/sweetAlert';
import { useTranslation } from '@/hooks/useTranslation';

interface FlashPayload {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

/**
 * NotificationHandler — the single place server feedback becomes visible.
 *
 * Mounted once per layout shell. It owns both channels:
 *
 * - **Animated SweetAlert toasts** for quiet, non-blocking confirmation: flash
 *   success, info and warning. Pages call `swal.toast()` for client-side events.
 * - **The SweetAlert modal** for anything that must not be missed: a flash
 *   error, and any validation failure. Inertia keeps the 422 bag in
 *   `props.errors`, so this fires for *every* form in the app — including the
 *   ones that never rendered an inline error themselves.
 *
 * Repeats are suppressed by identity: the same flash object, or the same error
 * bag, never raises a second modal on a re-render.
 */
export function NotificationHandler() {
    const { flash, errors } = usePage().props as {
        flash?: FlashPayload;
        errors?: Record<string, string>;
    };
    const { t } = useTranslation();

    const lastFlash = useRef<FlashPayload | undefined>(undefined);
    const lastErrors = useRef<string>('');

    useEffect(() => {
        if (!flash || lastFlash.current === flash) {
            return;
        }

        lastFlash.current = flash;

        if (flash.success) {
            swal.toast(flash.success, 'success');
        }

        if (flash.info) {
            swal.toast(flash.info, 'info');
        }

        if (flash.warning) {
            swal.toast(flash.warning, 'warning');
        }

        if (flash.error) {
            void swal.error(
                t('common.error', 'Something went wrong'),
                flash.error,
            );
        }
    }, [flash, t]);

    useEffect(() => {
        const hasErrors = Object.keys(errors ?? {}).length > 0;
        const signature = hasErrors ? JSON.stringify(errors) : '';

        if (signature === lastErrors.current) {
            return;
        }

        // Clearing the bag arms the handler again for the next submit.
        lastErrors.current = signature;

        if (!signature) {
            return;
        }

        void swal.validation(
            errors ?? null,
            t(
                'common.someFieldsRequireAttention',
                'Some fields need attention',
            ),
        );
    }, [errors, t]);

    return null;
}
