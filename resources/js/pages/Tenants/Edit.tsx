import {
    BuildingOfficeIcon,
    GlobeAltIcon,
    DocumentTextIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import FormInput from '@/Components/FormInput';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface TenantEditProps {
    tenant: { id: string; slug: string; name: string; domains: string[] };
}

export default function TenantsEdit({ tenant }: TenantEditProps) {
    const { data, setData, put, processing, errors } = useForm({
        name: tenant.name,
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(`/tenants/${tenant.id}`);
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${tenant.name}`}
            headerSubtitle="Update tenant information"
        >
            <Head title={`Edit ${tenant.name}`} />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Tenants', href: '/tenants' },
                        { label: tenant.name },
                    ]}
                />

                <PageSection
                    title="Hospital Information"
                    icon={BuildingOfficeIcon}
                >
                    <form onSubmit={submit} className="space-y-6 p-6">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="col-span-2">
                                <FormInput
                                    name="name"
                                    label="Hospital Name"
                                    icon={DocumentTextIcon}
                                    required
                                    errors={errors}
                                    value={data.name}
                                    onChange={(v) => setData('name', v)}
                                />
                            </div>
                            <FormInput
                                name="slug"
                                label="Slug"
                                value={tenant.slug}
                                disabled
                                onChange={() => {}}
                            />
                            <FormInput
                                name="domain"
                                label="Domain"
                                icon={GlobeAltIcon}
                                value={tenant.domains[0] || ''}
                                disabled
                                onChange={() => {}}
                            />
                        </div>

                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/tenants"
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        Saving...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" /> Update
                                        Tenant
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </PageSection>
            </div>
        </SuperAdminLayout>
    );
}
