import {
    ArchiveBoxIcon,
    ArrowPathRoundedSquareIcon,
    GlobeAltIcon,
    PauseCircleIcon,
    PencilSquareIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { router } from '@inertiajs/react';
import { useRef, useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Dialog,
    Field,
    PageHeader,
    Textarea,
} from '@/Components/ui';
import { cn } from '@/lib/utils';
import {
    formatDate,
    toneFromBackend,
} from '@/pages/Platform/components/format';
import type { TenantDetail } from '@/pages/Platform/components/types';

interface TenantHeaderProps {
    tenant: TenantDetail;
    canManage: boolean;
    canImpersonate: boolean;
}

function csrfToken(): string {
    if (typeof document === 'undefined') {
        return '';
    }

    return (
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? ''
    );
}

type LifecycleDialog = 'suspend' | 'archive' | 'impersonate' | null;

/**
 * Tenant header — identity, lifecycle state, plan chip and every lifecycle
 * action. Suspend/archive/impersonate all confirm through `Dialog`; nothing
 * here uses `window.confirm`.
 */
export function TenantHeader({
    tenant,
    canManage,
    canImpersonate,
}: TenantHeaderProps) {
    const [dialog, setDialog] = useState<LifecycleDialog>(null);
    const [reason, setReason] = useState('');
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const impersonateFormRef = useRef<HTMLFormElement>(null);
    const [copied, setCopied] = useState(false);

    const archived = Boolean(tenant.archived_at);
    const suspended = Boolean(tenant.suspended_at) || !tenant.is_active;

    const state = archived
        ? { label: 'Archived', tone: 'muted' as const }
        : suspended
          ? { label: 'Suspended', tone: 'warning' as const }
          : { label: 'Active', tone: 'positive' as const };

    const primaryDomain = tenant.domains[0] ?? null;

    const openDialog = (kind: LifecycleDialog) => {
        setReason('');
        setError(null);
        setDialog(kind);
    };

    const closeDialog = () => {
        if (!processing) {
            setDialog(null);
            setReason('');
            setError(null);
        }
    };

    const post = (url: string, payload: Record<string, string> = {}) => {
        setProcessing(true);
        setError(null);

        router.post(url, payload, {
            preserveScroll: true,
            onSuccess: () => {
                setDialog(null);
                setReason('');
            },
            onError: (errors) =>
                setError(
                    Object.values(errors)[0] ??
                        'The action could not complete.',
                ),
            onFinish: () => setProcessing(false),
        });
    };

    const copyDomain = async () => {
        if (!primaryDomain) {
            return;
        }

        try {
            await navigator.clipboard.writeText(primaryDomain);
            setCopied(true);
            setTimeout(() => setCopied(false), 1800);
        } catch {
            setCopied(false);
        }
    };

    return (
        <>
            <PageHeader
                eyebrow="Hospital"
                title={tenant.name}
                breadcrumbs={[
                    { label: 'Hospitals', href: '/tenants' },
                    { label: tenant.name },
                ]}
                description={
                    tenant.archived_reason
                        ? `Archived: ${tenant.archived_reason}`
                        : undefined
                }
                meta={
                    <>
                        <Badge tone={state.tone} dot>
                            {state.label}
                        </Badge>

                        {tenant.subscription ? (
                            <span className="inline-flex items-center gap-1.5">
                                <Badge
                                    tone={toneFromBackend(
                                        tenant.subscription.status_tone,
                                    )}
                                >
                                    {tenant.subscription.plan_name}
                                </Badge>
                                <span className="text-[12px] text-ink-subtle">
                                    {tenant.subscription.billing_cycle}
                                </span>
                            </span>
                        ) : (
                            <Badge tone="muted">No plan</Badge>
                        )}

                        <span className="font-mono text-[11.5px]">
                            {tenant.slug}
                        </span>

                        <span className="inline-flex items-center gap-1.5">
                            <GlobeAltIcon className="h-3.5 w-3.5" />
                            {primaryDomain ? (
                                <button
                                    type="button"
                                    onClick={copyDomain}
                                    className="rounded font-mono text-[11.5px] transition-colors duration-150 hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                    title="Copy domain"
                                >
                                    {copied ? 'copied' : primaryDomain}
                                </button>
                            ) : (
                                <span className="text-ink-subtle">
                                    No domain configured
                                </span>
                            )}
                        </span>

                        <span className="tabular-nums">
                            Created {formatDate(tenant.created_at)}
                        </span>
                    </>
                }
                actions={
                    <>
                        <Button
                            variant="secondary"
                            size="sm"
                            icon={PencilSquareIcon}
                            href={`/tenants/${tenant.id}/edit`}
                        >
                            Edit
                        </Button>

                        {canImpersonate && !archived && (
                            <Button
                                variant="secondary"
                                size="sm"
                                icon={UserCircleIcon}
                                onClick={() => openDialog('impersonate')}
                            >
                                Impersonate
                            </Button>
                        )}

                        {canManage &&
                            (archived ? (
                                <Button
                                    variant="primary"
                                    size="sm"
                                    icon={ArrowPathRoundedSquareIcon}
                                    loading={processing}
                                    onClick={() =>
                                        post(`/tenants/${tenant.id}/restore`)
                                    }
                                >
                                    Restore
                                </Button>
                            ) : suspended ? (
                                <>
                                    <Button
                                        variant="primary"
                                        size="sm"
                                        icon={ArrowPathRoundedSquareIcon}
                                        loading={processing}
                                        onClick={() =>
                                            post(
                                                `/tenants/${tenant.id}/reactivate`,
                                            )
                                        }
                                    >
                                        Reactivate
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        icon={ArchiveBoxIcon}
                                        onClick={() => openDialog('archive')}
                                    >
                                        Archive
                                    </Button>
                                </>
                            ) : (
                                <>
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        icon={PauseCircleIcon}
                                        onClick={() => openDialog('suspend')}
                                    >
                                        Suspend
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        icon={ArchiveBoxIcon}
                                        onClick={() => openDialog('archive')}
                                    >
                                        Archive
                                    </Button>
                                </>
                            ))}
                    </>
                }
            />

            {/* ── Suspend ── */}
            <Dialog
                open={dialog === 'suspend'}
                onClose={closeDialog}
                title={`Suspend ${tenant.name}?`}
                description="Every user of this hospital is signed out and blocked until you reactivate it."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={closeDialog}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="danger"
                            icon={PauseCircleIcon}
                            loading={processing}
                            onClick={() =>
                                post(`/tenants/${tenant.id}/suspend`, {
                                    reason,
                                })
                            }
                        >
                            Suspend hospital
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <Alert tone="warning" title="Access is cut immediately">
                        Reversible at any time from this page. Billing keeps
                        accruing unless the subscription is cancelled
                        separately.
                    </Alert>

                    <Field
                        label="Reason (optional)"
                        htmlFor="header-suspend-reason"
                        error={error}
                        hint="Recorded on the hospital and in the platform audit log."
                    >
                        <Textarea
                            id="header-suspend-reason"
                            rows={3}
                            maxLength={500}
                            value={reason}
                            placeholder="Unpaid invoice, abuse report, customer request…"
                            onChange={(event) => setReason(event.target.value)}
                        />
                    </Field>
                </div>
            </Dialog>

            {/* ── Archive ── */}
            <Dialog
                open={dialog === 'archive'}
                onClose={closeDialog}
                title={`Archive ${tenant.name}?`}
                description="Archiving revokes access but keeps all data. Restore it from this page at any time."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={closeDialog}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="danger"
                            icon={ArchiveBoxIcon}
                            loading={processing}
                            onClick={() =>
                                post(`/tenants/${tenant.id}/archive`, {
                                    reason,
                                })
                            }
                        >
                            Archive hospital
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <Alert tone="warning" title="Data is retained">
                        The hospital disappears from the active list and cannot
                        be used. Nothing is deleted — restore it whenever the
                        customer comes back.
                    </Alert>

                    <Field
                        label="Reason (optional)"
                        htmlFor="header-archive-reason"
                        error={error}
                    >
                        <Textarea
                            id="header-archive-reason"
                            rows={3}
                            maxLength={500}
                            value={reason}
                            placeholder="Hospital closed, contract ended…"
                            onChange={(event) => setReason(event.target.value)}
                        />
                    </Field>
                </div>
            </Dialog>

            {/* ── Impersonate (cross-domain native POST) ── */}
            <Dialog
                open={dialog === 'impersonate'}
                onClose={closeDialog}
                title={`Open ${tenant.name} as its administrator?`}
                description="A one-time support token is issued and the browser is sent to the hospital's own domain."
                size="md"
                footer={
                    <>
                        <Button variant="secondary" onClick={closeDialog}>
                            Cancel
                        </Button>
                        <Button
                            variant="primary"
                            icon={UserCircleIcon}
                            onClick={() =>
                                impersonateFormRef.current?.requestSubmit()
                            }
                        >
                            Start support session
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <Alert tone="warning" title="This is an audited action">
                        The session is recorded in the platform audit log with
                        your staff account as the actor.
                    </Alert>

                    {!primaryDomain && (
                        <Alert tone="critical" title="No domain configured">
                            Impersonation needs a domain on this hospital. Add
                            one from the edit screen first.
                        </Alert>
                    )}

                    <form
                        ref={impersonateFormRef}
                        method="post"
                        action={`/tenants/${tenant.id}/impersonate`}
                        className="hidden"
                    >
                        <input
                            type="hidden"
                            name="_token"
                            value={csrfToken()}
                        />
                    </form>

                    {primaryDomain && (
                        <p
                            className={cn(
                                'rounded-lg border border-line bg-surface-muted px-3.5 py-3',
                                'font-mono text-[12px] text-ink-muted',
                            )}
                        >
                            → https://{primaryDomain}/dashboard
                        </p>
                    )}
                </div>
            </Dialog>
        </>
    );
}
