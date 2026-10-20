import {
    PlusIcon,
    MagnifyingGlassIcon,
    TrashIcon,
    PencilSquareIcon,
    ExclamationTriangleIcon,
    UserGroupIcon,
    CheckBadgeIcon,
    ShieldCheckIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, Link, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import { toast } from 'sonner';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination'; // Make sure path matches your file structure
import StatCard from '@/Components/StatCard';
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

interface Shift {
    id?: number;
    shift_date: string;
    shift_type: string;
    start_time?: string | null;
    end_time?: string | null;
}

interface User {
    id: number;
    name: string;
    email: string;
    avatar_url: string | null;
    is_active: boolean;
    phone_number: string;
    department_id: number | null;
    department?: { name: string };
    roles?: { id: number; name: string }[];
    shifts?: Shift[];
}

interface Role {
    id: number;
    name: string;
}

interface PaginatedUsers {
    data: User[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    from: number;
    to: number;
    total: number;
}

interface Props {
    users: PaginatedUsers;
    departments: { id: number; name: string }[];
    roles: Role[];
    stats: {
        total: number;
        active: number;
    };
    filters?: {
        search?: string;
        role?: string;
    };
}

const roleBadgeColor = (roleName?: string): string => {
    if (roleName === 'admin') {
        return 'purple';
    }

    if (roleName === 'doctor') {
        return 'blue';
    }

    if (roleName === 'reception') {
        return 'emerald';
    }

    if (roleName === 'lab_tech') {
        return 'amber';
    }

    if (roleName === 'pharma') {
        return 'emerald';
    }

    return 'gray';
};

export default function UserManagement({
    users,
    departments,
    roles,
    stats,
    filters,
}: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState(filters?.search || '');
    const [activeTab, setActiveTab] = useState(filters?.role || 'all');
    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<'edit' | 'delete'>('edit');
    const [selectedItem, setSelectedItem] = useState<User | null>(null);
    const [searchTimeout, setSearchTimeout] = useState<NodeJS.Timeout | null>(
        null,
    );

    const {
        data,
        setData,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
        clearErrors,
    } = useForm({
        name: '',
        email: '',
        role: '',
        phone_number: '',
        department_id: '' as string | number,
        is_active: true,
        shifts: [] as Shift[],
    });

    const openEdit = (user: User) => {
        setSelectedItem(user);
        setModalMode('edit');
        clearErrors();
        setData({
            name: user.name,
            email: user.email,
            role: user.roles?.[0]?.name || '',
            phone_number: user.phone_number,
            department_id: user.department_id || '',
            is_active: user.is_active,
            shifts: (user.shifts || []).map((s) => ({
                shift_date: s.shift_date,
                shift_type: s.shift_type,
                start_time: s.start_time || '',
                end_time: s.end_time || '',
            })),
        });
        setModalOpen(true);
    };

    const openDelete = (user: User) => {
        setSelectedItem(user);
        setModalMode('delete');
        clearErrors();
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setSelectedItem(null);
        reset();
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (!selectedItem) {
            return;
        }

        put(route('users.update', selectedItem.id), {
            onSuccess: () => {
                closeModal();
                toast.success(t('page.users_index.updated_success'));
            },
        });
    };

    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (!selectedItem) {
            return;
        }

        destroy(route('users.destroy', selectedItem.id), {
            onSuccess: () => {
                closeModal();
                toast.success(t('page.users_index.deleted_success'));
            },
        });
    };

    // Handle debounced search changes via Inertia visits
    const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const val = e.target.value;
        setSearchQuery(val);

        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        const timeout = setTimeout(() => {
            router.get(
                route('users.index'),
                { search: val, role: activeTab },
                { preserveState: true, replace: true },
            );
        }, 300);

        setSearchTimeout(timeout);
    };

    // Handle tab switching via direct Inertia visits
    const handleTabChange = (roleName: string) => {
        setActiveTab(roleName);
        router.get(
            route('users.index'),
            { search: searchQuery, role: roleName },
            { preserveState: true, replace: true },
        );
    };

    const statsInfo = useMemo(
        () => ({
            total: stats.total,
            active: stats.active,
            rolesCount: roles.length,
        }),
        [stats, roles],
    );

    return (
        <AppLayout
            headerTitle={t('page.users_index.title')}
            headerSubtitle={t('page.users_index.subtitle')}
        >
            <Head title={t('page.users_index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[{ label: t('page.users_index.breadcrumb_users') }]}
                />

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <StatCard
                        icon={UserGroupIcon}
                        label={t('page.users_index.total_users')}
                        value={statsInfo.total}
                        color="blue"
                    />
                    <StatCard
                        icon={CheckBadgeIcon}
                        label={t('page.users_index.active_users')}
                        value={statsInfo.active}
                        color="emerald"
                    />
                    <StatCard
                        icon={ShieldCheckIcon}
                        label={t('page.users_index.roles')}
                        value={statsInfo.rolesCount}
                        color="purple"
                    />
                </div>

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex w-fit rounded-lg bg-surface-inset p-1">
                        <button
                            onClick={() => handleTabChange('all')}
                            className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide uppercase transition-all ${activeTab === 'all' ? 'bg-surface text-accent-ink shadow-surface' : 'text-ink-subtle hover:text-ink'}`}
                        >
                            {t('page.users_index.all')}
                        </button>
                        {roles.map((role) => (
                            <button
                                key={role.id}
                                onClick={() => handleTabChange(role.name)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide uppercase transition-all ${activeTab === role.name ? 'bg-surface text-accent-ink shadow-surface' : 'text-ink-subtle hover:text-ink'}`}
                            >
                                {role.name.replace('_', ' ')}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                            <input
                                type="text"
                                value={searchQuery}
                                placeholder={t(
                                    'page.users_index.search_placeholder',
                                )}
                                className="rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-ink outline-none focus:ring-2 focus:ring-blue-500"
                                onChange={handleSearchChange}
                            />
                        </div>
                        <Link
                            href={route('users.create')}
                            className="flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-sm font-bold text-ink-inverse shadow-surface transition-all hover:bg-accent-hover"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.users_index.add_user')}
                        </Link>
                    </div>
                </div>

                {users.data.length > 0 ? (
                    <div className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow className="hover:bg-transparent">
                                    <TableHeaderCell>
                                        {t('page.users_index.table_user')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.users_index.table_role')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.users_index.table_dept')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.users_index.table_status')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        <span className="sr-only">Actions</span>
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.data.map((user) => (
                                    <TableRow key={user.id} className="group">
                                        <TableCell>
                                            <div className="flex items-center gap-3">
                                                <div className="h-9 w-9 overflow-hidden rounded-md">
                                                    {user.avatar_url ? (
                                                        <img
                                                            src={
                                                                user.avatar_url
                                                            }
                                                            alt=""
                                                            className="h-full w-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="flex h-full w-full items-center justify-center bg-accent text-xs font-semibold text-ink-inverse">
                                                            {user.name.charAt(
                                                                0,
                                                            )}
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="min-w-0">
                                                    <div className="text-[13px] font-semibold text-ink">
                                                        {user.name}
                                                    </div>
                                                    <div className="text-xs text-ink-subtle">
                                                        {user.email}
                                                    </div>
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <StatusBadge
                                                label={
                                                    user.roles?.[0]?.name?.replace(
                                                        '_',
                                                        ' ',
                                                    ) ||
                                                    t('page.users_index.staff')
                                                }
                                                color={
                                                    roleBadgeColor(
                                                        user.roles?.[0]?.name,
                                                    ) as any
                                                }
                                            />
                                        </TableCell>
                                        <TableCell muted>
                                            {user.department?.name || '—'}
                                        </TableCell>
                                        <TableCell>
                                            <StatusBadge
                                                label={
                                                    user.is_active
                                                        ? t(
                                                              'page.users_index.active',
                                                          )
                                                        : t(
                                                              'page.users_index.disabled',
                                                          )
                                                }
                                                color={
                                                    user.is_active
                                                        ? 'emerald'
                                                        : 'gray'
                                                }
                                            />
                                        </TableCell>
                                        <TableCell numeric>
                                            <div className="flex justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                                <button
                                                    onClick={() =>
                                                        openEdit(user)
                                                    }
                                                    className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-accent-soft hover:text-accent"
                                                >
                                                    <PencilSquareIcon className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        openDelete(user)
                                                    }
                                                    className="rounded-lg p-2 text-ink-subtle transition-colors hover:bg-critical-soft hover:text-red-600"
                                                >
                                                    <TrashIcon className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>

                        {/* Pagination Component render */}
                        <Pagination
                            links={users.links}
                            meta={{
                                from: users.from,
                                to: users.to,
                                total: users.total,
                            }}
                        />
                    </div>
                ) : (
                    <EmptyState
                        icon={UserGroupIcon}
                        title={t('page.users_index.empty_state')}
                    />
                )}
            </div>

            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={
                    modalMode === 'delete'
                        ? t('page.users_index.delete_user')
                        : t('page.users_index.edit_user')
                }
            >
                {modalMode === 'delete' && selectedItem ? (
                    <form onSubmit={handleDelete} className="space-y-5">
                        <div className="text-center">
                            <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <ExclamationTriangleIcon className="h-6 w-6 text-critical-ink" />
                            </div>
                            <p className="mb-2 text-ink-muted">
                                {t('page.users_index.confirm_delete', {
                                    name: selectedItem.name,
                                })}
                            </p>
                            <p className="text-sm text-ink-subtle">
                                {t('page.users_index.cannot_undo')}
                            </p>
                            {(errors as any).error && (
                                <p className="mt-4 text-sm font-bold text-critical-ink">
                                    {(errors as any).error}
                                </p>
                            )}
                        </div>
                        <div className="flex justify-end gap-3 border-t border-line pt-4">
                            <button
                                type="button"
                                onClick={closeModal}
                                disabled={processing}
                                className="rounded-lg border border-line-strong bg-surface px-4 py-2 text-sm font-medium text-ink-muted transition-colors hover:bg-surface-hover disabled:opacity-50"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-critical px-4 py-2 text-sm font-medium text-ink-inverse transition-colors hover:bg-red-700 disabled:opacity-50"
                            >
                                {processing
                                    ? t('page.users_index.deleting')
                                    : t('page.users_index.delete_user')}
                            </button>
                        </div>
                    </form>
                ) : (
                    <form
                        onSubmit={handleSubmit}
                        className="max-h-[70vh] space-y-4 overflow-y-auto pr-1"
                    >
                        <FormInput
                            name="name"
                            label={t('page.users_index.full_name')}
                            value={data.name}
                            onChange={(val) => setData('name', val)}
                            errors={errors}
                            required
                        />

                        <FormInput
                            name="email"
                            label={t('page.users_index.email')}
                            type="email"
                            value={data.email}
                            onChange={(val) => setData('email', val)}
                            errors={errors}
                            required
                        />

                        <FormInput
                            name="phone_number"
                            label={t('page.users_index.phone')}
                            value={data.phone_number}
                            onChange={(val) => setData('phone_number', val)}
                            errors={errors}
                            required
                        />

                        <div className="grid grid-cols-2 gap-4">
                            <FormSelect
                                name="role"
                                label={t('page.users_index.role')}
                                value={data.role}
                                onChange={(val) => setData('role', val)}
                                errors={errors}
                                options={roles.map((r) => ({
                                    value: r.name,
                                    label: r.name.replace('_', ' '),
                                }))}
                                placeholder={t('page.users_index.select_role')}
                                required
                            />

                            <FormSelect
                                name="department_id"
                                label={t('page.users_index.department')}
                                value={data.department_id}
                                onChange={(val) =>
                                    setData('department_id', val)
                                }
                                errors={errors}
                                options={departments.map((d) => ({
                                    value: d.id,
                                    label: d.name,
                                }))}
                                placeholder={t('page.users_index.select_dept')}
                            />
                        </div>

                        <div className="flex items-center gap-3 rounded-xl border border-accent/30 bg-accent-soft p-4">
                            <input
                                type="checkbox"
                                id="is_active_edit"
                                checked={data.is_active}
                                onChange={(e) =>
                                    setData('is_active', e.target.checked)
                                }
                                className="h-5 w-5 rounded border-line-strong text-accent-ink focus:ring-blue-500"
                            />
                            <label
                                htmlFor="is_active_edit"
                                className="text-xs font-bold text-accent-ink"
                            >
                                {t('page.users_index.account_active')}
                            </label>
                        </div>

                        {/* Shifts Section */}
                        <div className="border-t border-line pt-4">
                            <div className="mb-3 flex items-center justify-between">
                                <label className="text-xs font-semibold text-ink-subtle uppercase">
                                    {t('page.users_index.assigned_shifts')}
                                </label>
                                <button
                                    type="button"
                                    onClick={() =>
                                        setData('shifts', [
                                            ...data.shifts,
                                            {
                                                shift_date: '',
                                                shift_type: 'morning',
                                                start_time: '',
                                                end_time: '',
                                            },
                                        ])
                                    }
                                    className="flex items-center gap-1 text-xs font-bold text-accent-ink hover:text-accent-ink"
                                >
                                    <PlusIcon className="h-3.5 w-3.5" />{' '}
                                    {t('page.users_index.add_shift')}
                                </button>
                            </div>
                            {data.shifts.length === 0 && (
                                <p className="text-xs text-ink-subtle italic">
                                    {t('page.users_index.no_shifts')}
                                </p>
                            )}
                            {data.shifts.map((shift, idx) => (
                                <div
                                    key={idx}
                                    className="mb-2 flex items-end gap-2 rounded-lg bg-surface-muted p-3"
                                >
                                    <div className="flex-1">
                                        <label className="text-[10px] font-semibold text-ink-subtle uppercase">
                                            {t('page.users_index.date')}
                                        </label>
                                        <input
                                            type="date"
                                            value={shift.shift_date}
                                            onChange={(e) =>
                                                setData(
                                                    'shifts',
                                                    data.shifts.map((s, i) =>
                                                        i === idx
                                                            ? {
                                                                  ...s,
                                                                  shift_date:
                                                                      e.target
                                                                          .value,
                                                              }
                                                            : s,
                                                    ),
                                                )
                                            }
                                            className="mt-0.5 w-full rounded border border-line bg-surface px-2 py-1.5 text-ink"
                                        />
                                    </div>
                                    <div className="flex-1">
                                        <label className="text-[10px] font-semibold text-ink-subtle uppercase">
                                            {t('page.users_index.type')}
                                        </label>
                                        <select
                                            value={shift.shift_type}
                                            onChange={(e) =>
                                                setData(
                                                    'shifts',
                                                    data.shifts.map((s, i) =>
                                                        i === idx
                                                            ? {
                                                                  ...s,
                                                                  shift_type:
                                                                      e.target
                                                                          .value,
                                                              }
                                                            : s,
                                                    ),
                                                )
                                            }
                                            className="mt-0.5 w-full rounded border border-line bg-surface px-2 py-1.5 text-ink"
                                        >
                                            <option value="morning">
                                                {t('page.users_index.morning')}
                                            </option>
                                            <option value="afternoon">
                                                {t(
                                                    'page.users_index.afternoon',
                                                )}
                                            </option>
                                            <option value="night">
                                                {t('page.users_index.night')}
                                            </option>
                                        </select>
                                    </div>
                                    <div className="w-20">
                                        <label className="text-[10px] font-semibold text-ink-subtle uppercase">
                                            {t('page.users_index.start')}
                                        </label>
                                        <input
                                            type="time"
                                            value={shift.start_time || ''}
                                            onChange={(e) =>
                                                setData(
                                                    'shifts',
                                                    data.shifts.map((s, i) =>
                                                        i === idx
                                                            ? {
                                                                  ...s,
                                                                  start_time:
                                                                      e.target
                                                                          .value,
                                                              }
                                                            : s,
                                                    ),
                                                )
                                            }
                                            className="mt-0.5 w-full rounded border border-line bg-surface px-2 py-1.5 text-ink"
                                        />
                                    </div>
                                    <div className="w-20">
                                        <label className="text-[10px] font-semibold text-ink-subtle uppercase">
                                            {t('page.users_index.end')}
                                        </label>
                                        <input
                                            type="time"
                                            value={shift.end_time || ''}
                                            onChange={(e) =>
                                                setData(
                                                    'shifts',
                                                    data.shifts.map((s, i) =>
                                                        i === idx
                                                            ? {
                                                                  ...s,
                                                                  end_time:
                                                                      e.target
                                                                          .value,
                                                              }
                                                            : s,
                                                    ),
                                                )
                                            }
                                            className="mt-0.5 w-full rounded border border-line bg-surface px-2 py-1.5 text-ink"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() =>
                                            setData(
                                                'shifts',
                                                data.shifts.filter(
                                                    (_, i) => i !== idx,
                                                ),
                                            )
                                        }
                                        className="mb-0.5 rounded-lg p-1.5 text-ink-subtle transition-colors hover:bg-critical-soft hover:text-red-600"
                                    >
                                        <TrashIcon className="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            ))}
                            {(errors as any)['shifts.0.shift_date'] && (
                                <p className="mt-1 text-xs font-bold text-red-500">
                                    {(errors as any)['shifts.0.shift_date']}
                                </p>
                            )}
                        </div>

                        <div className="flex gap-4 pt-2">
                            <button
                                type="button"
                                onClick={closeModal}
                                className="flex-1 rounded-xl border border-line bg-surface py-3 font-bold text-ink-subtle"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-[2] rounded-xl bg-accent py-3 font-bold text-ink-inverse hover:bg-accent-hover disabled:opacity-50"
                            >
                                {processing
                                    ? t('page.users_index.saving')
                                    : t('page.users_index.update_user')}
                            </button>
                        </div>
                    </form>
                )}
            </Modal>
        </AppLayout>
    );
}
