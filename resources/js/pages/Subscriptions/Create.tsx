import {
    HomeIcon,
    ChevronRightIcon,
    BuildingOfficeIcon,
    CreditCardIcon,
    CurrencyDollarIcon,
    CalendarDaysIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
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

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

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
        } else {
            setData('plan_id', planId);
        }
    };

    const submit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post('/subscriptions', {
            onSuccess: () => {
                setSuccessMessage(t('subscriptionCreatedSuccessfully'));
                setData('tenant_id', '');
                setData('plan_id', '');
                setData('plan_name', '');
                setData('billing_cycle', 'monthly');
                setData('price', '');
                setData('start_date', new Date().toISOString().split('T')[0]);
                setData('end_date', '');
                setData('status', 'active');
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={t('newSubscription')}
            headerSubtitle={t('createTenantSubscription')}
        >
            <Head title={t('newSubscription')} />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('nav.subscriptions')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('newSubscription')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <h3 className="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    {t('success')}
                                </h3>
                                <div className="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                    {successMessage}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSuccessMessage(null)}
                            className="rounded-lg p-1.5 text-emerald-500 transition hover:text-emerald-700 dark:hover:text-emerald-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <div className="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div className="flex items-center gap-3">
                            <ExclamationTriangleIcon className="h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage || t('someFieldsRequireAttention')}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setErrorMessage(null)}
                            className="rounded-lg p-1.5 text-red-500 transition hover:text-red-700 dark:hover:text-red-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {/* Main Form Card */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={submit} className="space-y-8 p-6">
                        {/* Section 1: Tenant & Plan */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <BuildingOfficeIcon className="h-5 w-5" />
                                {t('tenantAndPlan')}
                            </h3>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('tenant')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <BuildingOfficeIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <select
                                            value={data.tenant_id}
                                            onChange={(e) =>
                                                setData('tenant_id', e.target.value)
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.tenant_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        >
                                            <option value="">{t('selectTenant')}</option>
                                            {tenants.map((t) => (
                                                <option key={t.id} value={t.id}>
                                                    {t.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    {errors.tenant_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.tenant_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('plan')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CreditCardIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <select
                                            value={data.plan_id}
                                            onChange={(e) => selectPlan(e.target.value)}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.plan_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        >
                                            <option value="">{t('customManual')}</option>
                                            {plans.map((p) => (
                                                <option key={p.id} value={p.id}>
                                                    {p.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    {errors.plan_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.plan_id}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {selectedPlan && (
                                <div className="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/10">
                                    <p className="mb-2 text-sm font-bold text-blue-700 dark:text-blue-400">
                                        {selectedPlan.name} {t('features')}:
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
                        </div>

                        {/* Section 2: Billing Details */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <CurrencyDollarIcon className="h-5 w-5" />
                                {t('billingDetails')}
                            </h3>

                            <div className="grid grid-cols-3 gap-4">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('planName')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.plan_name}
                                            onChange={(e) =>
                                                setData('plan_name', e.target.value)
                                            }
                                            placeholder={t('planName')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.plan_name ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.plan_name && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.plan_name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('billingCycle')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CurrencyDollarIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <select
                                            value={data.billing_cycle}
                                            onChange={(e) =>
                                                setData('billing_cycle', e.target.value)
                                            }
                                            className="w-full rounded-lg border border-gray-300 py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                        >
                                            <option value="monthly">{t('monthly')}</option>
                                            <option value="yearly">{t('yearly')}</option>
                                        </select>
                                    </div>
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('price')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CurrencyDollarIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={data.price}
                                            onChange={(e) =>
                                                setData('price', e.target.value)
                                            }
                                            placeholder="0.00"
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.price ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.price && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.price}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('startDate')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CalendarDaysIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="date"
                                            value={data.start_date}
                                            onChange={(e) =>
                                                setData('start_date', e.target.value)
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.start_date ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.start_date && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.start_date}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('endDate')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CalendarDaysIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="date"
                                            value={data.end_date}
                                            onChange={(e) =>
                                                setData('end_date', e.target.value)
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.end_date ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.end_date && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.end_date}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('status')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CheckCircleIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <select
                                            value={data.status}
                                            onChange={(e) =>
                                                setData('status', e.target.value)
                                            }
                                            className="w-full rounded-lg border border-gray-300 py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                        >
                                            <option value="active">{t('active')}</option>
                                            <option value="inactive">{t('inactive')}</option>
                                            <option value="cancelled">{t('cancelled')}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/subscriptions"
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </Link>

                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        {t('common.creating')}
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t('createSubscription')}
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
