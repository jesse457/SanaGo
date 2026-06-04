import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    ShieldCheckIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormTextarea from '@/Components/FormTextarea';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Role {
    id: number;
    name: string;
    slug: string;
    description: string;
    permissions: string[];
    user_count: number;
}

interface PermissionItem {
    slug: string;
    label: string;
}

interface Props {
    roles: Role[];
    available_permissions: Record<string, PermissionItem[]>;
}

export default function RoleManagement({
    roles,
    available_permissions,
}: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<
        'add' | 'edit' | 'delete' | null
    >(null);
    const [selectedItem, setSelectedItem] = useState<Role | null>(null);

    // --- Inertia Form ---
    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
        clearErrors,
    } = useForm({
        name: '',
        slug: '',
        description: '',
        permissions: [] as string[],
    });

    const openModal = (mode: 'add' | 'edit' | 'delete', role?: Role) => {
        setModalMode(mode);
        setSelectedItem(role || null);
        clearErrors();

        if (mode === 'edit' && role) {
            setData({
                name: role.name,
                slug: role.slug,
                description: role.description,
                permissions: role.permissions,
            });
        } else {
            reset();
        }

        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setModalMode(null);
        setSelectedItem(null);
        reset();
    };

    const togglePermission = (permissionSlug: string) => {
        const current = data.permissions;
        setData(
            'permissions',
            current.includes(permissionSlug)
                ? current.filter((p) => p !== permissionSlug)
                : [...current, permissionSlug],
        );
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (modalMode === 'add') {
            post(route('roles.store'), { onSuccess: () => closeModal() });
        } else if (modalMode === 'edit' && selectedItem) {
            put(route('roles.update', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        } else if (modalMode === 'delete' && selectedItem) {
            destroy(route('roles.destroy', selectedItem.id), {
                onSuccess: () => closeModal(),
            });
        }
    };

    const filteredRoles = useMemo(() => {
        const q = searchQuery.toLowerCase();

        return roles.filter(
            (r) =>
                r.name.toLowerCase().includes(q) ||
                r.slug.toLowerCase().includes(q),
        );
    }, [roles, searchQuery]);

    return (
        <AppLayout
            headerTitle={t('page.roles_index.title')}
            headerSubtitle={t('page.roles_index.subtitle')}
        >
            <Head title={t('page.roles_index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        {
                            label: t('page.roles_index.breadcrumb_roles'),
                        },
                    ]}
                />

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder={t(
                                'page.roles_index.search_placeholder',
                            )}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                        />
                    </div>

                    <button
                        onClick={() => openModal('add')}
                        className="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" />{' '}
                        {t('page.roles_index.add_role')}
                    </button>
                </div>

                {/* Grid */}
                {filteredRoles.length === 0 ? (
                    <EmptyState icon={ShieldCheckIcon} title="No roles found" />
                ) : (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {filteredRoles.map((role) => (
                            <div
                                key={role.id}
                                className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="rounded-lg bg-blue-50 p-3 text-blue-600">
                                        <ShieldCheckIcon className="h-6 w-6" />
                                    </div>
                                    <div className="flex items-center gap-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-500 dark:bg-neutral-700 dark:text-neutral-500">
                                        <UserGroupIcon className="h-3.5 w-3.5" />{' '}
                                        {role.user_count}{' '}
                                        {t('page.roles_index.users')}
                                    </div>
                                </div>

                                <div className="mb-4 flex-grow">
                                    <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {role.name}
                                    </h3>
                                    <span className="mt-1 inline-block rounded bg-gray-50 px-1.5 py-0.5 font-mono text-xs font-semibold text-gray-400 dark:bg-neutral-700 dark:text-neutral-500">
                                        {role.slug}
                                    </span>
                                    <p className="mt-3 line-clamp-2 text-sm text-gray-500 dark:text-neutral-500">
                                        {role.description}
                                    </p>
                                </div>

                                <div className="mb-6">
                                    <p className="mb-2 text-xs font-semibold tracking-wider text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.roles_index.key_permissions')}
                                    </p>
                                    <div className="flex flex-wrap gap-1.5">
                                        {role.permissions
                                            .slice(0, 3)
                                            .map((p) => (
                                                <StatusBadge
                                                    key={p}
                                                    label={p.replace(/-/g, ' ')}
                                                    color="blue"
                                                />
                                            ))}
                                        {role.permissions.length > 3 && (
                                            <StatusBadge
                                                label={t(
                                                    'page.roles_index.more_count',
                                                    {
                                                        count:
                                                            role.permissions
                                                                .length - 3,
                                                    },
                                                )}
                                                color="gray"
                                            />
                                        )}
                                    </div>
                                </div>

                                <div className="flex gap-2 border-t border-gray-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100 dark:border-neutral-700">
                                    <button
                                        onClick={() => openModal('edit', role)}
                                        className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gray-50 py-2 text-xs font-semibold text-gray-600 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-400"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />{' '}
                                        {t('page.roles_index.edit_role')}
                                    </button>
                                    <button
                                        onClick={() =>
                                            openModal('delete', role)
                                        }
                                        className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:bg-neutral-700 dark:text-neutral-500 dark:hover:bg-red-900/20"
                                    >
                                        <TrashIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* --- MODAL --- */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={`${modalMode === 'add' ? t('page.roles_index.add') : modalMode === 'edit' ? t('page.roles_index.edit') : t('page.roles_index.confirm_delete')} ${t('page.roles_index.role')}`}
                maxWidth="max-w-2xl"
            >
                {modalMode === 'delete' ? (
                    <div>
                        <div className="py-4 text-center">
                            <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-red-600" />
                            <p className="mb-2 text-gray-700 dark:text-neutral-300">
                                {t('page.roles_index.delete_confirm', {
                                    name: selectedItem?.name || '',
                                })}
                            </p>
                            {(errors as Record<string, string>).error && (
                                <p className="mt-2 text-sm text-red-500">
                                    {(errors as Record<string, string>).error}
                                </p>
                            )}
                        </div>
                        <div className="flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-neutral-700">
                            <button
                                type="button"
                                onClick={closeModal}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="button"
                                onClick={handleSubmit}
                                disabled={processing}
                                className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700 disabled:opacity-50"
                            >
                                {processing
                                    ? t('common.processing')
                                    : t('page.roles_index.delete_role')}
                            </button>
                        </div>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-2 gap-4">
                            <FormInput
                                name="name"
                                label={t('page.roles_index.role_name')}
                                required
                                value={data.name}
                                onChange={(v) => setData('name', v)}
                                errors={errors}
                                className="col-span-2"
                            />
                            <FormTextarea
                                name="description"
                                label={t('page.roles_index.description')}
                                rows={2}
                                value={data.description}
                                onChange={(v) => setData('description', v)}
                                errors={errors}
                                className="col-span-2"
                            />
                        </div>

                        <div className="border-t border-gray-100 pt-6 dark:border-neutral-700">
                            <h4 className="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                <ShieldCheckIcon className="h-4 w-4 text-blue-600" />{' '}
                                {t('page.roles_index.assign_permissions')}
                            </h4>
                            <div className="space-y-6">
                                {Object.entries(available_permissions).map(
                                    ([category, perms]) => (
                                        <div key={category}>
                                            <h5 className="mb-3 text-xs font-bold tracking-wider text-gray-400 uppercase dark:text-neutral-500">
                                                {category}
                                            </h5>
                                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                {perms.map((perm) => (
                                                    <label
                                                        key={perm.slug}
                                                        className={`flex cursor-pointer items-start rounded-lg border p-3 transition-all ${data.permissions.includes(perm.slug) ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-700'}`}
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.permissions.includes(
                                                                perm.slug,
                                                            )}
                                                            onChange={() =>
                                                                togglePermission(
                                                                    perm.slug,
                                                                )
                                                            }
                                                            className="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600"
                                                        />
                                                        <div className="ml-3 text-sm">
                                                            <span
                                                                className={`block font-medium ${data.permissions.includes(perm.slug) ? 'text-blue-900' : 'text-gray-900 dark:text-neutral-100'}`}
                                                            >
                                                                {perm.label}
                                                            </span>
                                                            <span className="font-mono text-xs text-gray-500 dark:text-neutral-500">
                                                                {perm.slug}
                                                            </span>
                                                        </div>
                                                    </label>
                                                ))}
                                            </div>
                                        </div>
                                    ),
                                )}
                            </div>
                        </div>

                        <div className="flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-neutral-700">
                            <button
                                type="button"
                                onClick={closeModal}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing
                                    ? t('common.processing')
                                    : modalMode === 'add'
                                      ? t('page.roles_index.create_role')
                                      : t('page.roles_index.save_changes')}
                            </button>
                        </div>
                    </form>
                )}
            </Modal>
        </AppLayout>
    );
}
