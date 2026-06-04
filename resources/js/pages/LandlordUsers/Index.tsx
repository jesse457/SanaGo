import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    UserGroupIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
    EnvelopeIcon,
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

interface UserItem {
    id: number;
    name: string;
    email: string;
    roles: string[];
    is_active: boolean;
    created_at: string;
}

const ROLE_COLORS: Record<string, string> = {
    'super-admin':
        'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    'tenant-manager':
        'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    'billing-manager':
        'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
    'support-agent':
        'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
    'sales-rep':
        'bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400',
};

export default function LandlordUsersIndex({ users }: { users: UserItem[] }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<UserItem | null>(null);
    const { delete: destroy, processing } = useForm();
    const filtered = useMemo(
        () =>
            users.filter(
                (u) =>
                    u.name.toLowerCase().includes(search.toLowerCase()) ||
                    u.email.toLowerCase().includes(search.toLowerCase()),
            ),
        [users, search],
    );
    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/landlord-users/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Staff Users"
            headerSubtitle="Manage landlord organization members"
        >
            <Head title="Staff Users" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Staff Users' },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="w-full sm:w-72">
                        <FormInput
                            name="search"
                            placeholder="Search users..."
                            icon={MagnifyingGlassIcon}
                            value={search}
                            onChange={setSearch}
                        />
                    </div>
                    <Link
                        href="/landlord-users/create"
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> Add User
                    </Link>
                </div>

                {filtered.length > 0 ? (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filtered.map((user) => (
                            <div
                                key={user.id}
                                className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div
                                        className={`rounded-lg p-3 ${user.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500'}`}
                                    >
                                        <UserGroupIcon className="h-6 w-6" />
                                    </div>
                                    <StatusBadge
                                        label={
                                            user.is_active
                                                ? 'Active'
                                                : 'Inactive'
                                        }
                                        color={
                                            user.is_active ? 'emerald' : 'red'
                                        }
                                    />
                                </div>
                                <div className="mb-4 flex-grow">
                                    <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {user.name}
                                    </h3>
                                    <p className="mt-1 flex items-center gap-1.5 text-sm text-gray-500 dark:text-neutral-400">
                                        <EnvelopeIcon className="h-3.5 w-3.5" />
                                        {user.email}
                                    </p>
                                    <div className="mt-3 flex flex-wrap gap-1.5">
                                        {(user.roles || []).map((r) => (
                                            <span
                                                key={r}
                                                className={`rounded border px-2 py-0.5 text-[10px] font-bold uppercase ${ROLE_COLORS[r] || 'bg-gray-50 text-gray-600'}`}
                                            >
                                                {r}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                                <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                                    <Link
                                        href={`/landlord-users/${user.id}/edit`}
                                        className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />{' '}
                                        Edit
                                    </Link>
                                    <button
                                        onClick={() => setDelModal(user)}
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
                        icon={UserGroupIcon}
                        title="No staff users found"
                    />
                )}
            </div>

            <Modal
                isOpen={delModal !== null}
                onClose={() => setDelModal(null)}
                title="Delete User"
            >
                <div className="text-center">
                    <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                    <p className="text-gray-700 dark:text-neutral-300">
                        Delete <strong>{delModal?.name}</strong>?
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
