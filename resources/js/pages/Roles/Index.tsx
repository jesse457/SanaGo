import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    ShieldCheckIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    ExclamationTriangleIcon,
    CheckIcon,
    XMarkIcon,
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
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
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
    const [expandedCategories, setExpandedCategories] = useState<
        Record<string, boolean>
    >({});

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
        } else if (mode === 'add') {
            reset();
            // Expand all categories by default when adding
            const allExpanded: Record<string, boolean> = {};
            Object.keys(available_permissions).forEach((cat) => {
                allExpanded[cat] = true;
            });
            setExpandedCategories(allExpanded);
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

    const toggleCategory = (category: string, perms: PermissionItem[]) => {
        const slugs = perms.map((p) => p.slug);
        const allSelected = slugs.every((s) => data.permissions.includes(s));

        if (allSelected) {
            setData(
                'permissions',
                data.permissions.filter((p) => !slugs.includes(p)),
            );
        } else {
            const toAdd = slugs.filter((s) => !data.permissions.includes(s));
            setData('permissions', [...data.permissions, ...toAdd]);
        }
    };

    const toggleExpandCategory = (cat: string) => {
        setExpandedCategories((prev) => ({ ...prev, [cat]: !prev[cat] }));
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

    const totalPermissions = Object.values(available_permissions).flat().length;

    return (
        <AppLayout
            headerTitle={t('page.roles_index.title')}
            headerSubtitle={t('page.roles_index.subtitle')}
        >
            <Head title={t('page.roles_index.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[{ label: t('page.roles_index.breadcrumb_roles') }]}
                />

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                        <input
                            type="text"
                            placeholder={t(
                                'page.roles_index.search_placeholder',
                            )}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-ink transition-all placeholder:text-ink-subtle focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>
                    <button
                        onClick={() => openModal('add')}
                        className="flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-ink-inverse shadow-surface transition-colors hover:bg-accent-hover"
                    >
                        <PlusIcon className="h-4 w-4" />{' '}
                        {t('page.roles_index.add_role')}
                    </button>
                </div>

                {/* ─── ROLES TABLE ─── */}
                {filteredRoles.length === 0 ? (
                    <EmptyState icon={ShieldCheckIcon} title="No roles found" />
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('page.roles_index.role_name')}
                                </TableHeaderCell>
                                <TableHeaderCell>Slug</TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.roles_index.users')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.roles_index.key_permissions')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('common.actions')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filteredRoles.map((role) => (
                                <TableRow key={role.id}>
                                    <TableCell>
                                        <div className="flex items-center gap-3">
                                            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-accent-soft text-accent">
                                                <ShieldCheckIcon className="h-5 w-5" />
                                            </span>
                                            <div className="min-w-0">
                                                <p className="text-[13px] font-semibold text-ink">
                                                    {role.name}
                                                </p>
                                                <p className="mt-0.5 line-clamp-1 max-w-[200px] text-xs text-ink-subtle">
                                                    {role.description}
                                                </p>
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <code className="rounded bg-surface-inset px-2 py-1 text-xs font-medium text-ink-muted">
                                            {role.slug}
                                        </code>
                                    </TableCell>
                                    <TableCell>
                                        <span className="inline-flex items-center gap-1.5 text-ink-muted">
                                            <UserGroupIcon className="h-4 w-4 text-ink-subtle" />
                                            <span className="font-medium tabular-nums">
                                                {role.user_count}
                                            </span>
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex flex-wrap gap-1">
                                            {role.permissions
                                                .slice(0, 3)
                                                .map((p) => (
                                                    <StatusBadge
                                                        key={p}
                                                        label={p.replace(
                                                            /-/g,
                                                            ' ',
                                                        )}
                                                        color="blue"
                                                    />
                                                ))}
                                            {role.permissions.length > 3 && (
                                                <StatusBadge
                                                    label={`+${role.permissions.length - 3}`}
                                                    color="gray"
                                                />
                                            )}
                                            {role.permissions.length === 0 && (
                                                <span className="text-xs text-ink-subtle">
                                                    —
                                                </span>
                                            )}
                                        </div>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1.5">
                                            <button
                                                onClick={() =>
                                                    openModal('edit', role)
                                                }
                                                className="cursor-pointer rounded-lg p-2 text-ink-subtle transition-colors hover:bg-accent-soft hover:text-accent"
                                                title={t(
                                                    'page.roles_index.edit_role',
                                                )}
                                            >
                                                <PencilSquareIcon className="h-4 w-4" />
                                            </button>
                                            <button
                                                onClick={() =>
                                                    openModal('delete', role)
                                                }
                                                className="cursor-pointer rounded-lg p-2 text-ink-subtle transition-colors hover:bg-critical-soft hover:text-red-600"
                                                title={t(
                                                    'page.roles_index.delete_role',
                                                )}
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                {/* Stats summary */}
                <div className="flex items-center gap-4 text-xs text-ink-subtle">
                    <span>
                        {roles.length} {t('page.roles_index.roles')}
                    </span>
                    <span>·</span>
                    <span>
                        {totalPermissions}{' '}
                        {t('page.roles_index.total_permissions')}
                    </span>
                </div>
            </div>

            {/* ─── MODAL ─── */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={`${modalMode === 'add' ? t('page.roles_index.add') : modalMode === 'edit' ? t('page.roles_index.edit') : t('page.roles_index.confirm_delete')} ${t('page.roles_index.role')}`}
                maxWidth="max-w-3xl"
            >
                {modalMode === 'delete' ? (
                    <div>
                        <div className="py-4 text-center">
                            <ExclamationTriangleIcon className="mx-auto mb-4 h-12 w-12 text-critical-ink" />
                            <p className="mb-2 text-ink-muted">
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
                        <div className="flex justify-end gap-3 border-t border-line pt-6">
                            <button
                                type="button"
                                onClick={closeModal}
                                className="cursor-pointer rounded-lg border border-line-strong bg-surface px-4 py-2 text-sm font-medium text-ink-muted transition-colors hover:bg-surface-hover"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="button"
                                onClick={handleSubmit}
                                disabled={processing}
                                className="cursor-pointer rounded-lg bg-critical px-4 py-2 text-sm font-medium text-ink-inverse shadow-surface transition-colors hover:bg-red-700 disabled:opacity-50"
                            >
                                {processing
                                    ? t('common.processing')
                                    : t('page.roles_index.delete_role')}
                            </button>
                        </div>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Role name & description */}
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

                        {/* ─── PERMISSIONS TABLE BY CATEGORY ─── */}
                        <div className="border-t border-line pt-6">
                            <h4 className="mb-4 flex items-center gap-2 text-sm font-semibold text-ink">
                                <ShieldCheckIcon className="h-4 w-4 text-accent-ink" />
                                {t('page.roles_index.assign_permissions')}
                                <span className="ml-auto text-xs font-normal text-ink-subtle">
                                    {data.permissions.length} /{' '}
                                    {totalPermissions}{' '}
                                    {t('page.roles_index.selected')}
                                </span>
                            </h4>

                            <div className="max-h-[50vh] overflow-y-auto rounded-xl border border-line">
                                {Object.entries(available_permissions).map(
                                    ([category, perms]) => {
                                        const selectedCount = perms.filter(
                                            (p) =>
                                                data.permissions.includes(
                                                    p.slug,
                                                ),
                                        ).length;
                                        const allSelected =
                                            selectedCount === perms.length;
                                        const someSelected =
                                            selectedCount > 0 && !allSelected;
                                        const isExpanded =
                                            expandedCategories[category] ??
                                            true;

                                        return (
                                            <div
                                                key={category}
                                                className="border-b border-line last:border-0"
                                            >
                                                {/* Category header */}
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        toggleExpandCategory(
                                                            category,
                                                        )
                                                    }
                                                    className="flex w-full cursor-pointer items-center gap-3 bg-surface-muted px-4 py-2.5 text-left transition-colors hover:bg-surface-hover"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        checked={allSelected}
                                                        ref={(el) => {
                                                            if (el) {
                                                                el.indeterminate =
                                                                    someSelected;
                                                            }
                                                        }}
                                                        onChange={(e) => {
                                                            e.stopPropagation();
                                                            toggleCategory(
                                                                category,
                                                                perms,
                                                            );
                                                        }}
                                                        className="h-4 w-4 rounded border-line-strong text-accent-ink"
                                                    />
                                                    <span className="flex-1 text-xs font-bold tracking-wider text-ink-muted uppercase">
                                                        {category}
                                                    </span>
                                                    <span className="text-[10px] font-medium text-ink-subtle">
                                                        {selectedCount}/
                                                        {perms.length}
                                                    </span>
                                                </button>

                                                {/* Permission rows table */}
                                                {isExpanded && (
                                                    <Table bare>
                                                        <TableHeader>
                                                            <TableRow className="hover:bg-transparent">
                                                                <TableHeaderCell className="w-10">
                                                                    <span className="sr-only">
                                                                        Select
                                                                    </span>
                                                                </TableHeaderCell>
                                                                <TableHeaderCell>
                                                                    {t(
                                                                        'page.roles_index.permission',
                                                                    )}
                                                                </TableHeaderCell>
                                                                <TableHeaderCell>
                                                                    Slug
                                                                </TableHeaderCell>
                                                                <TableHeaderCell
                                                                    numeric
                                                                    className="w-12"
                                                                >
                                                                    {t(
                                                                        'page.roles_index.granted',
                                                                    )}
                                                                </TableHeaderCell>
                                                            </TableRow>
                                                        </TableHeader>
                                                        <TableBody>
                                                            {perms.map(
                                                                (perm) => {
                                                                    const isChecked =
                                                                        data.permissions.includes(
                                                                            perm.slug,
                                                                        );

                                                                    return (
                                                                        <TableRow
                                                                            key={
                                                                                perm.slug
                                                                            }
                                                                            className={`cursor-pointer ${isChecked ? 'bg-accent-soft hover:bg-accent-soft' : ''}`}
                                                                            onClick={() =>
                                                                                togglePermission(
                                                                                    perm.slug,
                                                                                )
                                                                            }
                                                                        >
                                                                            <TableCell className="w-10">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    checked={
                                                                                        isChecked
                                                                                    }
                                                                                    onChange={() =>
                                                                                        togglePermission(
                                                                                            perm.slug,
                                                                                        )
                                                                                    }
                                                                                    className="h-4 w-4 rounded border-line-strong accent-accent"
                                                                                />
                                                                            </TableCell>
                                                                            <TableCell
                                                                                className={`text-xs ${isChecked ? 'font-semibold text-accent-ink' : 'text-ink-muted'}`}
                                                                            >
                                                                                {
                                                                                    perm.label
                                                                                }
                                                                            </TableCell>
                                                                            <TableCell>
                                                                                <code className="text-[10px] text-ink-subtle">
                                                                                    {
                                                                                        perm.slug
                                                                                    }
                                                                                </code>
                                                                            </TableCell>
                                                                            <TableCell
                                                                                numeric
                                                                            >
                                                                                {isChecked ? (
                                                                                    <CheckIcon className="ml-auto h-4 w-4 text-accent" />
                                                                                ) : (
                                                                                    <XMarkIcon className="ml-auto h-4 w-4 text-ink-subtle" />
                                                                                )}
                                                                            </TableCell>
                                                                        </TableRow>
                                                                    );
                                                                },
                                                            )}
                                                        </TableBody>
                                                    </Table>
                                                )}
                                            </div>
                                        );
                                    },
                                )}
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex justify-end gap-3 border-t border-line pt-6">
                            <button
                                type="button"
                                onClick={closeModal}
                                className="cursor-pointer rounded-lg border border-line-strong bg-surface px-4 py-2 text-sm font-medium text-ink-muted transition-colors hover:bg-surface-hover"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="cursor-pointer rounded-lg bg-accent px-4 py-2 text-sm font-medium text-ink-inverse shadow-surface transition-colors hover:bg-accent-hover disabled:opacity-50"
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
