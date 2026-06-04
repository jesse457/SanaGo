import {
    MagnifyingGlassIcon,
    PlusIcon,
    BeakerIcon,
    PencilSquareIcon,
    TrashIcon,
    CurrencyDollarIcon,
    CheckBadgeIcon,
    XMarkIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import debounce from 'lodash/debounce';
import React, { useState, useMemo, useCallback, useEffect } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface LabTest {
    id: number;
    code: string;
    test_name: string;
    price: number | string;
    units: string;
    is_active: boolean;
    description: string;
}

interface Props {
    tests: {
        data: LabTest[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    filters: { search?: string };
}

export default function LabTestManagement({ tests, filters }: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [selectedId, setSelectedId] = useState<number | null>(null);

    // Form logic for EDIT MODAL
    const { data, setData, put, processing, errors, reset, clearErrors } =
        useForm({
            test_name: '',
            code: '',
            price: 0,
            units: '',
            description: '',
            is_active: true,
        });

    // Handle Search
    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(
                route('tests.index'),
                { search: searchQuery },
                { preserveState: true, replace: true },
            );
        }, 300);

        return () => clearTimeout(timeout);
    }, [searchQuery]);

    // Open Edit Modal
    const openEditModal = (test: LabTest) => {
        setSelectedId(test.id);
        setData({
            test_name: test.test_name,
            code: test.code,
            price: Number(test.price),
            units: test.units,
            description: test.description || '',
            is_active: !!test.is_active,
        });
        setIsEditModalOpen(true);
    };

    const handleUpdate = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('tests.update', selectedId!), {
            onSuccess: () => {
                setIsEditModalOpen(false);
                reset();
            },
            preserveScroll: true,
        });
    };

    const handleDelete = (id: number) => {
        if (confirm(t('lab_tests.index.delete_confirm'))) {
            router.delete(route('tests.destroy', id), { preserveScroll: true });
        }
    };

    const stats = useMemo(
        () => ({
            total: tests.total,
            active: tests.data.filter((t) => t.is_active).length,
            avgPrice:
                tests.data.reduce((acc, t) => acc + Number(t.price), 0) /
                (tests.data.length || 1),
        }),
        [tests],
    );

    return (
        <AppLayout
            headerTitle={t('lab_tests.index.title')}
            headerSubtitle={t('lab_tests.index.subtitle')}
        >
            <Head title={t('lab_tests.index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: t('lab_tests.index.test_definitions') },
                    ]}
                />

                {/* Stats */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <StatCard
                        label={t('lab_tests.index.total_tests')}
                        value={stats.total}
                        icon={BeakerIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('lab_tests.index.active')}
                        value={stats.active}
                        icon={CheckBadgeIcon}
                        color="emerald"
                    />
                    <StatCard
                        label={t('lab_tests.index.avg_price')}
                        value={`$${stats.avgPrice.toFixed(2)}`}
                        icon={CurrencyDollarIcon}
                        color="amber"
                    />
                </div>

                {/* Controls */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div className="relative flex-grow lg:max-w-md">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                        <input
                            type="text"
                            placeholder={t(
                                'lab_tests.index.search_placeholder',
                            )}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800"
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('tests.create')}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('lab_tests.index.new_test')}
                        </Link>
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                    </div>
                </div>

                {/* Main Content Area */}
                <AnimatePresence mode="wait">
                    {tests.data.length === 0 ? (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                        >
                            <EmptyState
                                icon={BeakerIcon}
                                title={t('lab_tests.index.no_tests')}
                                description={t(
                                    'lab_tests.index.no_tests_description',
                                )}
                                action={{
                                    label: t(
                                        'lab_tests.index.create_first_test',
                                    ),
                                    href: route('tests.create'),
                                }}
                            />
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        <motion.div
                            key="grid"
                            className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                        >
                            {tests.data.map((test) => (
                                <div
                                    key={test.id}
                                    className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div
                                            className={`flex h-12 w-12 items-center justify-center rounded-lg ${test.is_active ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-50 text-gray-400 dark:bg-neutral-700 dark:text-neutral-500'}`}
                                        >
                                            <BeakerIcon className="h-6 w-6" />
                                        </div>
                                        <StatusBadge
                                            label={
                                                test.is_active
                                                    ? t(
                                                          'lab_tests.index.active_status',
                                                      )
                                                    : t(
                                                          'lab_tests.index.inactive_status',
                                                      )
                                            }
                                            color={
                                                test.is_active
                                                    ? 'emerald'
                                                    : 'gray'
                                            }
                                        />
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                            {test.test_name}
                                        </h3>
                                        <p className="mb-3 font-mono text-xs font-bold text-blue-500">
                                            {test.code}
                                        </p>
                                        <p className="line-clamp-2 h-10 text-sm text-gray-500 dark:text-neutral-500">
                                            {test.description ||
                                                t(
                                                    'lab_tests.index.no_description',
                                                )}
                                        </p>
                                    </div>
                                    <div className="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-neutral-700">
                                        <div className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                            ${Number(test.price).toFixed(2)}
                                        </div>
                                        <div className="flex gap-2">
                                            <button
                                                onClick={() =>
                                                    openEditModal(test)
                                                }
                                                className="rounded-md p-1.5 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600 dark:text-neutral-500 dark:hover:bg-blue-900/20"
                                            >
                                                <PencilSquareIcon className="h-4 w-4" />
                                            </button>
                                            <button
                                                onClick={() =>
                                                    handleDelete(test.id)
                                                }
                                                className="rounded-md p-1.5 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600 dark:text-neutral-500 dark:hover:bg-blue-900/20"
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    ) : (
                        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 text-xs font-bold text-gray-500 uppercase dark:bg-neutral-700 dark:text-neutral-500">
                                    <tr>
                                        <th className="px-6 py-4 text-left">
                                            {t('lab_tests.index.test_name')}
                                        </th>
                                        <th className="px-6 py-4 text-left">
                                            {t('lab_tests.index.price')}
                                        </th>
                                        <th className="px-6 py-4 text-center">
                                            {t('lab_tests.index.status')}
                                        </th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {tests.data.map((test) => (
                                        <tr
                                            key={test.id}
                                            className="hover:bg-gray-50 dark:hover:bg-neutral-700"
                                        >
                                            <td className="px-6 py-4">
                                                <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {test.test_name}
                                                </div>
                                                <div className="font-mono text-[10px] text-gray-400 dark:text-neutral-500">
                                                    {test.code}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                ${Number(test.price).toFixed(2)}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge
                                                    label={
                                                        test.is_active
                                                            ? t(
                                                                  'lab_tests.index.active_status',
                                                              )
                                                            : t(
                                                                  'lab_tests.index.inactive_status',
                                                              )
                                                    }
                                                    color={
                                                        test.is_active
                                                            ? 'emerald'
                                                            : 'gray'
                                                    }
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button
                                                    onClick={() =>
                                                        openEditModal(test)
                                                    }
                                                    className="mr-2 p-1 text-gray-400 hover:text-blue-600 dark:text-neutral-500"
                                                >
                                                    <PencilSquareIcon className="h-5 w-5" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDelete(test.id)
                                                    }
                                                    className="p-1 text-gray-400 hover:text-blue-600 dark:text-neutral-500"
                                                >
                                                    <TrashIcon className="h-5 w-5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </AnimatePresence>

                {/* Pagination */}
                <Pagination
                    links={tests.links}
                    meta={{
                        from: tests.from || 0,
                        to: tests.to || 0,
                        total: tests.total,
                    }}
                />
            </div>

            {/* --- EDIT MODAL --- */}
            {isEditModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div
                        className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                        onClick={() => setIsEditModalOpen(false)}
                    ></div>
                    <div className="relative flex w-full max-w-2xl flex-col overflow-hidden rounded-xl bg-white shadow-xl dark:bg-neutral-800">
                        <div className="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-700">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-neutral-100">
                                {t('lab_tests.index.edit_test')}
                            </h3>
                            <button
                                onClick={() => setIsEditModalOpen(false)}
                                className="text-gray-400 hover:text-gray-600 dark:text-neutral-500 dark:hover:text-neutral-400"
                            >
                                <XMarkIcon className="h-5 w-5" />
                            </button>
                        </div>
                        <form onSubmit={handleUpdate} className="space-y-4 p-6">
                            <FormInput
                                name="test_name"
                                label={t('lab_tests.index.test_name')}
                                value={data.test_name}
                                onChange={(v) => setData('test_name', v)}
                                errors={errors}
                                required
                            />
                            <div className="grid grid-cols-2 gap-4">
                                <FormInput
                                    name="code"
                                    label={t('lab_tests.index.code')}
                                    value={data.code}
                                    onChange={(v) => setData('code', v)}
                                    errors={errors}
                                />
                                <FormInput
                                    name="price"
                                    label={t('lab_tests.index.price')}
                                    type="number"
                                    step="0.01"
                                    value={data.price}
                                    onChange={(v) =>
                                        setData('price', Number(v))
                                    }
                                    errors={errors}
                                />
                            </div>
                            <FormTextarea
                                name="description"
                                label={t('lab_tests.index.description')}
                                value={data.description}
                                onChange={(v) => setData('description', v)}
                                rows={3}
                            />
                            <div className="flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                                <button
                                    type="button"
                                    onClick={() => setIsEditModalOpen(false)}
                                    className="rounded-xl border border-gray-200 px-4 py-2 font-bold text-gray-500 transition-all hover:bg-gray-50 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700"
                                >
                                    {t('common.cancel')}
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-all hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing && (
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                    )}{' '}
                                    {t('common.save_changes')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
