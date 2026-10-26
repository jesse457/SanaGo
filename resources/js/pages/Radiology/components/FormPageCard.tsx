import { CheckCircleIcon } from '@heroicons/react/24/outline';
import type { ComponentType, FormEvent, ReactNode, SVGProps } from 'react';
import { Card, CardFooter, CardHeader, CardTitle } from '@/Components/ui';

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

/** Status icon re-exported so form pages import one module. */
export { CheckCircleIcon };
