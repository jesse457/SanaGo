import {
    MagnifyingGlassIcon,
    TrashIcon,
    ArrowPathIcon,
    EyeIcon,
    CalendarIcon,
    ClipboardDocumentIcon,
    FunnelIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import React, { useState, useEffect, useCallback } from 'react';
import DetailItem from '@/Components/DetailItem';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

// --- Types ---
interface User {
    name: string;
    email: string;
}

interface Activity {
    id: number;
    activity_type: string;
    description: string;
    created_at: string;
    user: User | null;
    metadata?: Record<string, any>;
    ip_address?: string;
    user_agent?: string;
}

interface PaginationProps {
    data: Activity[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number;
    to: number;
    total: number;
    current_page: number;
}

interface Props {
    activities: PaginationProps;
    filters: {
        search?: string;
        date?: string;
        type?: string;
    };
}

export default function UserActivities({ activities, filters }: Props) {
    const { t } = useTranslation();
    // --- State ---
    const [search, setSearch] = useState(filters.search || '');
    const [dateFilter, setDateFilter] = useState(filters.date || '');
    const [filterType, setFilterType] = useState(filters.type || '');
    const [isLoading, setIsLoading] = useState(false);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedActivity, setSelectedActivity] = useState<Activity | null>(
        null,
    );
    const [copiedField, setCopiedField] = useState<string | null>(null);

    // --- Server-Side Filtering Logic ---
    const updateFilters = useCallback(
        debounce((query: any) => {
            router.get(route('activities.index'), query, {
                preserveState: true,
                preserveScroll: true,
                onBefore: () => setIsLoading(true),
                onFinish: () => setIsLoading(false),
            });
        }, 300),
        [],
    );

    useEffect(() => {
        updateFilters({ search, date: dateFilter, type: filterType });
    }, [search, dateFilter, filterType]);

    // --- Helpers ---
    const getActivityColor = (
        type: string,
    ): 'emerald' | 'blue' | 'amber' | 'red' | 'gray' => {
        const t = type.toLowerCase();

        if (t.includes('created')) {
            return 'emerald';
        }

        if (t.includes('login')) {
            return 'blue';
        }

        if (t.includes('updated')) {
            return 'amber';
        }

        if (t.includes('deleted')) {
            return 'red';
        }

        return 'gray';
    };

    // --- Handlers ---
    const handleShowDetails = (activity: Activity) => {
        setSelectedActivity(activity);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedActivity(null);
        setCopiedField(null);
    };

    const handleCopyToClipboard = async (text: string, field: string) => {
        try {
            await navigator.clipboard.writeText(text);
            setCopiedField(field);
            setTimeout(() => setCopiedField(null), 2000);
        } catch (err) {
            console.error(err);
        }
    };

    const clearFilters = () => {
        setSearch('');
        setDateFilter('');
        setFilterType('');
    };

    const hasActiveFilters = search || dateFilter || filterType;

    return (
        <AppLayout
            headerTitle={t('page.activities.title')}
            headerSubtitle={t('page.activities.subtitle')}
        >
            <Head title={t('page.activities.head_title')} />

            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <div className="mb-5">
                    <PageBreadcrumb
                        items={[
                            { label: t('common.home'), href: '/' },
                            { label: t('page.activities.head_title') },
                        ]}
                    />
                </div>

                {/* Filters Bar */}
                <div className="sticky top-4 z-10 mb-5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="flex flex-col items-start justify-between gap-3 lg:flex-row lg:items-center">
                        <div className="flex w-full flex-1 flex-col gap-3 sm:flex-row lg:w-auto">
                            <FormInput
                                name="search"
                                icon={MagnifyingGlassIcon}
                                placeholder={t(
                                    'page.activities.search_placeholder',
                                )}
                                value={search}
                                onChange={setSearch}
                                className="w-full sm:max-w-xs"
                            />

                            <FormInput
                                name="date"
                                type="date"
                                icon={CalendarIcon}
                                value={dateFilter}
                                onChange={setDateFilter}
                                className="w-full sm:w-44"
                            />

                            <FormSelect
                                name="type"
                                icon={FunnelIcon}
                                value={filterType}
                                onChange={setFilterType}
                                options={[
                                    {
                                        value: '',
                                        label: t('page.activities.all_events'),
                                    },
                                    {
                                        value: 'created',
                                        label: t('page.activities.created'),
                                    },
                                    {
                                        value: 'updated',
                                        label: t('page.activities.updated'),
                                    },
                                    {
                                        value: 'deleted',
                                        label: t('page.activities.deleted'),
                                    },
                                    {
                                        value: 'login',
                                        label: t('page.activities.logins'),
                                    },
                                ]}
                                className="w-full sm:w-44"
                            />
                        </div>

                        {hasActiveFilters && (
                            <button
                                onClick={clearFilters}
                                className="flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                            >
                                <TrashIcon className="h-3.5 w-3.5" />{' '}
                                {t('page.activities.clear_filters')}
                            </button>
                        )}
                    </div>
                </div>

                {/* Results Count & Loader */}
                <div className="mb-4 flex items-center justify-between">
                    <p className="text-sm text-gray-600 dark:text-neutral-400">
                        {t('page.activities.showing_results', {
                            from: activities.from || 0,
                            to: activities.to || 0,
                            total: activities.total,
                        })}
                    </p>
                    {isLoading && (
                        <div className="flex items-center gap-2 text-sm text-blue-600">
                            <ArrowPathIcon className="h-4 w-4 animate-spin" />
                            {t('page.activities.syncing')}
                        </div>
                    )}
                </div>

                {/* Main Table Content */}
                {activities.data.length > 0 ? (
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50/50 dark:bg-neutral-900/50">
                                    <tr>
                                        <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.activities.col_user')}
                                        </th>
                                        <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.activities.col_event')}
                                        </th>
                                        <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.activities.col_activity')}
                                        </th>
                                        <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.activities.col_date')}
                                        </th>
                                        <th className="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.activities.col_action')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {activities.data.map((activity) => (
                                        <tr
                                            key={activity.id}
                                            className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-900/40"
                                        >
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-xs font-bold text-white shadow-sm">
                                                        {activity.user?.name.charAt(
                                                            0,
                                                        ) || 'S'}
                                                    </div>
                                                    <div className="ml-3">
                                                        <div className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                                            {activity.user
                                                                ?.name ||
                                                                t(
                                                                    'page.activities.system',
                                                                )}
                                                        </div>
                                                        <div className="text-xs text-gray-500 dark:text-neutral-400">
                                                            {activity.user
                                                                ?.email ||
                                                                'automated@system.com'}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge
                                                    label={
                                                        activity.activity_type
                                                    }
                                                    color={getActivityColor(
                                                        activity.activity_type,
                                                    )}
                                                />
                                            </td>
                                            <td className="px-6 py-4">
                                                <p className="line-clamp-1 text-sm text-gray-700 dark:text-neutral-300">
                                                    {activity.description}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900 dark:text-neutral-200">
                                                    {new Date(
                                                        activity.created_at,
                                                    ).toLocaleDateString(
                                                        undefined,
                                                        {
                                                            month: 'short',
                                                            day: 'numeric',
                                                            year: 'numeric',
                                                        },
                                                    )}
                                                </div>
                                                <div className="text-xs text-gray-500 dark:text-neutral-400">
                                                    {new Date(
                                                        activity.created_at,
                                                    ).toLocaleTimeString([], {
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                    })}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button
                                                    onClick={() =>
                                                        handleShowDetails(
                                                            activity,
                                                        )
                                                    }
                                                    className="rounded-lg p-2 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                                                >
                                                    <EyeIcon className="h-5 w-5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900/50">
                            <Pagination
                                links={activities.links}
                                meta={{
                                    from: activities.from,
                                    to: activities.to,
                                    total: activities.total,
                                }}
                            />
                        </div>
                    </div>
                ) : (
                    <EmptyState
                        icon={MagnifyingGlassIcon}
                        title={t('page.activities.no_results')}
                    />
                )}
            </div>

            {/* Details Modal */}
            <Modal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                title={t('page.activities.log_details')}
                maxWidth="max-w-2xl"
            >
                <div className="max-h-[75vh] space-y-6 overflow-y-auto">
                    <div className="grid grid-cols-2 gap-4">
                        <DetailItem
                            label={t('page.activities.action')}
                            value={selectedActivity?.activity_type ?? '—'}
                        />
                        <DetailItem
                            label={t('page.activities.timestamp')}
                            value={
                                selectedActivity
                                    ? new Date(
                                          selectedActivity.created_at,
                                      ).toLocaleString()
                                    : '—'
                            }
                        />
                        <DetailItem
                            label={t('page.activities.user')}
                            value={
                                selectedActivity?.user?.name ||
                                t('page.activities.system')
                            }
                        />
                        <DetailItem
                            label={t('page.activities.ip_address')}
                            value={
                                selectedActivity?.ip_address ||
                                t('page.activities.na')
                            }
                        />
                    </div>

                    {selectedActivity && (
                        <DetailItem
                            label={t('page.activities.full_description')}
                            value={selectedActivity.description}
                        />
                    )}

                    {selectedActivity?.metadata && (
                        <div>
                            <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                {t('page.activities.properties_changes')}
                            </span>
                            <pre className="mt-1.5 overflow-x-auto rounded-xl border border-gray-800 bg-gray-900 p-4 font-mono text-xs text-emerald-400">
                                {JSON.stringify(
                                    selectedActivity.metadata,
                                    null,
                                    2,
                                )}
                            </pre>
                        </div>
                    )}

                    {selectedActivity?.user_agent && (
                        <DetailItem
                            label={t('page.activities.user_agent')}
                            value={selectedActivity.user_agent}
                        />
                    )}
                </div>

                <div className="-mx-8 mt-6 -mb-8 flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-800/50">
                    <button
                        onClick={() =>
                            handleCopyToClipboard(
                                JSON.stringify(selectedActivity, null, 2),
                                'json',
                            )
                        }
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all hover:bg-blue-700"
                    >
                        {copiedField === 'json' ? (
                            t('page.activities.copied')
                        ) : (
                            <>
                                <ClipboardDocumentIcon className="h-4 w-4" />{' '}
                                {t('page.activities.copy_raw_json')}
                            </>
                        )}
                    </button>
                </div>
            </Modal>
        </AppLayout>
    );
}
