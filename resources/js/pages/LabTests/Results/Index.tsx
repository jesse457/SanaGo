import {
    MagnifyingGlassIcon,
    FunnelIcon,
    PencilSquareIcon,
    EyeIcon,
    ChevronRightIcon,
    HomeIcon,
    ArrowDownTrayIcon,
    BeakerIcon,
    UserIcon,
    CalendarIcon,
    CheckBadgeIcon,
    EllipsisVerticalIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import React, { useState, useCallback } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    results: any;
    filters: {
        search?: string;
        status?: string;
    };
}

export default function ResultsIndex({ results, filters }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search || '');

    // Handle Search with Debounce
    const handleSearch = useCallback(
        debounce((value: string) => {
            router.get(
                route('lab-results.index'),
                { ...filters, search: value },
                {
                    preserveState: true,
                    replace: true,
                },
            );
        }, 300),
        [filters],
    );

    const onSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setSearch(e.target.value);
        handleSearch(e.target.value);
    };

    const getStatusStyle = (status: string) => {
        switch (status) {
            case 'Completed':
                return 'bg-emerald-50 text-emerald-700 border-emerald-200';
            case 'In Progress':
                return 'bg-amber-50 text-amber-700 border-amber-200';
            default:
                return 'bg-gray-50 text-gray-700 border-gray-200';
        }
    };

    return (
        <AppLayout
            headerTitle={t('lab_tests.results.index.title')}
            headerSubtitle={t('lab_tests.results.index.subtitle')}
        >
            <Head title={t('lab_tests.results.index.head_title')} />

            <div className="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="mb-6 flex text-[10px] font-bold tracking-widest text-gray-400 uppercase">
                    <ol className="flex items-center gap-2">
                        <li className="transition-colors hover:text-blue-600">
                            <Link
                                href="/lab/dashboard"
                                className="flex items-center gap-1"
                            >
                                <HomeIcon className="h-3 w-3" />{' '}
                                {t('common.home')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="text-blue-600">
                            {t('lab_tests.results.index.results_archive')}
                        </li>
                    </ol>
                </nav>

                {/* Filters Bar */}
                <div className="mb-6 flex flex-col items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:flex-row">
                    <div className="relative w-full md:max-w-md">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            placeholder={t(
                                'lab_tests.results.index.search_placeholder',
                            )}
                            value={search}
                            onChange={onSearchChange}
                            className="w-full rounded-xl border-gray-200 py-2 pr-4 pl-10 text-sm transition-all focus:ring-4 focus:ring-blue-500/10"
                        />
                    </div>

                    <div className="flex w-full items-center gap-3 md:w-auto">
                        <select
                            value={filters.status || ''}
                            onChange={(e) =>
                                router.get(route('lab-results.index'), {
                                    ...filters,
                                    status: e.target.value,
                                })
                            }
                            className="rounded-xl border-gray-200 text-sm focus:ring-blue-500"
                        >
                            <option value="">
                                {t('lab_tests.results.index.all_statuses')}
                            </option>
                            <option value="Completed">
                                {t('lab_tests.results.index.completed')}
                            </option>
                            <option value="In Progress">
                                {t('lab_tests.results.index.in_progress')}
                            </option>
                        </select>

                        <button className="flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white transition-all hover:bg-gray-800">
                            <ArrowDownTrayIcon className="h-4 w-4" />
                            {t('lab_tests.results.index.export')}
                        </button>
                    </div>
                </div>

                {/* Table Content */}
                <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <table className="w-full text-left">
                        <thead className="border-b border-gray-100 bg-gray-50/50 dark:bg-neutral-700/50">
                            <tr>
                                <th className="px-6 py-4 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                    {t(
                                        'lab_tests.results.index.patient_details',
                                    )}
                                </th>
                                <th className="px-6 py-4 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                    {t('lab_tests.results.index.test_profile')}
                                </th>
                                <th className="px-6 py-4 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                    {t('lab_tests.results.index.result_date')}
                                </th>
                                <th className="px-6 py-4 text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                    {t('lab_tests.results.index.status')}
                                </th>
                                <th className="px-6 py-4 text-right text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                    {t('lab_tests.results.index.actions')}
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50">
                            {results.data.map((result: any) => (
                                <tr
                                    key={result.id}
                                    className="group transition-colors hover:bg-blue-50/30"
                                >
                                    <td className="px-6 py-5">
                                        <div className="flex items-center gap-3">
                                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-600">
                                                {
                                                    result.lab_request.patient
                                                        .first_name[0]
                                                }
                                            </div>
                                            <div>
                                                <p className="text-sm font-bold text-gray-900">
                                                    {
                                                        result.lab_request
                                                            .patient.first_name
                                                    }{' '}
                                                    {
                                                        result.lab_request
                                                            .patient.last_name
                                                    }
                                                </p>
                                                <p className="font-mono text-[10px] text-gray-400">
                                                    {
                                                        result.lab_request
                                                            .patient.patient_uid
                                                    }
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-5">
                                        <div className="flex items-center gap-2">
                                            <BeakerIcon className="h-4 w-4 text-purple-400" />
                                            <div>
                                                <p className="text-sm font-medium text-gray-700">
                                                    {
                                                        result.lab_request
                                                            .test_definition
                                                            .test_name
                                                    }
                                                </p>
                                                <p className="text-[10px] text-gray-400 uppercase">
                                                    {
                                                        result.lab_request
                                                            .test_definition
                                                            .code
                                                    }
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-5">
                                        <div className="flex items-center gap-2 text-gray-600">
                                            <CalendarIcon className="h-4 w-4 text-gray-300" />
                                            <span className="text-sm">
                                                {new Date(
                                                    result.result_date,
                                                ).toLocaleDateString()}
                                            </span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-5">
                                        <span
                                            className={`rounded-lg border px-2.5 py-1 text-[10px] font-black tracking-wider uppercase ${getStatusStyle(result.status)}`}
                                        >
                                            {result.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-5 text-right">
                                        <div className="flex items-center justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                            <Link
                                                href={route(
                                                    'results.edit',
                                                    result.id,
                                                )}
                                                className="rounded-lg p-2 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600"
                                            >
                                                <PencilSquareIcon className="h-5 w-5" />
                                            </Link>
                                            <button className="rounded-lg p-2 text-gray-400 transition-all hover:bg-emerald-50 hover:text-emerald-600">
                                                <ArrowDownTrayIcon className="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {/* Pagination */}
                    <div className="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:bg-neutral-700/50">
                        <p className="text-xs text-gray-500">
                            {t('lab_tests.results.index.showing_archives', {
                                from: results.from,
                                to: results.to,
                                total: results.total,
                            })}
                        </p>
                        <div className="flex gap-2">
                            {results.links.map((link: any, i: number) => (
                                <Link
                                    key={i}
                                    href={link.url || ''}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                    className={`rounded-lg px-3 py-1 text-[10px] font-bold transition-all ${
                                        link.active
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50'
                                    } ${!link.url && 'pointer-events-none opacity-30'}`}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
