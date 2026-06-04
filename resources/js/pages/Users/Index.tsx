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
import { Head, useForm, Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState, useMemo } from 'react';
import { toast, Toaster } from 'sonner';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
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

interface Props {
    users: User[];
    departments: { id: number; name: string }[];
    roles: Role[];
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

export default function UserManagement({ users, departments, roles }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');
    const [activeTab, setActiveTab] = useState('all');
    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<'edit' | 'delete'>('edit');
    const [selectedItem, setSelectedItem] = useState<User | null>(null);

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

    const filteredUsers = useMemo(() => {
        let result = users;

        if (activeTab !== 'all') {
            result = result.filter((u) =>
                u.roles?.some((r) => r.name === activeTab),
            );
        }

        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            result = result.filter(
                (u) =>
                    u.name.toLowerCase().includes(q) ||
                    u.email.toLowerCase().includes(q),
            );
        }

        return result;
    }, [users, activeTab, searchQuery]);

    const stats = useMemo(
        () => ({
            total: users.length,
            active: users.filter((u) => u.is_active).length,
            rolesCount: roles.length,
        }),
        [users, roles],
    );

    return (
        <AppLayout
            headerTitle={t('page.users_index.title')}
            headerSubtitle={t('page.users_index.subtitle')}
        >
            <Head title={t('page.users_index.head_title')} />
            <Toaster position="top-right" richColors />

            <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.users_index.breadcrumb_users') },
                    ]}
                />

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <StatCard
                        icon={UserGroupIcon}
                        label={t('page.users_index.total_users')}
                        value={stats.total}
                        color="blue"
                    />
                    <StatCard
                        icon={CheckBadgeIcon}
                        label={t('page.users_index.active_users')}
                        value={stats.active}
                        color="emerald"
                    />
                    <StatCard
                        icon={ShieldCheckIcon}
                        label={t('page.users_index.roles')}
                        value={stats.rolesCount}
                        color="purple"
                    />
                </div>

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex w-fit rounded-lg bg-gray-100 p-1 dark:bg-neutral-800">
                        <button
                            onClick={() => setActiveTab('all')}
                            className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide uppercase transition-all ${activeTab === 'all' ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700' : 'text-gray-500 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-100'}`}
                        >
                            {t('page.users_index.all')}
                        </button>
                        {roles.map((role) => (
                            <button
                                key={role.id}
                                onClick={() => setActiveTab(role.name)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide uppercase transition-all ${activeTab === role.name ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700' : 'text-gray-500 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-100'}`}
                            >
                                {role.name.replace('_', ' ')}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t(
                                    'page.users_index.search_placeholder',
                                )}
                                className="rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                                onChange={(e) => setSearchQuery(e.target.value)}
                            />
                        </div>
                        <Link
                            href={route('users.create')}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.users_index.add_user')}
                        </Link>
                    </div>
                </div>

                {filteredUsers.length > 0 ? (
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <table className="w-full text-left">
                            <thead className="border-b border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800">
                                <tr>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_index.table_user')}
                                    </th>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_index.table_role')}
                                    </th>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_index.table_dept')}
                                    </th>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.users_index.table_status')}
                                    </th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                {filteredUsers.map((user) => (
                                    <tr
                                        key={user.id}
                                        className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                    >
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="h-10 w-10 overflow-hidden rounded-lg">
                                                    {user.avatar_url ? (
                                                        <img
                                                            src={
                                                                user.avatar_url
                                                            }
                                                            alt=""
                                                            className="h-full w-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-xs font-bold text-white">
                                                            {user.name.charAt(
                                                                0,
                                                            )}
                                                        </div>
                                                    )}
                                                </div>
                                                <div>
                                                    <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {user.name}
                                                    </div>
                                                    <div className="text-xs font-medium text-gray-400 dark:text-neutral-500">
                                                        {user.email}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
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
                                        </td>
                                        <td className="px-6 py-4 text-sm font-medium text-gray-500 dark:text-neutral-400">
                                            {user.department?.name || '—'}
                                        </td>
                                        <td className="px-6 py-4">
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
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                                <button
                                                    onClick={() =>
                                                        openEdit(user)
                                                    }
                                                    className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20"
                                                >
                                                    <PencilSquareIcon className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        openDelete(user)
                                                    }
                                                    className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                                >
                                                    <TrashIcon className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
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
                                <ExclamationTriangleIcon className="h-6 w-6 text-red-600" />
                            </div>
                            <p className="mb-2 text-gray-700 dark:text-neutral-300">
                                {t('page.users_index.confirm_delete', {
                                    name: selectedItem.name,
                                })}
                            </p>
                            <p className="text-sm text-gray-500 dark:text-neutral-400">
                                {t('page.users_index.cannot_undo')}
                            </p>
                            {(errors as any).error && (
                                <p className="mt-4 text-sm font-bold text-red-600">
                                    {(errors as any).error}
                                </p>
                            )}
                        </div>
                        <div className="flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <button
                                type="button"
                                onClick={closeModal}
                                disabled={processing}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700 disabled:opacity-50"
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

                        <div className="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                            <input
                                type="checkbox"
                                id="is_active_edit"
                                checked={data.is_active}
                                onChange={(e) =>
                                    setData('is_active', e.target.checked)
                                }
                                className="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <label
                                htmlFor="is_active_edit"
                                className="text-xs font-bold text-blue-900 dark:text-blue-300"
                            >
                                {t('page.users_index.account_active')}
                            </label>
                        </div>

                        {/* Shifts Section */}
                        <div className="border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <div className="mb-3 flex items-center justify-between">
                                <label className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
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
                                    className="flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700"
                                >
                                    <PlusIcon className="h-3.5 w-3.5" />{' '}
                                    {t('page.users_index.add_shift')}
                                </button>
                            </div>
                            {data.shifts.length === 0 && (
                                <p className="text-xs text-gray-400 italic dark:text-neutral-500">
                                    {t('page.users_index.no_shifts')}
                                </p>
                            )}
                            {data.shifts.map((shift, idx) => (
                                <div
                                    key={idx}
                                    className="mb-2 flex items-end gap-2 rounded-lg bg-gray-50 p-3 dark:bg-neutral-700/50"
                                >
                                    <div className="flex-1">
                                        <label className="text-[10px] font-semibold text-gray-400 uppercase">
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
                                            className="mt-0.5 w-full rounded border border-gray-200 bg-white px-2 py-1.5 text-xs dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
                                        />
                                    </div>
                                    <div className="flex-1">
                                        <label className="text-[10px] font-semibold text-gray-400 uppercase">
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
                                            className="mt-0.5 w-full rounded border border-gray-200 bg-white px-2 py-1.5 text-xs dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
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
                                        <label className="text-[10px] font-semibold text-gray-400 uppercase">
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
                                            className="mt-0.5 w-full rounded border border-gray-200 bg-white px-2 py-1.5 text-xs dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
                                        />
                                    </div>
                                    <div className="w-20">
                                        <label className="text-[10px] font-semibold text-gray-400 uppercase">
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
                                            className="mt-0.5 w-full rounded border border-gray-200 bg-white px-2 py-1.5 text-xs dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
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
                                        className="mb-0.5 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
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
                                className="flex-1 rounded-xl border border-gray-200 bg-white py-3 font-bold text-gray-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                            >
                                {t('common.cancel')}
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex-[2] rounded-xl bg-blue-600 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-50"
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
