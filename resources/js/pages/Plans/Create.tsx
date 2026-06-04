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
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import FormInput from '@/Components/FormInput';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface FeatureInput {
    feature_label: string;
    is_highlighted: boolean;
}

export default function PlansCreate() {
    const { data, setData, post, processing, errors } = useForm({
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
        post('/plans');
    };

    return (
        <SuperAdminLayout
            headerTitle="Create Plan"
            headerSubtitle="Define a new subscription plan"
        >
            <Head title="Create Plan" />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home' },
                        { label: 'Plans', href: '/plans' },
                        { label: 'New' },
                    ]}
                />

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={submit} className="space-y-8 p-6">
                        {/* Plan Identity */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <DocumentTextIcon className="h-5 w-5" /> Plan
                                Identity
                            </h3>
                            <div className="grid grid-cols-2 gap-4">
                                <FormInput
                                    name="name"
                                    label="Plan Name"
                                    icon={CreditCardIcon}
                                    value={data.name}
                                    onChange={(v) => setData('name', v)}
                                    errors={errors}
                                    required
                                    placeholder="e.g. Professional"
                                    className="col-span-2"
                                />
                                <FormInput
                                    name="slug"
                                    label="Slug"
                                    value={data.slug}
                                    onChange={(v) => setData('slug', v)}
                                    errors={errors}
                                    required
                                    placeholder="professional"
                                />
                                <FormTextarea
                                    name="description"
                                    label="Description"
                                    value={data.description}
                                    onChange={(v) => setData('description', v)}
                                    errors={errors}
                                    rows={2}
                                    placeholder="Brief description of this plan..."
                                    className="col-span-2"
                                />
                            </div>
                        </div>

                        {/* Pricing & Limits */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <CurrencyDollarIcon className="h-5 w-5" />{' '}
                                Pricing & Limits
                            </h3>
                            <div className="grid grid-cols-2 gap-4">
                                <FormInput
                                    name="monthly_price"
                                    label="Monthly Price ($)"
                                    type="number"
                                    step="0.01"
                                    icon={CurrencyDollarIcon}
                                    value={data.monthly_price}
                                    onChange={(v) =>
                                        setData('monthly_price', v)
                                    }
                                    errors={errors}
                                    placeholder="99.99"
                                />
                                <FormInput
                                    name="yearly_price"
                                    label="Yearly Price ($)"
                                    type="number"
                                    step="0.01"
                                    icon={CurrencyDollarIcon}
                                    value={data.yearly_price}
                                    onChange={(v) => setData('yearly_price', v)}
                                    errors={errors}
                                    placeholder="999.99"
                                />
                                <FormInput
                                    name="max_users_per_tenant"
                                    label="Max Users / Tenant"
                                    type="number"
                                    icon={UserGroupIcon}
                                    value={data.max_users_per_tenant}
                                    onChange={(v) =>
                                        setData('max_users_per_tenant', v)
                                    }
                                    errors={errors}
                                    placeholder="50"
                                />
                                <FormInput
                                    name="storage_gb"
                                    label="Storage (GB)"
                                    type="number"
                                    icon={ArchiveBoxIcon}
                                    value={data.storage_gb}
                                    onChange={(v) => setData('storage_gb', v)}
                                    errors={errors}
                                    placeholder="50"
                                />
                            </div>
                            <div className="flex items-center gap-3 pt-2">
                                <label className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    Active
                                </label>
                                <input
                                    type="checkbox"
                                    checked={data.is_active}
                                    onChange={(e) =>
                                        setData('is_active', e.target.checked)
                                    }
                                    className="rounded border-gray-300 text-blue-600"
                                />
                            </div>
                        </div>

                        {/* Features */}
                        <div className="space-y-4">
                            <div className="flex items-center justify-between border-b border-gray-100 pb-2 dark:border-neutral-700">
                                <h3 className="flex items-center gap-2 text-sm font-bold tracking-wide text-blue-600 uppercase">
                                    <TagIcon className="h-5 w-5" /> Features
                                </h3>
                                <button
                                    type="button"
                                    onClick={addFeature}
                                    className="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                >
                                    <PlusIcon className="h-4 w-4" /> Add Feature
                                </button>
                            </div>
                            {data.features_json.length === 0 ? (
                                <p className="py-4 text-center text-sm text-gray-400">
                                    No features added yet. Click &quot;Add
                                    Feature&quot; to start.
                                </p>
                            ) : (
                                data.features_json.map((f, i) => (
                                    <div
                                        key={i}
                                        className="flex items-center gap-3"
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
                                                setData('features_json', n);
                                            }}
                                            placeholder="Feature label"
                                            className="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                                        />
                                        <label className="flex items-center gap-1 text-xs text-gray-500">
                                            <input
                                                type="checkbox"
                                                checked={f.is_highlighted}
                                                onChange={(e) => {
                                                    const n = [
                                                        ...data.features_json,
                                                    ];
                                                    n[i].is_highlighted =
                                                        e.target.checked;
                                                    setData('features_json', n);
                                                }}
                                                className="rounded border-gray-300 text-blue-600"
                                            />{' '}
                                            Highlight
                                        </label>
                                        <button
                                            type="button"
                                            onClick={() => removeFeature(i)}
                                            className="text-red-400 hover:text-red-600"
                                        >
                                            <TrashIcon className="h-4 w-4" />
                                        </button>
                                    </div>
                                ))
                            )}
                        </div>

                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/plans"
                                className="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        Creating...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" /> Create
                                        Plan
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
