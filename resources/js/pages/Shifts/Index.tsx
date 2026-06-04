import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    ClockIcon,
    CalendarDaysIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, router } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Shift {
    id: number;
    user_id: number;
    shift_date: string;
    shift_type: string;
    start_time: string;
    end_time: string;
    user?: { id: number; name: string; department?: { name: string } | null };
}

interface Props {
    shifts: {
        data: Shift[];
        links: any[];
        meta?: {
            from: number;
            to: number;
            total: number;
        };
        current_page: number;
        last_page: number;
    };
}

const SHIFT_LABELS: Record<string, string> = {
    morning: 'Morning (6AM-2PM)',
    afternoon: 'Afternoon (2PM-10PM)',
    night: 'Night (10PM-6AM)',
};

const SHIFT_BADGE_COLORS: Record<string, 'emerald' | 'amber' | 'purple'> = {
    morning: 'emerald',
    afternoon: 'amber',
    night: 'purple',
};

export default function ShiftsIndex({ shifts }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filterType, setFilterType] = useState('all');
    const [modalOpen, setModalOpen] = useState(false);
    const [editingShift, setEditingShift] = useState<Shift | null>(null);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        user_id: '',
        shift_date: '',
        shift_type: 'morning',
        start_time: '',
        end_time: '',
    });

    const stats = useMemo(() => {
        const total = shifts.data.length;
        const morning = shifts.data.filter(
            (s) => s.shift_type === 'morning',
        ).length;
        const afternoon = shifts.data.filter(
            (s) => s.shift_type === 'afternoon',
        ).length;
        const night = shifts.data.filter(
            (s) => s.shift_type === 'night',
        ).length;

        return { total, morning, afternoon, night };
    }, [shifts.data]);

    const filtered = useMemo(() => {
        let result = shifts.data;

        if (filterType !== 'all') {
            result = result.filter((s) => s.shift_type === filterType);
        }

        if (search) {
            const q = search.toLowerCase();
            result = result.filter((s) =>
                s.user?.name.toLowerCase().includes(q),
            );
        }

        return result;
    }, [shifts.data, filterType, search]);

    const openEdit = (shift: Shift) => {
        setEditingShift(shift);
        setData({
            user_id: String(shift.user_id),
            shift_date: shift.shift_date,
            shift_type: shift.shift_type,
            start_time: shift.start_time,
            end_time: shift.end_time,
        });
        setModalOpen(true);
    };

    const openCreate = () => {
        setEditingShift(null);
        reset();
        setModalOpen(true);
    };

    const handleSubmit = () => {
        if (editingShift) {
            put(route('shifts.update', editingShift.id), {
                onSuccess: () => setModalOpen(false),
            });
        } else {
            post(route('shifts.store'), {
                onSuccess: () => setModalOpen(false),
            });
        }
    };

    const handleDelete = (id: number) => {
        if (confirm('Delete this shift?')) {
            router.delete(route('shifts.destroy', id));
        }
    };

    return (
        <AppLayout
            headerTitle={t('page.shifts_index.title')}
            headerSubtitle={t('page.shifts_index.subtitle')}
        >
            <Head title={t('page.shifts_index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.shifts_index.breadcrumb_shifts') },
                    ]}
                />

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        icon={CalendarDaysIcon}
                        label="Total Shifts"
                        value={stats.total}
                    />
                    <StatCard
                        icon={ClockIcon}
                        label="Morning"
                        value={stats.morning}
                        color="emerald"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label="Afternoon"
                        value={stats.afternoon}
                        color="amber"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label="Night"
                        value={stats.night}
                        color="purple"
                    />
                </div>

                <div className="flex items-center justify-between gap-4">
                    <div className="flex rounded-lg bg-gray-100 p-1 dark:bg-neutral-800">
                        {['all', 'morning', 'afternoon', 'night'].map(
                            (type) => (
                                <button
                                    key={type}
                                    onClick={() => setFilterType(type)}
                                    className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide uppercase transition-all ${filterType === type ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700' : 'text-gray-500 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-200'}`}
                                >
                                    {type === 'all'
                                        ? t('page.shifts_index.all')
                                        : type === 'morning'
                                          ? t('page.shifts_index.morning')
                                          : type === 'afternoon'
                                            ? t('page.shifts_index.afternoon')
                                            : t('page.shifts_index.night')}
                                </button>
                            ),
                        )}
                    </div>
                    <div className="flex items-center gap-3">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t(
                                'page.shifts_index.search_placeholder',
                            )}
                            value={search}
                            onChange={setSearch}
                        />
                        <button
                            onClick={openCreate}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />
                            {t('page.shifts_index.add_shift')}
                        </button>
                    </div>
                </div>

                {filtered.length > 0 ? (
                    <PageSection>
                        <table className="w-full text-left">
                            <thead className="border-b border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800">
                                <tr>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                                        {t('page.shifts_index.table_staff')}
                                    </th>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                                        {t('page.shifts_index.table_date_type')}
                                    </th>
                                    <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                                        {t('page.shifts_index.table_hours')}
                                    </th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                {filtered.map((shift) => (
                                    <tr
                                        key={shift.id}
                                        className="hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                    >
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                                    {shift.user?.name?.charAt(
                                                        0,
                                                    ) || '?'}
                                                </div>
                                                <div>
                                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {shift.user?.name}
                                                    </p>
                                                    <p className="text-xs text-gray-400">
                                                        {shift.user?.department
                                                            ?.name ||
                                                            t(
                                                                'page.shifts_index.general',
                                                            )}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {shift.shift_date}
                                            </p>
                                            <StatusBadge
                                                label={shift.shift_type}
                                                color={
                                                    SHIFT_BADGE_COLORS[
                                                        shift.shift_type
                                                    ] || 'gray'
                                                }
                                                className="mt-1"
                                            />
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-1 text-sm font-bold text-gray-700 dark:text-neutral-300">
                                                <ClockIcon className="h-4 w-4 text-gray-400" />
                                                {shift.start_time} -{' '}
                                                {shift.end_time}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex justify-end gap-2">
                                                <button
                                                    onClick={() =>
                                                        openEdit(shift)
                                                    }
                                                    className="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                                                >
                                                    <PencilSquareIcon className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDelete(shift.id)
                                                    }
                                                    className="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                                >
                                                    <TrashIcon className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </PageSection>
                ) : (
                    <EmptyState
                        icon={ClockIcon}
                        title={t('page.shifts_index.no_shifts')}
                    />
                )}

                {shifts.last_page > 1 && (
                    <Pagination links={shifts.links} meta={shifts.meta} />
                )}
            </div>

            <Modal
                isOpen={modalOpen}
                onClose={() => setModalOpen(false)}
                title={
                    editingShift
                        ? t('page.shifts_index.edit_shift')
                        : t('page.shifts_index.assign_shift')
                }
            >
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        handleSubmit();
                    }}
                    className="space-y-4"
                >
                    <FormInput
                        name="user_id"
                        type="number"
                        label={t('page.shifts_index.staff_id')}
                        value={data.user_id}
                        onChange={(v) => setData('user_id', v)}
                        required
                        errors={errors}
                    />
                    <FormInput
                        name="shift_date"
                        type="date"
                        label={t('page.shifts_index.date')}
                        value={data.shift_date}
                        onChange={(v) => setData('shift_date', v)}
                        required
                        errors={errors}
                    />
                    <FormSelect
                        name="shift_type"
                        label={t('page.shifts_index.shift_type')}
                        value={data.shift_type}
                        onChange={(v) => setData('shift_type', v)}
                        options={[
                            {
                                value: 'morning',
                                label: t('page.shifts_index.morning_option'),
                            },
                            {
                                value: 'afternoon',
                                label: t('page.shifts_index.afternoon_option'),
                            },
                            {
                                value: 'night',
                                label: t('page.shifts_index.night_option'),
                            },
                        ]}
                        errors={errors}
                    />
                    <div className="grid grid-cols-2 gap-4">
                        <FormInput
                            name="start_time"
                            type="time"
                            label={t('page.shifts_index.start')}
                            value={data.start_time}
                            onChange={(v) => setData('start_time', v)}
                            required
                            errors={errors}
                        />
                        <FormInput
                            name="end_time"
                            type="time"
                            label={t('page.shifts_index.end')}
                            value={data.end_time}
                            onChange={(v) => setData('end_time', v)}
                            required
                            errors={errors}
                        />
                    </div>
                    <div className="flex gap-4 pt-2">
                        <button
                            type="button"
                            onClick={() => setModalOpen(false)}
                            className="flex-1 rounded-xl border border-gray-200 bg-white py-3 font-bold text-gray-500 dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex-[2] rounded-xl bg-blue-600 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {processing
                                ? t('common.processing')
                                : editingShift
                                  ? t('page.shifts_index.update_shift')
                                  : t('page.shifts_index.assign_shift')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
