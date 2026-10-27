import { Head } from '@inertiajs/react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { PlanForm } from '@/pages/Plans/components/PlanForm';
import type { PlanFormValues } from '@/pages/Plans/components/PlanForm';
import type { FeatureOption } from '@/pages/Platform/components/types';

interface PlansCreateProps {
    featureOptions: FeatureOption[];
    defaults: {
        currency?: string;
        feature_flags?: Record<string, boolean>;
    };
}

export default function PlansCreate({
    featureOptions,
    defaults,
}: PlansCreateProps) {
    const { t } = useTranslation();

    const emptyPlan: PlanFormValues = {
        name: '',
        slug: '',
        description: '',
        monthly_price: '',
        yearly_price: '',
        currency: defaults?.currency ?? 'XAF',
        is_active: true,
        is_featured: false,
        sort_order: 0,
        max_users_per_tenant: '',
        max_patients_per_tenant: '',
        storage_gb: '',
        trial_days: '14',
        grace_days: '3',
        api_rate_limit: '',
        features_json: [],
        // The controller seeds this from PlanFeature::forPlanSlug('basic').
        feature_flags: { ...(defaults?.feature_flags ?? {}) },
    };

    return (
        <SuperAdminLayout
            headerTitle={t('createPlan')}
            headerSubtitle={t('defineNewSubscriptionPlan')}
        >
            <Head title={t('createPlan')} />
            <div className="mx-auto max-w-5xl space-y-5 pb-12">
                <PageHeader
                    title={t('createPlan')}
                    description="Define a subscription tier: its price, the quotas it enforces and the modules it unlocks."
                    breadcrumbs={[
                        { label: t('plans'), href: '/plans' },
                        { label: t('createPlan') },
                    ]}
                />

                <PlanForm
                    t={t}
                    initialValues={emptyPlan}
                    featureOptions={featureOptions ?? []}
                    submit="post"
                    action="/plans"
                    submitLabel={t('createPlan')}
                    submittingLabel="Creating…"
                    cancelHref="/plans"
                    cancelLabel={t('cancel')}
                />
            </div>
        </SuperAdminLayout>
    );
}
