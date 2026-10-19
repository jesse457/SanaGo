import { Toaster } from 'sonner';

/**
 * AppToaster — the single toast renderer for the app.
 *
 * Mounted once by the layout shells. Pages should only call `toast(...)` from
 * `sonner`; they must not render their own `<Toaster />`, otherwise toasts get
 * duplicated styling and positions.
 */
export function AppToaster() {
    return (
        <Toaster
            position="bottom-right"
            closeButton
            duration={4200}
            gap={10}
            offset={18}
            toastOptions={{
                unstyled: true,
                classNames: {
                    toast: 'group flex w-full items-start gap-2.5 rounded-lg border border-line bg-surface px-3.5 py-3 text-[13px] text-ink shadow-float',
                    title: 'font-semibold tracking-tight text-ink',
                    description:
                        'mt-0.5 text-[12.5px] leading-relaxed text-ink-muted',
                    icon: 'mt-0.5 shrink-0',
                    closeButton:
                        'absolute top-1.5 right-1.5 rounded p-1 text-ink-subtle opacity-0 transition-opacity duration-150 group-hover:opacity-100 hover:text-ink',
                    actionButton:
                        'rounded-md border border-line-strong bg-surface px-2 py-1 text-[11.5px] font-semibold text-ink',
                    cancelButton:
                        'rounded-md px-2 py-1 text-[11.5px] font-medium text-ink-muted',
                    success: 'border-positive/30',
                    error: 'border-critical/30',
                    warning: 'border-warning/30',
                    info: 'border-accent/25',
                },
            }}
        />
    );
}
