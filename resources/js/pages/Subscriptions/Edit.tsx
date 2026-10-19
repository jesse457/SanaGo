import { CreditCardIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { SubscriptionForm } from '@/pages/Subscriptions/components/SubscriptionForm';
import type { TenantOption } from '@/pages/Subscriptions/components/SubscriptionForm';

interface SubscriptionData {
    id: number;
    tenant_id: string;
    plan_name: string;
    billing_cycle: string;
    price: number;
    start_date: string;
    end_date: string | null;
    status: string;
    features_json: unknown[];
}

export default function SubscriptionsEdit({
    subscription,
    tenants,
}: {
    subscription: SubscriptionData;
    tenants: TenantOption[];
}) {
    const { t } = useTranslation();

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${subscription.plan_name}`}
            headerSubtitle="Update subscription"
        >
            <Head title={`Edit ${subscription.plan_name}`} />
            <div className="mx-auto max-w-4xl space-y-6 px-6">
                <PageHeader
                    title={subscription.plan_name}
                    eyebrow="Edit subscription"
                    description="Adjust the tenant assignment, term dates and commercial terms."
                    icon={CreditCardIcon}
                    breadcrumbs={[
                        {
                            label: t('nav.subscriptions'),
                            href: '/subscriptions',
                        },
                        {
                            label: subscription.plan_name,
                            href: `/subscriptions/${subscription.id}`,
                        },
                        { label: 'Edit' },
                    ]}
                />

                <SubscriptionForm
                    t={t}
                    initialValues={{
                        tenant_id: subscription.tenant_id,
                        plan_name: subscription.plan_name,
                        billing_cycle: subscription.billing_cycle,
                        price: String(subscription.price),
                        start_date: subscription.start_date,
                        end_date: subscription.end_date ?? '',
                        status: subscription.status,
                    }}
                    submit="put"
                    action={`/subscriptions/${subscription.id}`}
                    submitLabel={t('update')}
                    submittingLabel="Saving"
                    cancelHref="/subscriptions"
                    cancelLabel={t('common.cancel')}
                    tenants={tenants}
                />
            </div>
        </SuperAdminLayout>
    );
}
