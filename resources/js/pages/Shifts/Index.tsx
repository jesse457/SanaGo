import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    HomeIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm, router } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import Modal from '@/Components/Modal';

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
        current_page: number;
        last_page: number;
    };
}

const SHIFT_LABELS: Record<string, string> = {
    morning: 'Morning (6AM-2PM)',
    afternoon: 'Afternoon (2PM-10PM)',
    night: 'Night (10PM-6AM)',
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

    const filtered = useMemo(() => {
        let result = shifts.data;
        if (filterType !== 'all')
            result = result.filter((s) => s.shift_type === filterType);
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
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" />
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('page.shifts_index.breadcrumb_shifts')}
                        </li>
                    </ol>
                </nav>

                <div className="flex items-center justify-between gap-4">
                    <div className="flex rounded-lg bg-gray-100 p-1 dark:bg-neutral-800">
                        {['all', 'morning', 'afternoon', 'night'].map(
                            (type) => (
                                <button
                                    key={type}
                                    onClick={() => setFilterType(type)}
                                    className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide uppercase transition-all ${filterType === type ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700' : 'text-gray-500 hover:text-gray-900'}`}
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
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t(
                                    'page.shifts_index.search_placeholder',
                                )}
                                className="rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800"
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>
                        <button
                            onClick={openCreate}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.shifts_index.add_shift')}
                        </button>
                    </div>
                </div>

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
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
                                            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-xs font-bold text-white">
                                                {shift.user?.name?.charAt(0) ||
                                                    '?'}
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
                                        <span
                                            className={`rounded px-2 py-0.5 text-[10px] font-bold uppercase ${shift.shift_type === 'night' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : shift.shift_type === 'afternoon' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'}`}
                                        >
                                            {shift.shift_type}
                                        </span>
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
                                                onClick={() => openEdit(shift)}
                                                className="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600"
                                            >
                                                <PencilSquareIcon className="h-4 w-4" />
                                            </button>
                                            <button
                                                onClick={() =>
                                                    handleDelete(shift.id)
                                                }
                                                className="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {filtered.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={4}
                                        className="px-6 py-16 text-center font-medium text-gray-400"
                                    >
                                        {t('page.shifts_index.no_shifts')}
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {shifts.last_page > 1 && (
                    <div className="flex justify-center gap-2">
                        {Array.from(
                            { length: shifts.last_page },
                            (_, i) => i + 1,
                        ).map((p) => (
                            <Link
                                key={p}
                                href={`/shifts?page=${p}`}
                                className={`rounded-lg px-3 py-2 text-xs font-bold ${p === shifts.current_page ? 'bg-blue-600 text-white' : 'border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800'}`}
                            >
                                {p}
                            </Link>
                        ))}
                    </div>
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
                    <div>
                        <label className="ml-1 text-xs font-semibold text-gray-500 uppercase">
                            {t('page.shifts_index.staff_id')}
                        </label>
                        <input
                            type="number"
                            value={data.user_id}
                            onChange={(e) => setData('user_id', e.target.value)}
                            className="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold dark:border-neutral-600 dark:bg-neutral-700"
                            required
                        />
                    </div>
                    <div>
                        <label className="ml-1 text-xs font-semibold text-gray-500 uppercase">
                            {t('page.shifts_index.date')}
                        </label>
                        <input
                            type="date"
                            value={data.shift_date}
                            onChange={(e) =>
                                setData('shift_date', e.target.value)
                            }
                            className="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold dark:border-neutral-600 dark:bg-neutral-700"
                            required
                        />
                    </div>
                    <div>
                        <label className="ml-1 text-xs font-semibold text-gray-500 uppercase">
                            {t('page.shifts_index.shift_type')}
                        </label>
                        <select
                            value={data.shift_type}
                            onChange={(e) =>
                                setData('shift_type', e.target.value)
                            }
                            className="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold dark:border-neutral-600 dark:bg-neutral-700"
                        >
                            <option value="morning">
                                {t('page.shifts_index.morning_option')}
                            </option>
                            <option value="afternoon">
                                {t('page.shifts_index.afternoon_option')}
                            </option>
                            <option value="night">
                                {t('page.shifts_index.night_option')}
                            </option>
                        </select>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="ml-1 text-xs font-semibold text-gray-500 uppercase">
                                {t('page.shifts_index.start')}
                            </label>
                            <input
                                type="time"
                                value={data.start_time}
                                onChange={(e) =>
                                    setData('start_time', e.target.value)
                                }
                                className="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold dark:border-neutral-600 dark:bg-neutral-700"
                                required
                            />
                        </div>
                        <div>
                            <label className="ml-1 text-xs font-semibold text-gray-500 uppercase">
                                {t('page.shifts_index.end')}
                            </label>
                            <input
                                type="time"
                                value={data.end_time}
                                onChange={(e) =>
                                    setData('end_time', e.target.value)
                                }
                                className="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold dark:border-neutral-600 dark:bg-neutral-700"
                                required
                            />
                        </div>
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
                            className="flex-[2] rounded-xl bg-blue-600 py-3 font-bold text-white hover:bg-blue-700"
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
