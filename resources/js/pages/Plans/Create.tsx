import {
    CreditCardIcon,
    PlusIcon,
    TrashIcon,
    ArrowPathIcon,
    CheckIcon,
    TagIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    ArchiveBoxIcon,
    UserGroupIcon,
    HomeIcon,
    ChevronRightIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface FeatureInput {
    feature_label: string;
    is_highlighted: boolean;
}

export default function PlansCreate() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        slug: '',
        description: '',
        monthly_price: '',
        yearly_price: '',
        is_active: true,
        sort_order: 0,
        max_users_per_tenant: '',
        storage_gb: '',
        features_json: [] as FeatureInput[],
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const addFeature = () =>
        setData('features_json', [
            ...data.features_json,
            { feature_label: '', is_highlighted: false },
        ]);
    const removeFeature = (i: number) =>
        setData(
            'features_json',
            data.features_json.filter((_, j) => j !== i),
        );
    const submit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post('/plans', {
            onSuccess: () => {
                setSuccessMessage(t('planCreatedSuccessfully'));
                reset();
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={t('createPlan')}
            headerSubtitle={t('defineNewSubscriptionPlan')}
        >
            <Head title={t('createPlan')} />
            <div className="mx-auto max-w-8xl space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('plans')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('createPlan')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
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
                            <ExclamationTriangleIcon className="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage ||
                                        t('someFieldsRequireAttention')}
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
                        {/* Plan Identity */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <DocumentTextIcon className="h-5 w-5" />
                                {t('planIdentity')}
                            </h3>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('planName')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CreditCardIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            placeholder={t(
                                                'planNamePlaceholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.name ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.name && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('slug')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <TagIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.slug}
                                            onChange={(e) =>
                                                setData('slug', e.target.value)
                                            }
                                            placeholder={t('slugPlaceholder')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.slug ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.slug && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.slug}
                                        </p>
                                    )}
                                </div>

                                <div className="col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('description')}
                                    </label>
                                    <textarea
                                        value={data.description}
                                        onChange={(e) =>
                                            setData(
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'descriptionPlaceholder',
                                        )}
                                        rows={2}
                                        className={`w-full resize-none rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.description ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    />
                                    {errors.description && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.description}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Pricing & Limits */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <CurrencyDollarIcon className="h-5 w-5" />
                                {t('pricingAndLimits')}
                            </h3>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('monthlyPrice')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CurrencyDollarIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={data.monthly_price}
                                            onChange={(e) =>
                                                setData(
                                                    'monthly_price',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'monthlyPricePlaceholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.monthly_price ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.monthly_price && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.monthly_price}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('yearlyPrice')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CurrencyDollarIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={data.yearly_price}
                                            onChange={(e) =>
                                                setData(
                                                    'yearly_price',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'yearlyPricePlaceholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.yearly_price ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.yearly_price && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.yearly_price}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('maxUsersPerTenant')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <UserGroupIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.max_users_per_tenant}
                                            onChange={(e) =>
                                                setData(
                                                    'max_users_per_tenant',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'maxUsersPlaceholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.max_users_per_tenant ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.max_users_per_tenant && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.max_users_per_tenant}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('storageGb')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <ArchiveBoxIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.storage_gb}
                                            onChange={(e) =>
                                                setData(
                                                    'storage_gb',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'storagePlaceholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.storage_gb ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.storage_gb && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.storage_gb}
                                        </p>
                                    )}
                                </div>
                            </div>
                            <div className="flex items-center gap-3 pt-2">
                                <label className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('active')}
                                </label>
                                <input
                                    type="checkbox"
                                    checked={data.is_active}
                                    onChange={(e) =>
                                        setData(
                                            'is_active',
                                            e.target.checked,
                                        )
                                    }
                                    className="rounded border-gray-300 text-blue-600 dark:border-neutral-600 dark:bg-neutral-800"
                                />
                            </div>
                        </div>

                        {/* Features */}
                        <div className="space-y-4">
                            <div className="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-neutral-700">
                                <h3 className="flex items-center gap-2 text-sm font-bold tracking-wide text-blue-600 uppercase">
                                    <TagIcon className="h-5 w-5" />{' '}
                                    {t('features')}
                                </h3>
                                <button
                                    type="button"
                                    onClick={addFeature}
                                    className="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                >
                                    <PlusIcon className="h-4 w-4" />{' '}
                                    {t('addFeature')}
                                </button>
                            </div>
                            {data.features_json.length === 0 ? (
                                <div className="rounded-lg border border-dashed border-gray-300 bg-gray-50 py-8 text-center dark:border-neutral-600 dark:bg-neutral-700/30">
                                    <TagIcon className="mx-auto mb-2 h-8 w-8 text-gray-400" />
                                    <p className="text-sm text-gray-500 dark:text-neutral-400">
                                        {t('noFeaturesAdded')}
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {data.features_json.map((f, i) => (
                                        <div
                                            key={i}
                                            className="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-neutral-600 dark:bg-neutral-700/30"
                                        >
                                            <input
                                                type="text"
                                                value={f.feature_label}
                                                onChange={(e) => {
                                                    const n = [
                                                        ...data.features_json,
                                                    ];
                                                    n[i].feature_label =
                                                        e.target.value;
                                                    setData(
                                                        'features_json',
                                                        n,
                                                    );
                                                }}
                                                placeholder={t(
                                                    'featureLabelPlaceholder',
                                                )}
                                                className={`flex-1 rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors[`features_json.${i}.feature_label`] ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                            />
                                            <label className="flex cursor-pointer items-center gap-1.5 text-xs text-gray-500 dark:text-neutral-400">
                                                <input
                                                    type="checkbox"
                                                    checked={f.is_highlighted}
                                                    onChange={(e) => {
                                                        const n = [
                                                            ...data.features_json,
                                                        ];
                                                        n[i].is_highlighted =
                                                            e.target.checked;
                                                        setData(
                                                            'features_json',
                                                            n,
                                                        );
                                                    }}
                                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800"
                                                />{' '}
                                                {t('highlight')}
                                            </label>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    removeFeature(i)
                                                }
                                                className="rounded p-1 text-red-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/plans"
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('cancel')}
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        {t('creating')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t('createPlan')}
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
