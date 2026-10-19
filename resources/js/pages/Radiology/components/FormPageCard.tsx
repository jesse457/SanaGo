import { CheckCircleIcon, XMarkIcon } from '@heroicons/react/24/outline';
import type { ComponentType, FormEvent, ReactNode, SVGProps } from 'react';
import {
    Alert,
    Button,
    Card,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/Components/ui';

/**
 * FormPageCard — the single-panel wrapper shared by the create/edit forms in
 * this module: a titled surface, a body and a right-aligned footer action row.
 *
 * Replaces the per-page `rounded-xl border border-line bg-surface` blocks with
 * their own `<h3>` headings and inline `<button>` rows.
 */
export function FormPageCard({
    title,
    description,
    headingIcon: HeadingIcon,
    onSubmit,
    children,
    footer,
}: {
    title: string;
    description?: string;
    headingIcon?: ComponentType<SVGProps<SVGSVGElement>>;
    onSubmit: (event: FormEvent) => void;
    children: ReactNode;
    footer: ReactNode;
}) {
    return (
        <Card className="overflow-hidden">
            <form onSubmit={onSubmit}>
                <CardHeader>
                    <div className="flex items-center gap-3">
                        {HeadingIcon && (
                            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                                <HeadingIcon className="h-[18px] w-[18px]" />
                            </span>
                        )}
                        <div className="min-w-0">
                            <CardTitle>{title}</CardTitle>
                            {description && (
                                <p className="mt-1 text-[13px] leading-relaxed text-ink-muted">
                                    {description}
                                </p>
                            )}
                        </div>
                    </div>
                </CardHeader>

                <div className="space-y-6 px-5 py-5 sm:px-6">{children}</div>

                <CardFooter>{footer}</CardFooter>
            </form>
        </Card>
    );
}

/**
 * FormStatusAlerts — the success / validation pair shown above every form.
 * Renders nothing when there is nothing to say.
 */
export function FormStatusAlerts({
    successMessage,
    onDismissSuccess,
    errorMessage,
    hasFieldErrors,
    successTitle,
    errorTitle,
    errorFallback,
}: {
    successMessage: string | null;
    onDismissSuccess: () => void;
    errorMessage: string | null;
    hasFieldErrors: boolean;
    successTitle: string;
    errorTitle: string;
    errorFallback: string;
}) {
    return (
        <>
            {successMessage && (
                <Alert
                    tone="positive"
                    title={successTitle}
                    action={
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label={successTitle}
                            icon={XMarkIcon}
                            onClick={onDismissSuccess}
                        />
                    }
                >
                    {successMessage}
                </Alert>
            )}

            {(errorMessage || hasFieldErrors) && (
                <Alert tone="critical" title={errorTitle}>
                    {errorMessage || errorFallback}
                </Alert>
            )}
        </>
    );
}

/** Status icon kept next to the alert helpers so pages import one module. */
export { CheckCircleIcon };
