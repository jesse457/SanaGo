import {
    CurrencyDollarIcon,
    ArrowPathIcon,
    CheckIcon,
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
interface SubscriptionData {
    id: number;
    tenant_id: string;
    plan_name: string;
    billing_cycle: string;
    price: number;
    start_date: string;
    end_date: string | null;
    status: string;
    features_json: any[];
}

export default function SubscriptionsEdit({
    subscription,
    tenants,
}: {
    subscription: SubscriptionData;
    tenants: TenantOption[];
}) {
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm({
        tenant_id: subscription.tenant_id,
        plan_name: subscription.plan_name,
        billing_cycle: subscription.billing_cycle,
        price: String(subscription.price),
        start_date: subscription.start_date,
        end_date: subscription.end_date || '',
        status: subscription.status,
        features_json: subscription.features_json || [],
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(`/subscriptions/${subscription.id}`);
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${subscription.plan_name}`}
            headerSubtitle="Update subscription"
        >
            <Head title={`Edit ${subscription.plan_name}`} />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home' },
                        { label: 'Subscriptions', href: '/subscriptions' },
                        { label: subscription.plan_name },
                    ]}
                />

                <form onSubmit={submit} className="space-y-8">
                    <PageSection title="Subscription Details">
                        <div className="grid grid-cols-2 gap-4 p-6">
                            <div className="col-span-2">
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
                                />
                            </div>
                            <FormInput
                                name="plan_name"
                                label="Plan Name"
                                value={data.plan_name}
                                onChange={(v) => setData('plan_name', v)}
                                required
                                errors={errors}
                            />
                            <FormSelect
                                name="billing_cycle"
                                label="Billing Cycle"
                                value={data.billing_cycle}
                                onChange={(v) => setData('billing_cycle', v)}
                                options={[
                                    {
                                        value: 'monthly',
                                        label: 'Monthly',
                                    },
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
                                errors={errors}
                            />
                            <FormSelect
                                name="status"
                                label="Status"
                                value={data.status}
                                onChange={(v) => setData('status', v)}
                                options={[
                                    {
                                        value: 'active',
                                        label: 'Active',
                                    },
                                    {
                                        value: 'inactive',
                                        label: 'Inactive',
                                    },
                                    {
                                        value: 'cancelled',
                                        label: 'Cancelled',
                                    },
                                ]}
                            />
                            <FormInput
                                name="start_date"
                                label="Start Date"
                                type="date"
                                value={data.start_date}
                                onChange={(v) => setData('start_date', v)}
                                errors={errors}
                            />
                            <FormInput
                                name="end_date"
                                label="End Date"
                                type="date"
                                value={data.end_date}
                                onChange={(v) => setData('end_date', v)}
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
                                    Saving...
                                </span>
                            ) : (
                                <span className="flex items-center gap-2">
                                    <CheckIcon className="h-4 w-4" /> Update
                                </span>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </SuperAdminLayout>
    );
}
