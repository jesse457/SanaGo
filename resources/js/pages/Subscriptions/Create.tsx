import {
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    CalendarDaysIcon,
    ArrowPathIcon,
    CheckIcon,
    CheckCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface TenantOption {
    id: string;
    name: string;
}
interface PlanOption {
    id: number;
    name: string;
    monthly_price: string | null;
    yearly_price: string | null;
    features_json: { feature_label: string; is_highlighted: boolean }[];
}

export default function SubscriptionsCreate({
    tenants,
    plans,
}: {
    tenants: TenantOption[];
    plans: PlanOption[];
}) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        tenant_id: '',
        plan_id: '',
        plan_name: '',
        billing_cycle: 'monthly',
        price: '',
        start_date: new Date().toISOString().split('T')[0],
        end_date: '',
        status: 'active',
    });

    const selectedPlan = plans.find((p) => p.id === Number(data.plan_id));

    const selectPlan = (planId: string) => {
        const plan = plans.find((p) => p.id === Number(planId));

        if (plan) {
            const price = plan.monthly_price ?? plan.yearly_price ?? '';
            const cycle = plan.monthly_price ? 'monthly' : 'yearly';
            setData({
                ...data,
                plan_id: planId,
                plan_name: plan.name,
                price: String(price),
                billing_cycle: cycle,
            });
        }
    };

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post('/subscriptions');
    };

    return (
        <SuperAdminLayout
            headerTitle="New Subscription"
            headerSubtitle="Create a tenant subscription"
        >
            <Head title="New Subscription" />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home' },
                        { label: 'Subscriptions', href: '/subscriptions' },
                        { label: 'New' },
                    ]}
                />

                <form onSubmit={submit} className="space-y-8">
                    <PageSection
                        icon={BuildingOfficeIcon}
                        title="Tenant & Plan"
                    >
                        <div className="grid grid-cols-2 gap-4 p-6">
                            <FormSelect
                                name="tenant_id"
                                label="Tenant"
                                value={data.tenant_id}
                                onChange={(v) => setData('tenant_id', v)}
                                options={tenants.map((t) => ({
                                    value: t.id,
                                    label: t.name,
                                }))}
                                placeholder="Select tenant..."
                                required
                                errors={errors}
                            />
                            <FormSelect
                                name="plan_id"
                                label="Plan"
                                value={data.plan_id}
                                onChange={selectPlan}
                                options={plans.map((p) => ({
                                    value: p.id,
                                    label: p.name,
                                }))}
                                placeholder="Custom / Manual"
                                errors={errors}
                            />
                        </div>
                        {selectedPlan && (
                            <div className="mx-6 mb-6 rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/10">
                                <p className="mb-2 text-sm font-bold text-blue-700 dark:text-blue-400">
                                    {selectedPlan.name} Features:
                                </p>
                                <div className="flex flex-wrap gap-1.5">
                                    {selectedPlan.features_json
                                        .filter((f) => f.is_highlighted)
                                        .map((f, i) => (
                                            <span
                                                key={i}
                                                className="rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                                            >
                                                <CheckCircleIcon className="mr-1 inline h-3 w-3" />
                                                {f.feature_label}
                                            </span>
                                        ))}
                                </div>
                            </div>
                        )}
                    </PageSection>

                    <PageSection
                        icon={CurrencyDollarIcon}
                        title="Billing Details"
                    >
                        <div className="grid grid-cols-3 gap-4 p-6">
                            <FormInput
                                name="plan_name"
                                label="Plan Name"
                                value={data.plan_name}
                                onChange={(v) => setData('plan_name', v)}
                                errors={errors}
                            />
                            <FormSelect
                                name="billing_cycle"
                                label="Billing Cycle"
                                value={data.billing_cycle}
                                onChange={(v) => setData('billing_cycle', v)}
                                options={[
                                    { value: 'monthly', label: 'Monthly' },
                                    { value: 'yearly', label: 'Yearly' },
                                ]}
                            />
                            <FormInput
                                name="price"
                                label="Price ($)"
                                type="number"
                                step="0.01"
                                icon={CurrencyDollarIcon}
                                value={data.price}
                                onChange={(v) => setData('price', v)}
                                required
                                errors={errors}
                            />
                            <FormInput
                                name="start_date"
                                label="Start Date"
                                type="date"
                                icon={CalendarDaysIcon}
                                value={data.start_date}
                                onChange={(v) => setData('start_date', v)}
                                required
                                errors={errors}
                            />
                            <FormInput
                                name="end_date"
                                label="End Date"
                                type="date"
                                value={data.end_date}
                                onChange={(v) => setData('end_date', v)}
                            />
                            <FormSelect
                                name="status"
                                label="Status"
                                value={data.status}
                                onChange={(v) => setData('status', v)}
                                options={[
                                    { value: 'active', label: 'Active' },
                                    { value: 'inactive', label: 'Inactive' },
                                    {
                                        value: 'cancelled',
                                        label: 'Cancelled',
                                    },
                                ]}
                            />
                        </div>
                    </PageSection>

                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href="/subscriptions"
                            className="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.cancel')}
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                        >
                            {processing ? (
                                <span className="flex items-center gap-2">
                                    <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                    Creating...
                                </span>
                            ) : (
                                <span className="flex items-center gap-2">
                                    <CheckIcon className="h-4 w-4" /> Create
                                    Subscription
                                </span>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </SuperAdminLayout>
    );
}
