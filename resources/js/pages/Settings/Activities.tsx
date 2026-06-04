import {
    HomeIcon as HomeIconSolid,
    ChevronRightIcon as ChevronRightIconSolid
} from '@heroicons/react/20/solid';
import {
    MagnifyingGlassIcon,
    TrashIcon,
    ArrowPathIcon,
    EyeIcon,
    XMarkIcon,
    CalendarIcon,
    ArrowRightIcon,
    ClipboardDocumentIcon,
    ArrowDownTrayIcon,
    FunnelIcon
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React, { useState, useEffect, useCallback } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import debounce from 'lodash/debounce'; // Recommended: npm install lodash
import { useTranslation } from '@/hooks/useTranslation';

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
    const [selectedActivity, setSelectedActivity] = useState<Activity | null>(null);
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
        []
    );

    useEffect(() => {
        updateFilters({ search, date: dateFilter, type: filterType });
    }, [search, dateFilter, filterType]);

    // --- Helpers ---
    const getTypeBadgeClass = (type: string): string => {
        const t = type.toLowerCase();
        if (t.includes('created')) return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800';
        if (t.includes('login')) return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800';
        if (t.includes('updated')) return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800';
        if (t.includes('deleted')) return 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800';
        return 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700';
    };

    const getTypeIcon = (type: string) => {
        const t = type.toLowerCase();
        if (t.includes('login')) return '🔐';
        if (t.includes('created')) return '✨';
        if (t.includes('updated')) return '✏️';
        if (t.includes('deleted')) return '🗑️';
        return '📋';
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
        } catch (err) { console.error(err); }
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

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-medium text-gray-500 dark:text-neutral-400 mb-5">
                    <ol className="inline-flex items-center space-x-1">
                        <li>
                            <Link href="/" className="hover:text-blue-600 flex items-center transition-colors">
                                <HomeIconSolid className="w-4 h-4 mr-1" /> {t('common.home')}
                            </Link>
                        </li>
                        <li className="flex items-center">
                            <ChevronRightIconSolid className="w-4 h-4 text-gray-300 mx-0.5" />
                            <span className="text-blue-600 dark:text-blue-400 font-semibold">{t('page.activities.head_title')}</span>
                        </li>
                    </ol>
                </nav>

                {/* Filters Bar */}
                <div className="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-4 mb-5 sticky top-4 z-10 shadow-sm">
                    <div className="flex flex-col lg:flex-row gap-3 items-start lg:items-center justify-between">
                        <div className="flex flex-col sm:flex-row gap-3 w-full lg:w-auto flex-1">
                            <div className="relative w-full sm:max-w-xs">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm bg-white dark:bg-neutral-900 dark:text-neutral-100 outline-none focus:ring-2 focus:ring-blue-500/20"
                                    placeholder={t('page.activities.search_placeholder')}
                                />
                            </div>

                            <div className="relative w-full sm:w-44">
                                <CalendarIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" />
                                <input
                                    type="date"
                                    value={dateFilter}
                                    onChange={(e) => setDateFilter(e.target.value)}
                                    className="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm bg-white dark:bg-neutral-900 dark:text-neutral-100 outline-none"
                                />
                            </div>

                            <div className="relative w-full sm:w-44">
                                <FunnelIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" />
                                <select
                                    value={filterType}
                                    onChange={(e) => setFilterType(e.target.value)}
                                    className="block w-full pl-9 pr-8 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm bg-white dark:bg-neutral-900 dark:text-neutral-100 appearance-none outline-none"
                                >
                                    <option value="">{t('page.activities.all_events')}</option>
                                    <option value="created">✨ {t('page.activities.created')}</option>
                                    <option value="updated">✏️ {t('page.activities.updated')}</option>
                                    <option value="deleted">🗑️ {t('page.activities.deleted')}</option>
                                    <option value="login">🔐 {t('page.activities.logins')}</option>
                                </select>
                            </div>
                        </div>

                        {hasActiveFilters && (
                            <button
                                onClick={clearFilters}
                                className="text-xs text-red-600 dark:text-red-400 font-medium flex items-center gap-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-2 rounded-lg transition-colors"
                            >
                                <TrashIcon className="w-3.5 h-3.5" /> {t('page.activities.clear_filters')}
                            </button>
                        )}
                    </div>
                </div>

                {/* Results Count & Loader */}
                <div className="flex items-center justify-between mb-4">
                    <p className="text-sm text-gray-600 dark:text-neutral-400">
                        {t('page.activities.showing_results', { from: activities.from || 0, to: activities.to || 0, total: activities.total })}
                    </p>
                    {isLoading && (
                        <div className="flex items-center gap-2 text-sm text-blue-600">
                            <ArrowPathIcon className="animate-spin h-4 w-4" />
                            {t('page.activities.syncing')}
                        </div>
                    )}
                </div>

                {/* Main Table Content */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead className="bg-gray-50/50 dark:bg-neutral-900/50">
                                <tr>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.activities.col_user')}</th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.activities.col_event')}</th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.activities.col_activity')}</th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.activities.col_date')}</th>
                                    <th className="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{t('page.activities.col_action')}</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                {activities.data.length > 0 ? (
                                    activities.data.map((activity) => (
                                        <tr key={activity.id} className="group hover:bg-gray-50 dark:hover:bg-neutral-900/40 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-9 w-9 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                        {activity.user?.name.charAt(0) || 'S'}
                                                    </div>
                                                    <div className="ml-3">
                                                        <div className="text-sm font-medium text-gray-900 dark:text-neutral-100">{activity.user?.name || t('page.activities.system')}</div>
                                                        <div className="text-xs text-gray-500">{activity.user?.email || 'automated@system.com'}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium border ${getTypeBadgeClass(activity.activity_type)}`}>
                                                    {getTypeIcon(activity.activity_type)} {activity.activity_type}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <p className="text-sm text-gray-700 dark:text-neutral-300 line-clamp-1">{activity.description}</p>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900 dark:text-neutral-200">
                                                    {new Date(activity.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })}
                                                </div>
                                                <div className="text-xs text-gray-500">
                                                    {new Date(activity.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <button
                                                    onClick={() => handleShowDetails(activity)}
                                                    className="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all"
                                                >
                                                    <EyeIcon className="w-5 h-5" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={5} className="py-12 text-center">
                                            <div className="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-neutral-800 mb-4">
                                                <MagnifyingGlassIcon className="w-6 h-6 text-gray-400" />
                                            </div>
                                            <p className="text-gray-500 dark:text-neutral-400 text-sm">{t('page.activities.no_results')}</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination Footer */}
                    <div className="px-6 py-4 bg-gray-50 dark:bg-neutral-900/50 border-t border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                        <div className="flex gap-2">
                            {activities.links.map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url || ''}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                                        link.active
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'bg-white dark:bg-neutral-800 text-gray-600 dark:text-neutral-400 border border-gray-200 dark:border-neutral-700 hover:border-blue-500'
                                    } ${!link.url ? 'opacity-40 cursor-not-allowed' : ''}`}
                                    onClick={(e) => !link.url && e.preventDefault()}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            {/* Details Modal */}
            {isModalOpen && selectedActivity && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onClick={handleCloseModal}></div>
                    <div className="relative bg-white dark:bg-neutral-900 rounded-2xl w-full max-w-2xl shadow-2xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 dark:border-neutral-800 flex justify-between items-center bg-gray-50/50 dark:bg-neutral-800/50">
                            <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">{t('page.activities.log_details')}</h3>
                            <button onClick={handleCloseModal} className="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-neutral-800 transition-colors">
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>

                        <div className="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                            <div className="grid grid-cols-2 gap-6">
                                <DetailField label={t('page.activities.action')} value={
                                    <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold border ${getTypeBadgeClass(selectedActivity.activity_type)}`}>
                                        {selectedActivity.activity_type.toUpperCase()}
                                    </span>
                                } />
                                <DetailField label={t('page.activities.timestamp')} value={new Date(selectedActivity.created_at).toLocaleString()} />
                                <DetailField label={t('page.activities.user')} value={selectedActivity.user?.name || t('page.activities.system')} />
                                <DetailField label={t('page.activities.ip_address')} value={selectedActivity.ip_address || t('page.activities.na')} />
                            </div>

                            <DetailField label={t('page.activities.full_description')} value={
                                <div className="p-3 bg-gray-50 dark:bg-neutral-800 rounded-lg border border-gray-100 dark:border-neutral-700 text-sm">
                                    {selectedActivity.description}
                                </div>
                            } />

                            {selectedActivity.metadata && (
                                <div>
                                    <label className="block text-xs font-bold text-gray-400 uppercase mb-2">{t('page.activities.properties_changes')}</label>
                                    <pre className="p-4 bg-gray-900 text-emerald-400 rounded-xl text-xs overflow-x-auto border border-gray-800 font-mono">
                                        {JSON.stringify(selectedActivity.metadata, null, 2)}
                                    </pre>
                                </div>
                            )}

                            {selectedActivity.user_agent && (
                                <DetailField label={t('page.activities.user_agent')} value={
                                    <span className="text-xs text-gray-500 break-all leading-relaxed">{selectedActivity.user_agent}</span>
                                } />
                            )}
                        </div>

                        <div className="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-100 dark:border-neutral-800 flex justify-end gap-3">
                            <button
                                onClick={() => handleCopyToClipboard(JSON.stringify(selectedActivity, null, 2), 'json')}
                                className="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2"
                            >
                                {copiedField === 'json' ? t('page.activities.copied') : <><ClipboardDocumentIcon className="w-4 h-4" /> {t('page.activities.copy_raw_json')}</>}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

function DetailField({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div>
            <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{label}</label>
            <div className="text-sm font-medium text-gray-900 dark:text-neutral-200">{value}</div>
        </div>
    );
}
