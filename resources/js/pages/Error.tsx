import {
    ArrowLeftIcon,
    ArrowPathIcon,
    CreditCardIcon,
    HeartIcon,
    LifebuoyIcon,
    LockClosedIcon,
    MagnifyingGlassIcon,
    ServerStackIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/Components/ui';

interface Props {
    status: number;
    /**
     * Backend-supplied overrides, sent for subscription and plan-feature
     * blocks (`EnsureSubscriptionIsActive` → 402, `EnsurePlanFeature` → 403):
     * the server knows *why* access was refused, so it says so.
     */
    title?: string;
    message?: string;
    /** The plan feature key that is missing, e.g. `radiology`. */
    feature?: string;
}

type ErrorCopy = {
    title: string;
    description: string;
    icon: typeof MagnifyingGlassIcon;
};

const COPY: Record<number, ErrorCopy> = {
    402: {
        title: 'Your subscription needs attention',
        description:
            "This hospital's plan no longer grants access to this part of SanaGo. Renew from the billing page to continue — your data is safe.",
        icon: CreditCardIcon,
    },
    403: {
        title: 'You do not have access to this page',
        description:
            'Your account is missing the permission this screen requires. Ask an administrator to review your role.',
        icon: LockClosedIcon,
    },
    404: {
        title: 'This page does not exist',
        description:
            'The link may be outdated, or the record was moved. Check the address or head back to a known screen.',
        icon: MagnifyingGlassIcon,
    },
    419: {
        title: 'Your session expired',
        description:
            'You were inactive for a while. Reload the page and sign in again to continue where you left off.',
        icon: ArrowPathIcon,
    },
    429: {
        title: 'Too many requests',
        description:
            'You have hit the rate limit for this action. Wait a moment, then try again.',
        icon: ServerStackIcon,
    },
    500: {
        title: 'Something went wrong on our side',
        description:
            'The server could not complete this request. The incident is logged — try again, and contact support if it persists.',
        icon: ServerStackIcon,
    },
    503: {
        title: 'The system is under maintenance',
        description:
            'SanaGo is briefly unavailable while we deploy an update. Your data is safe; try again in a few minutes.',
        icon: ServerStackIcon,
    },
};

const FALLBACK: ErrorCopy = {
    title: 'The request could not be completed',
    description:
        'An unexpected condition stopped this page from loading. Try again or contact support.',
    icon: ServerStackIcon,
};

/**
 * Error — the branded failure screen for 403/404/419/429/500/503. Rendered by
 * the Inertia exception handler in `bootstrap/app.php`, so users never land on
 * a bare Laravel error page.
 */
export default function Error({ status, title, message, feature }: Props) {
    const copy = COPY[status] ?? FALLBACK;

    // Backend copy wins where present: a 402 knows which plan lapsed, and a 403
    // from a plan-feature gate knows which capability was not purchased.
    const heading = title?.trim() || copy.title;
    const body = message?.trim() || copy.description;
    const Icon = copy.icon;

    // Only the money-shaped failures get a billing call to action.
    const isSubscriptionIssue = status === 402;

    return (
        <>
            <Head title={heading} />

            <div className="flex min-h-dvh flex-col bg-canvas">
                <header className="flex items-center justify-between px-5 py-4 sm:px-8">
                    <Link
                        href="/"
                        className="inline-flex items-center gap-2 text-ink"
                    >
                        <span className="flex h-7 w-7 items-center justify-center rounded-lg bg-ink text-ink-inverse">
                            <HeartIcon className="h-4 w-4" />
                        </span>
                        <span className="text-[14px] font-semibold tracking-tight">
                            SanaGo
                        </span>
                    </Link>
                    <a
                        href="/support"
                        className="inline-flex items-center gap-1.5 text-[12.5px] font-medium text-ink-muted transition-colors duration-150 hover:text-ink"
                    >
                        <LifebuoyIcon className="h-4 w-4" />
                        Support
                    </a>
                </header>

                <main className="flex flex-1 items-center justify-center px-5 pb-20 sm:px-8">
                    <div className="w-full max-w-lg">
                        <p
                            data-numeric
                            className="text-[64px] leading-none font-semibold tracking-[-0.04em] text-ink-subtle/45 tabular-nums sm:text-[80px]"
                        >
                            {status}
                        </p>

                        <div className="mt-6 rounded-xl border border-line bg-surface p-6 sm:p-7">
                            <span className="flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent-ink">
                                <Icon className="h-5 w-5" />
                            </span>
                            <h1 className="mt-4 text-[19px] leading-tight font-semibold tracking-tight text-ink">
                                {heading}
                            </h1>
                            <p className="mt-2 text-[13px] leading-relaxed text-ink-muted">
                                {body}
                            </p>

                            {feature && (
                                <p className="mt-3 inline-flex items-center gap-1.5 rounded-full bg-surface-inset px-2.5 py-1 font-mono text-[11px] text-ink-subtle">
                                    <LockClosedIcon className="h-3 w-3" />
                                    {feature}
                                </p>
                            )}

                            <div className="mt-6 flex flex-wrap items-center gap-2 border-t border-line pt-5">
                                {isSubscriptionIssue ? (
                                    <Button href="/billing" size="sm">
                                        Review billing
                                    </Button>
                                ) : (
                                    <Button href="/" size="sm">
                                        Go to dashboard
                                    </Button>
                                )}
                                <Button
                                    variant="secondary"
                                    size="sm"
                                    icon={ArrowLeftIcon}
                                    onClick={() => window.history.back()}
                                >
                                    Go back
                                </Button>
                                {isSubscriptionIssue && (
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        href="/support"
                                        icon={LifebuoyIcon}
                                    >
                                        Contact support
                                    </Button>
                                )}
                            </div>
                        </div>

                        <p className="mt-4 text-center text-[11.5px] text-ink-subtle">
                            If this keeps happening, include the code{' '}
                            <span className="font-mono text-ink-muted">
                                ERR-{status}
                            </span>{' '}
                            when you contact support.
                        </p>
                    </div>
                </main>
            </div>
        </>
    );
}
