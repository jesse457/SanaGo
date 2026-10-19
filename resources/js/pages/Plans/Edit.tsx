import { CreditCardIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { PageHeader } from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { PlanForm } from '@/pages/Plans/components/PlanForm';
import type { PlanFormValues } from '@/pages/Plans/components/PlanForm';

interface Plan {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    monthly_price: string | null;
    yearly_price: string | null;
    is_active: boolean;
    sort_order: number;
    max_users_per_tenant: number | null;
    storage_gb: number | null;
    features_json: PlanFormValues['features_json'];
}

export default function PlansEdit({ plan }: { plan: Plan }) {
    const identity = (key: string) => key;

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${plan.name}`}
            headerSubtitle="Update subscription plan"
        >
            <Head title={`Edit ${plan.name}`} />
            <div className="mx-auto max-w-4xl space-y-6 px-6">
                <PageHeader
                    title={plan.name}
                    eyebrow="Edit plan"
                    description="Update pricing, limits and the features attached to this plan."
                    icon={CreditCardIcon}
                    breadcrumbs={[
                        { label: 'Plans', href: '/plans' },
                        { label: plan.name },
                    ]}
                />

                <PlanForm
                    t={identity}
                    initialValues={{
                        name: plan.name,
                        slug: plan.slug,
                        description: plan.description ?? '',
                        monthly_price: plan.monthly_price ?? '',
                        yearly_price: plan.yearly_price ?? '',
                        is_active: plan.is_active,
                        sort_order: plan.sort_order,
                        max_users_per_tenant: plan.max_users_per_tenant ?? '',
                        storage_gb: plan.storage_gb ?? '',
                        features_json:
                            (plan.features_json as PlanFormValues['features_json']) ??
                            [],
                    }}
                    submit="put"
                    action={`/plans/${plan.id}`}
                    submitLabel="Update plan"
                    submittingLabel="Saving"
                    cancelHref="/plans"
                    cancelLabel="Cancel"
                />
            </div>
        </SuperAdminLayout>
    );
}
