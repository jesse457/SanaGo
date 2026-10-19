import { CreditCardIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    SubscriptionForm,
    PlanFeatureList,
} from '@/pages/Subscriptions/components/SubscriptionForm';
import type {
    PlanOption,
    TenantOption,
} from '@/pages/Subscriptions/components/SubscriptionForm';

export default function SubscriptionsCreate({
    tenants,
    plans,
}: {
    tenants: TenantOption[];
    plans: PlanOption[];
}) {
    const { t } = useTranslation();
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [selectedPlanId, setSelectedPlanId] = useState('');
    const [formKey, setFormKey] = useState(0);

    const selectedPlan = plans.find((p) => p.id === Number(selectedPlanId));

    const initialValues = () => {
        const base = {
            tenant_id: '',
            plan_name: '',
            billing_cycle: 'monthly',
            price: '',
            start_date: new Date().toISOString().split('T')[0],
            end_date: '',
            status: 'active',
        };

        if (!selectedPlan) {
            return base;
        }

        const price =
            selectedPlan.monthly_price ?? selectedPlan.yearly_price ?? '';

        return {
            ...base,
            plan_name: selectedPlan.name,
            price: String(price),
            billing_cycle: selectedPlan.monthly_price ? 'monthly' : 'yearly',
        };
    };

    return (
        <SuperAdminLayout
            headerTitle={t('newSubscription')}
            headerSubtitle={t('createTenantSubscription')}
        >
            <Head title={t('newSubscription')} />

            <div className="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title={t('newSubscription')}
                    description={t('createTenantSubscription')}
                    icon={CreditCardIcon}
                    breadcrumbs={[
                        {
                            label: t('nav.subscriptions'),
                            href: '/subscriptions',
                        },
                        { label: t('newSubscription') },
                    ]}
                />

                {selectedPlan && (
                    <PlanFeatureList
                        plan={selectedPlan}
                        label={t('features')}
                    />
                )}

                <SubscriptionForm
                    key={formKey}
                    t={t}
                    initialValues={initialValues()}
                    submit="post"
                    action="/subscriptions"
                    submitLabel={t('createSubscription')}
                    submittingLabel={t('common.creating')}
                    cancelHref="/subscriptions"
                    cancelLabel={t('common.cancel')}
                    tenants={tenants}
                    plans={plans}
                    planId={selectedPlanId}
                    onPlanSelect={(value) => {
                        setSelectedPlanId(value);
                        setFormKey((key) => key + 1);
                    }}
                    successMessage={successMessage}
                    onSuccess={() =>
                        setSuccessMessage(t('subscriptionCreatedSuccessfully'))
                    }
                />
            </div>
        </SuperAdminLayout>
    );
}
