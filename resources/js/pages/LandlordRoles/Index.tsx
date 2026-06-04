import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    ShieldCheckIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Role {
    id: number;
    name: string;
    permissions: string[];
    users_count: number;
}

export default function LandlordRolesIndex({ roles }: { roles: Role[] }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<Role | null>(null);
    const { delete: destroy, processing } = useForm();
    const filtered = useMemo(
        () =>
            roles.filter((r) =>
                r.name.toLowerCase().includes(search.toLowerCase()),
            ),
        [roles, search],
    );
    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/landlord-roles/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Roles & Permissions"
            headerSubtitle="Manage landlord organization roles"
        >
            <Head title="Roles & Permissions" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Roles & Permissions' },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="w-full sm:w-72">
                        <FormInput
                            name="search"
                            placeholder="Search roles..."
                            icon={MagnifyingGlassIcon}
                            value={search}
                            onChange={setSearch}
                        />
                    </div>
                    <Link
                        href="/landlord-roles/create"
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> Create Role
                    </Link>
                </div>

                {filtered.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filtered.map((role) => (
                            <div
                                key={role.id}
                                className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="rounded-lg bg-blue-50 p-3 text-blue-600">
                                        <ShieldCheckIcon className="h-6 w-6" />
                                    </div>
                                    <div className="flex items-center gap-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-500 dark:bg-neutral-700">
                                        <UserGroupIcon className="h-3.5 w-3.5" />{' '}
                                        {role.users_count} users
                                    </div>
                                </div>
                                <div className="mb-4 flex-grow">
                                    <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {role.name}
                                    </h3>
                                    <div className="mt-3 mb-4">
                                        <p className="mb-2 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            Key Permissions
                                        </p>
                                        <div className="flex flex-wrap gap-1.5">
                                            {role.permissions
                                                .slice(0, 4)
                                                .map((p) => (
                                                    <StatusBadge
                                                        key={p}
                                                        label={p.replace(/-/g, ' ')}
                                                        color="blue"
                                                    />
                                                ))}
                                            {role.permissions.length > 4 && (
                                                <StatusBadge
                                                    label={`+${role.permissions.length - 4} more`}
                                                    color="gray"
                                                />
                                            )}
                                        </div>
                                    </div>
                                </div>
                                <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                                    <Link
                                        href={`/landlord-roles/${role.id}/edit`}
                                        className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />{' '}
                                        Edit
                                    </Link>
                                    <button
                                        onClick={() => setDelModal(role)}
                                        className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:bg-neutral-700"
                                    >
                                        <TrashIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <EmptyState
                        icon={ShieldCheckIcon}
                        title="No roles found"
                        description="Try adjusting your search or create a new role."
                        action={{
                            label: 'Create Role',
                            href: '/landlord-roles/create',
                        }}
                    />
                )}
            </div>

            <Modal
                isOpen={delModal !== null}
                onClose={() => setDelModal(null)}
                title="Delete Role"
            >
                <div className="text-center">
                    <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                    <p className="text-gray-700 dark:text-neutral-300">
                        Delete role <strong>{delModal?.name}</strong>?
                    </p>
                </div>
                <div className="mt-6 flex justify-end gap-3">
                    <button
                        onClick={() => setDelModal(null)}
                        className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    >
                        {t('common.cancel')}
                    </button>
                    <button
                        onClick={handleDelete}
                        disabled={processing}
                        className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                    >
                        {processing ? 'Deleting...' : 'Delete'}
                    </button>
                </div>
            </Modal>
        </SuperAdminLayout>
    );
}
