import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { SanaGoMark, SanaGoWordmark } from '@/Components/brand/SanaGoLogo';
import { Card } from '@/Components/ui';
import { cn } from '@/lib/utils';

interface AuthShellProps {
    title: string;
    description?: ReactNode;
    children: ReactNode;
    /** Existing breadcrumb-style link back up the flow (e.g. "Back to login"). */
    backLink?: { label: string; href: string };
    /** Sentence case footnote under the card (support links, alternate routes). */
    footer?: ReactNode;
    className?: string;
}

/** The product lockup. One glyph, one word — no stock imagery on this screen. */
function BrandMark() {
    return (
        <Link
            href="/"
            className="group inline-flex items-center gap-2.5 rounded-md focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-4 focus-visible:ring-offset-canvas focus-visible:outline-none"
        >
            <SanaGoMark className="h-9 w-auto shrink-0 transition-transform duration-200 ease-premium group-hover:-translate-y-px" />
            <SanaGoWordmark size="lg" />
        </Link>
    );
}

/**
 * AuthShell — the calm, centred canvas every unauthenticated screen sits on:
 * brand mark, one heading, the form card, and quiet support links. Shared by
 * login, forgot password and password reset so the front door reads as one
 * product.
 */
export function AuthShell({
    title,
    description,
    children,
    backLink,
    footer,
    className,
}: AuthShellProps) {
    return (
        <div className="flex min-h-screen flex-col bg-canvas">
            <header className="flex items-center justify-between px-6 py-6 sm:px-10">
                <BrandMark />
                {backLink && (
                    <Link
                        href={backLink.href}
                        className="text-[12.5px] font-medium text-ink-subtle transition-colors duration-200 ease-premium hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas focus-visible:outline-none"
                    >
                        {backLink.label}
                    </Link>
                )}
            </header>

            <main className="flex flex-1 items-start justify-center px-4 pt-6 pb-16 sm:items-center sm:pt-0">
                <div className={cn('w-full max-w-[26rem]', className)}>
                    <div className="mb-6">
                        <h1 className="text-[22px] leading-tight font-semibold tracking-[-0.02em] text-ink">
                            {title}
                        </h1>
                        {description && (
                            <p className="mt-1.5 text-[13px] leading-relaxed text-ink-muted">
                                {description}
                            </p>
                        )}
                    </div>

                    <Card className="p-5 shadow-surface sm:p-6">
                        {children}
                    </Card>

                    {footer && (
                        <div className="mt-5 text-center text-[12.5px] leading-relaxed text-ink-subtle">
                            {footer}
                        </div>
                    )}
                </div>
            </main>

            <footer className="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 px-6 py-6 text-[11px] font-medium tracking-[0.08em] text-ink-subtle uppercase">
                <Link
                    href="/support"
                    className="transition-colors duration-150 hover:text-ink-muted"
                >
                    Support
                </Link>
                <Link
                    href="/privacy"
                    className="transition-colors duration-150 hover:text-ink-muted"
                >
                    Privacy
                </Link>
                <Link
                    href="/security"
                    className="transition-colors duration-150 hover:text-ink-muted"
                >
                    Security
                </Link>
            </footer>
        </div>
    );
}
