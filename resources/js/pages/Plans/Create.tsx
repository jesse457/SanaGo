import { CreditCardIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { PlanForm } from '@/pages/Plans/components/PlanForm';
import type { PlanFormValues } from '@/pages/Plans/components/PlanForm';

const emptyPlan: PlanFormValues = {
    name: '',
    slug: '',
    description: '',
    monthly_price: '',
    yearly_price: '',
    is_active: true,
    sort_order: 0,
    max_users_per_tenant: '',
    storage_gb: '',
    features_json: [],
};

export default function PlansCreate() {
    const { t } = useTranslation();
    const [successMessage, setSuccessMessage] = useState<string | null>(null);

    return (
        <SuperAdminLayout
            headerTitle={t('createPlan')}
            headerSubtitle={t('defineNewSubscriptionPlan')}
        >
            <Head title={t('createPlan')} />
            <div className="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title={t('createPlan')}
                    description={t('defineNewSubscriptionPlan')}
                    icon={CreditCardIcon}
                    breadcrumbs={[
                        { label: t('plans'), href: '/plans' },
                        { label: t('createPlan') },
                    ]}
                />

                <PlanForm
                    t={t}
                    initialValues={emptyPlan}
                    submit="post"
                    action="/plans"
                    submitLabel={t('createPlan')}
                    submittingLabel={t('creating')}
                    cancelHref="/plans"
                    cancelLabel={t('cancel')}
                    successMessage={successMessage}
                    onSuccess={() =>
                        setSuccessMessage(t('planCreatedSuccessfully'))
                    }
                />
            </div>
        </SuperAdminLayout>
    );
}
