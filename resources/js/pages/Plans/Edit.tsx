import { CreditCardIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { Badge, PageHeader } from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { PlanForm } from '@/pages/Plans/components/PlanForm';
import type { PlanFormValues } from '@/pages/Plans/components/PlanForm';
import type {
    FeatureOption,
    PlanRecord,
} from '@/pages/Platform/components/types';

interface PlansEditProps {
    plan: PlanRecord;
    featureOptions: FeatureOption[];
    subscribers?: number;
}

const asString = (value: number | null | undefined): string =>
    value === null || value === undefined ? '' : String(value);

export default function PlansEdit({
    plan,
    featureOptions,
    subscribers,
}: PlansEditProps) {
    const t = (key: string) => key;

    const initialValues: PlanFormValues = {
        name: plan.name,
        slug: plan.slug,
        description: plan.description ?? '',
        monthly_price: asString(plan.monthly_price),
        yearly_price: asString(plan.yearly_price),
        currency: plan.currency ?? 'XAF',
        is_active: plan.is_active,
        is_featured: plan.is_featured,
        sort_order: plan.sort_order ?? 0,
        max_users_per_tenant: asString(plan.max_users_per_tenant),
        max_patients_per_tenant: asString(plan.max_patients_per_tenant),
        storage_gb: asString(plan.storage_gb),
        trial_days: asString(plan.trial_days),
        grace_days: asString(plan.grace_days),
        api_rate_limit: asString(plan.api_rate_limit),
        features_json: plan.features_json ?? [],
        feature_flags: { ...(plan.feature_flags ?? {}) },
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${plan.name}`}
            headerSubtitle="Plan catalogue"
        >
            <Head title={`Edit ${plan.name}`} />
            <div className="mx-auto max-w-5xl space-y-5 pb-12">
                <PageHeader
                    title={plan.name}
                    eyebrow="Edit plan"
                    description="Update pricing, quotas and the capabilities this plan unlocks. Existing subscriptions keep their agreed price."
                    icon={CreditCardIcon}
                    breadcrumbs={[
                        { label: 'Plans', href: '/plans' },
                        { label: plan.name, href: '/plans' },
                        { label: 'Edit' },
                    ]}
                    meta={
                        <>
                            <Badge tone="muted" variant="outline">
                                {plan.slug}
                            </Badge>
                            <Badge
                                tone={plan.is_active ? 'positive' : 'muted'}
                                dot
                            >
                                {plan.is_active ? 'Active' : 'Inactive'}
                            </Badge>
                            {plan.is_featured && (
                                <Badge tone="accent">Featured</Badge>
                            )}
                        </>
                    }
                />

                <PlanForm
                    t={t}
                    initialValues={initialValues}
                    featureOptions={featureOptions ?? []}
                    submit="put"
                    action={`/plans/${plan.id}`}
                    submitLabel="Update plan"
                    submittingLabel="Saving…"
                    cancelHref="/plans"
                    cancelLabel="Cancel"
                    subscribers={subscribers ?? null}
                />
            </div>
        </SuperAdminLayout>
    );
}
