import { Head, usePage } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import type {
    PlanOption,
    TenantDetail,
} from '@/pages/Platform/components/types';
import { InvoicesPanel } from '@/pages/Tenants/components/InvoicesPanel';
import { LimitsPanel } from '@/pages/Tenants/components/LimitsPanel';
import { NotesPanel } from '@/pages/Tenants/components/NotesPanel';
import { SubscriptionPanel } from '@/pages/Tenants/components/SubscriptionPanel';
import { TenantHeader } from '@/pages/Tenants/components/TenantHeader';
import { TenantHealthPanel } from '@/pages/Tenants/components/TenantHealthPanel';

interface TenantShowProps {
    tenant: TenantDetail;
    plans: PlanOption[];
}

/** Permission check against the shared `auth.user.permissions` list. */
function useCan(permission: string): boolean {
    const { props } = usePage();
    const permissions = ((props.auth as any)?.user?.permissions ??
        []) as string[];

    return permissions.includes(permission);
}

/**
 * Tenants/Show — the full control surface for one hospital.
 *
 * Stacked sections rather than tabs: every operator task on this page
 * (lifecycle, subscription, quotas, invoices, support access) is something a
 * platform engineer scans in one pass.
 */
export default function TenantShow({ tenant, plans }: TenantShowProps) {
    const canManageTenants = useCan('manage-tenants');
    const canManageSubscriptions = useCan('manage-subscriptions');
    const canImpersonate = useCan('impersonate-tenants') || canManageTenants;

    return (
        <SuperAdminLayout
            headerTitle={tenant.name}
            headerSubtitle="Hospital control panel"
        >
            <Head title={`${tenant.name} — Hospital`} />

            <div className="space-y-5 pb-12">
                <TenantHeader
                    tenant={tenant}
                    canManage={canManageTenants}
                    canImpersonate={canImpersonate}
                />

                <SubscriptionPanel
                    tenantId={tenant.id}
                    tenantName={tenant.name}
                    subscription={tenant.subscription}
                    plans={plans ?? []}
                    canManage={canManageSubscriptions}
                />

                {tenant.subscription && (
                    <LimitsPanel
                        tenantId={tenant.id}
                        subscription={tenant.subscription}
                        usage={tenant.usage}
                        canManage={canManageSubscriptions}
                    />
                )}

                <TenantHealthPanel
                    health={tenant.health}
                    usage={tenant.usage}
                    users={tenant.users ?? []}
                    lastSeenAt={tenant.last_seen_at}
                />

                <InvoicesPanel
                    tenantId={tenant.id}
                    tenantName={tenant.name}
                    invoices={tenant.invoices ?? []}
                    canManage={canManageSubscriptions}
                />

                <NotesPanel
                    tenantId={tenant.id}
                    notes={tenant.notes ?? []}
                    canManage={canManageTenants}
                />
            </div>
        </SuperAdminLayout>
    );
}
