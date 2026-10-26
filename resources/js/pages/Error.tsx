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
import {
    Badge,
    Button,
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui';
import { cn } from '@/lib/utils';

/**
 * What the server attaches to this page when `app.debug` is on — see
 * `App\Support\ErrorReport`. Absent in production.
 */
interface DebugDetails {
    exception: string;
    message: string;
    location: string | null;
    previous: string | null;
    request: string | null;
    trace: Array<{ call: string; location: string | null }>;
}

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
    /** Technical detail of the failure. Local builds only. */
    debug?: DebugDetails | null;
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
export default function Error({
    status,
    title,
    message,
    feature,
    debug,
}: Props) {
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
                    {/* The debug panel needs room for stack frames. */}
                    <div
                        className={cn(
                            'w-full',
                            debug ? 'max-w-3xl' : 'max-w-lg',
                        )}
                    >
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

                        {debug && <DebugPanel details={debug} />}
                    </div>
                </main>
            </div>
        </>
    );
}

/**
 * The failure itself, for whoever is building this: what threw, where from and
 * what the request was. Only ever rendered when the backend sends `debug`,
 * which it does for `APP_DEBUG=true` builds alone — a production visitor must
 * never read our file paths.
 */
function DebugPanel({ details }: { details: DebugDetails }) {
    const { exception, message, location, previous, request, trace } = details;

    return (
        <Card className="mt-6">
            <CardHeader
                actions={
                    <Badge tone="warning" size="sm">
                        Debug
                    </Badge>
                }
            >
                <CardTitle>Why this failed</CardTitle>
                <CardDescription>
                    Shown because <code className="font-mono">APP_DEBUG</code>{' '}
                    is on. Turn it off before this build reaches a real
                    hospital.
                </CardDescription>
            </CardHeader>

            <CardContent className="space-y-4">
                <div>
                    <p className="font-mono text-[11.5px] break-words text-ink">
                        {exception}
                    </p>
                    <p className="mt-1.5 font-mono text-[12px] leading-relaxed break-words whitespace-pre-wrap text-critical-ink">
                        {message}
                    </p>
                    {previous && (
                        <p className="mt-2 font-mono text-[11px] leading-relaxed break-words whitespace-pre-wrap text-ink-subtle">
                            {previous}
                        </p>
                    )}
                </div>

                <dl className="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1.5 border-t border-line pt-3 text-[11.5px]">
                    {location && (
                        <>
                            <dt className="text-ink-subtle">Thrown at</dt>
                            <dd className="font-mono break-all text-ink-muted">
                                {location}
                            </dd>
                        </>
                    )}
                    {request && (
                        <>
                            <dt className="text-ink-subtle">Request</dt>
                            <dd className="font-mono break-all text-ink-muted">
                                {request}
                            </dd>
                        </>
                    )}
                </dl>

                {trace.length > 0 && (
                    <div className="border-t border-line pt-3">
                        <p className="text-[11px] font-medium tracking-wide text-ink-subtle uppercase">
                            Stack trace
                        </p>
                        <ol className="mt-2 space-y-1">
                            {trace.map((frame, index) => (
                                <li
                                    key={`${index}-${frame.call}`}
                                    className="flex gap-2 font-mono text-[11px] leading-relaxed"
                                >
                                    <span className="w-4 shrink-0 text-right text-ink-subtle/70 tabular-nums">
                                        {index + 1}
                                    </span>
                                    <span className="min-w-0 break-all text-ink-muted">
                                        {frame.call}
                                        {frame.location && (
                                            <span className="text-ink-subtle">
                                                {' — '}
                                                {frame.location}
                                            </span>
                                        )}
                                    </span>
                                </li>
                            ))}
                        </ol>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
