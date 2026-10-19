import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    BuildingOfficeIcon,
    GlobeAltIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface TenantItem {
    id: string;
    slug: string;
    name: string;
    domains: string[];
    is_active: boolean;
    suspended_at: string | null;
    created_at: string;
    subscription: { plan_name: string; status: string } | null;
}

export default function TenantsIndex({ tenants }: { tenants: TenantItem[] }) {
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<TenantItem | null>(null);
    const { delete: destroy, processing } = useForm();
    const filtered = useMemo(
        () =>
            tenants.filter(
                (t) =>
                    t.name.toLowerCase().includes(search.toLowerCase()) ||
                    t.slug.toLowerCase().includes(search.toLowerCase()),
            ),
        [tenants, search],
    );

    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/tenants/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Tenants"
            headerSubtitle="Manage hospital tenants"
        >
            <Head title="Tenants" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[{ label: 'Home', href: '/' }, { label: 'Tenants' }]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div className="w-full sm:w-96">
                        <FormInput
                            name="search"
                            label="Search"
                            icon={MagnifyingGlassIcon}
                            placeholder="Search tenants..."
                            value={search}
                            onChange={setSearch}
                        />
                    </div>
                    <Link
                        href="/tenants/create"
                        className="inline-flex shrink-0 items-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-ink-inverse shadow-surface transition-colors hover:bg-accent-hover"
                    >
                        <PlusIcon className="h-4 w-4" /> New Tenant
                    </Link>
                </div>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {filtered.map((tenant) => (
                        <div
                            key={tenant.id}
                            className="group relative flex h-full flex-col rounded-xl border border-line bg-surface p-6 transition-all duration-200 hover:border-accent hover:shadow-surface"
                        >
                            <div className="mb-4 flex items-start justify-between">
                                <div className="rounded-lg bg-accent-soft p-3 text-accent">
                                    <BuildingOfficeIcon className="h-6 w-6" />
                                </div>
                                <StatusBadge
                                    label={
                                        tenant.is_active
                                            ? 'Active'
                                            : 'Suspended'
                                    }
                                    color={tenant.is_active ? 'emerald' : 'red'}
                                />
                            </div>
                            <div className="mb-4 flex-grow">
                                <Link href={`/tenants/${tenant.id}`}>
                                    <h3 className="truncate text-lg font-bold text-ink hover:text-accent">
                                        {tenant.name}
                                    </h3>
                                </Link>
                                <span className="mt-1 inline-block rounded bg-surface-muted px-1.5 py-0.5 font-mono text-xs text-ink-subtle">
                                    {tenant.slug}
                                </span>
                                <div className="mt-3 flex items-center gap-1.5 text-xs text-ink-subtle">
                                    <GlobeAltIcon className="h-3.5 w-3.5" />
                                    {tenant.domains[0] || 'No domain'}
                                </div>
                                {tenant.subscription && (
                                    <StatusBadge
                                        label={tenant.subscription.plan_name}
                                        color={
                                            tenant.subscription.status ===
                                            'active'
                                                ? 'blue'
                                                : 'gray'
                                        }
                                        className="mt-2"
                                    />
                                )}
                            </div>
                            <div className="flex gap-2 border-t border-line pt-4 opacity-0 transition-opacity group-hover:opacity-100">
                                <Link
                                    href={`/tenants/${tenant.id}/edit`}
                                    className="flex flex-1 items-center justify-center gap-2 rounded-lg border border-line bg-surface py-2 text-xs font-semibold text-ink-muted transition-colors hover:border-accent/40 hover:bg-accent-soft hover:text-accent"
                                >
                                    <PencilSquareIcon className="h-4 w-4" />{' '}
                                    Edit
                                </Link>
                                <button
                                    onClick={() => setDelModal(tenant)}
                                    className="rounded-lg border border-line bg-surface p-2 text-ink-subtle transition-colors hover:border-red-200 hover:bg-critical-soft hover:text-critical-ink"
                                >
                                    <TrashIcon className="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
                {filtered.length === 0 && (
                    <EmptyState
                        icon={BuildingOfficeIcon}
                        title="No tenants found"
                    />
                )}
            </div>

            <Modal
                isOpen={!!delModal}
                onClose={() => setDelModal(null)}
                title="Delete Tenant"
            >
                <div className="text-center">
                    <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-critical-ink" />
                    <p className="text-ink-muted">
                        Delete <strong>{delModal?.name}</strong> permanently?
                    </p>
                </div>
                <div className="mt-6 flex justify-end gap-3">
                    <button
                        onClick={() => setDelModal(null)}
                        className="rounded-lg border border-line-strong bg-surface px-4 py-2 text-sm font-medium text-ink-muted transition-colors hover:bg-surface-hover"
                    >
                        Cancel
                    </button>
                    <button
                        onClick={handleDelete}
                        disabled={processing}
                        className="rounded-lg bg-critical px-4 py-2 text-sm font-semibold text-ink-inverse transition-colors hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {processing ? 'Deleting...' : 'Delete'}
                    </button>
                </div>
            </Modal>
        </SuperAdminLayout>
    );
}
