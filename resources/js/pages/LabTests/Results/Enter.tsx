import {
    HomeIcon,
    ChevronRightIcon,
    BeakerIcon,
    PencilSquareIcon,
    CloudArrowUpIcon,
    DocumentTextIcon,
    XMarkIcon,
    DocumentCheckIcon,
    CheckIcon,
    ChatBubbleLeftEllipsisIcon,
    CalendarIcon,
    CpuChipIcon,
    LinkIcon,
    CommandLineIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    labRequest: any;
    labResult?: any; // Optional: provided only during edit
    isEdit: boolean;
}

export default function EnterResults({ labRequest, labResult, isEdit }: Props) {
    const { t } = useTranslation();

    // --- 1. Form Initialization (Resource Based) ---
    const { data, setData, post, put, processing, errors } = useForm({
        lab_request_id: labRequest.id,
        status: labResult?.status || labRequest.status || 'In Progress',
        result_date: labResult?.result_date
            ? new Date(labResult.result_date).toISOString().split('T')[0]
            : new Date().toISOString().split('T')[0],
        results_text: labResult?.results_text || '',
        analysis_comments: labResult?.analysis_comments || '',
        attachments: [] as File[],
    });

    // --- 2. Machine Integration Logic ---
    const [isConnecting, setIsConnecting] = useState(false);
    const [selectedMachine, setSelectedMachine] = useState('');

    const handleMachineFetch = async () => {
        if (!selectedMachine) {
            return;
        }

        setIsConnecting(true);
        // Simulation
        await new Promise((resolve) => setTimeout(resolve, 1200));
        const mockData = {
            sysmex: 'WBC: 6.8 x10^3/uL\nRBC: 4.10 x10^6/uL\nHGB: 12.5 g/dL',
            roche: 'Glucose: 105 mg/dL\nBUN: 12 mg/dL',
        };
        setData(
            'results_text',
            (mockData as any)[selectedMachine] || 'No data.',
        );
        setIsConnecting(false);
    };

    // --- 3. Submit Handler ---
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isEdit) {
            put(route('lab-results.update', labResult.id), {
                forceFormData: true,
            });
        } else {
            post(route('lab-results.store'), { forceFormData: true });
        }
    };

    return (
        <AppLayout
            headerTitle={
                isEdit
                    ? t('lab_tests.results.enter.edit_title')
                    : t('lab_tests.results.enter.create_title')
            }
            headerSubtitle={t('lab_tests.results.enter.subtitle')}
        >
            <Head title={t('lab_tests.results.enter.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <ol className="inline-flex items-center space-x-2">
                        <li>
                            <Link
                                href="/lab/dashboard"
                                className="transition-colors hover:text-blue-600"
                            >
                                {t('common.home')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="text-blue-600">
                            {t('lab_tests.results.enter.breadcrumb')}
                        </li>
                    </ol>
                </nav>

                <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    {/* Header: Request Summary */}
                    <div className="grid grid-cols-1 divide-y divide-gray-200 border-b border-gray-200 bg-gray-50/80 md:grid-cols-3 md:divide-x md:divide-y-0">
                        <div className="px-8 py-6">
                            <label className="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase">
                                {t('lab_tests.results.enter.subject_patient')}
                            </label>
                            <div className="flex items-center gap-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 font-bold text-white">
                                    {labRequest.patient.first_name[0]}
                                </div>
                                <div>
                                    <p className="text-sm font-bold text-gray-900">
                                        {labRequest.patient.first_name}{' '}
                                        {labRequest.patient.last_name}
                                    </p>
                                    <p className="font-mono text-[10px] text-gray-500">
                                        {labRequest.patient.patient_uid}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="px-8 py-6">
                            <label className="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase">
                                {t('lab_tests.results.enter.test_definition')}
                            </label>
                            <div className="flex items-center gap-3 text-sm font-bold text-gray-800">
                                <BeakerIcon className="h-5 w-5 text-purple-500" />
                                {labRequest.test_definition.test_name}
                            </div>
                        </div>
                        <div className="px-8 py-6">
                            <label className="mb-2 block text-[10px] font-bold tracking-widest text-gray-400 uppercase">
                                {t('lab_tests.results.enter.current_lifecycle')}
                            </label>
                            <span className="rounded-full border border-gray-200 bg-white px-3 py-1 text-[10px] font-black text-gray-600 shadow-sm">
                                {labRequest.status.toUpperCase()}
                            </span>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-8 p-8">
                        {/* Machine Integration Logic */}
                        <div className="relative overflow-hidden rounded-2xl bg-gray-900 p-6 text-white shadow-xl">
                            <CommandLineIcon className="absolute right-0 bottom-0 -mr-8 -mb-8 h-32 w-32 text-white/5" />
                            <div className="relative z-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                                <div>
                                    <h3 className="flex items-center gap-2 text-sm font-bold">
                                        <CpuChipIcon className="h-5 w-5 text-blue-400" />{' '}
                                        {t(
                                            'lab_tests.results.enter.analyst_integration',
                                        )}
                                    </h3>
                                    <p className="mt-1 text-xs text-gray-400">
                                        {t(
                                            'lab_tests.results.enter.integration_description',
                                        )}
                                    </p>
                                </div>
                                <div className="flex items-center gap-3">
                                    <select
                                        value={selectedMachine}
                                        onChange={(e) =>
                                            setSelectedMachine(e.target.value)
                                        }
                                        className="rounded-xl border-gray-700 bg-gray-800 text-xs font-bold text-white focus:ring-blue-500"
                                    >
                                        <option value="">
                                            {t(
                                                'lab_tests.results.enter.select_hardware',
                                            )}
                                        </option>
                                        <option value="sysmex">
                                            Sysmex Analyzer (LIMS-HL7)
                                        </option>
                                        <option value="roche">
                                            Roche Cobas (Direct Link)
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        onClick={handleMachineFetch}
                                        disabled={
                                            !selectedMachine || isConnecting
                                        }
                                        className="flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white transition-all hover:bg-blue-500"
                                    >
                                        {isConnecting ? (
                                            <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        ) : (
                                            <LinkIcon className="h-4 w-4" />
                                        )}
                                        {t(
                                            'lab_tests.results.enter.fetch_data',
                                        )}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Metadata Row */}
                        <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div className="space-y-2">
                                <label className="text-xs font-bold text-gray-500 uppercase">
                                    {t(
                                        'lab_tests.results.enter.status_assignment',
                                    )}
                                </label>
                                <select
                                    value={data.status}
                                    onChange={(e) =>
                                        setData('status', e.target.value)
                                    }
                                    className="w-full rounded-xl border-gray-200 p-3 text-sm focus:ring-blue-500"
                                >
                                    <option value="In Progress">
                                        {t(
                                            'lab_tests.results.enter.in_progress',
                                        )}
                                    </option>
                                    <option value="Completed">
                                        {t(
                                            'lab_tests.results.enter.completed_verified',
                                        )}
                                    </option>
                                    <option value="Cancelled">
                                        {t(
                                            'lab_tests.results.enter.voided_cancelled',
                                        )}
                                    </option>
                                </select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-xs font-bold text-gray-500 uppercase">
                                    {t(
                                        'lab_tests.results.enter.verification_date',
                                    )}
                                </label>
                                <input
                                    type="date"
                                    value={data.result_date}
                                    onChange={(e) =>
                                        setData('result_date', e.target.value)
                                    }
                                    className="w-full rounded-xl border-gray-200 p-3 text-sm focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        {/* Text Results Section */}
                        <div className="grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <div className="space-y-2">
                                <label className="flex items-center gap-2 text-xs font-bold text-gray-700">
                                    <DocumentCheckIcon className="h-5 w-5 text-emerald-500" />
                                    {t('lab_tests.results.enter.findings')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <textarea
                                    rows={10}
                                    value={data.results_text}
                                    onChange={(e) =>
                                        setData('results_text', e.target.value)
                                    }
                                    className={`w-full rounded-2xl border p-4 font-mono text-sm leading-relaxed transition-all focus:ring-4 focus:ring-blue-500/10 ${errors.results_text ? 'border-red-500' : 'border-gray-200'}`}
                                    placeholder={t(
                                        'lab_tests.results.enter.results_placeholder',
                                    )}
                                />
                                {errors.results_text && (
                                    <p className="text-xs text-red-500">
                                        {errors.results_text}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <label className="flex items-center gap-2 text-xs font-bold text-gray-700">
                                    <ChatBubbleLeftEllipsisIcon className="h-5 w-5 text-gray-400" />
                                    {t(
                                        'lab_tests.results.enter.analytical_interpretation',
                                    )}
                                </label>
                                <textarea
                                    rows={10}
                                    value={data.analysis_comments}
                                    onChange={(e) =>
                                        setData(
                                            'analysis_comments',
                                            e.target.value,
                                        )
                                    }
                                    className="w-full rounded-2xl border border-gray-200 p-4 text-sm leading-relaxed focus:ring-4 focus:ring-blue-500/10"
                                    placeholder={t(
                                        'lab_tests.results.enter.interpretation_placeholder',
                                    )}
                                />
                            </div>
                        </div>

                        {/* File Attachments */}
                        <div className="space-y-2">
                            <label className="flex items-center gap-2 text-xs font-bold text-gray-700">
                                <CloudArrowUpIcon className="h-5 w-5 text-blue-500" />
                                {t('lab_tests.results.enter.attach_reports')}
                            </label>
                            <div
                                className="cursor-pointer rounded-2xl border-2 border-dashed border-gray-300 p-6 text-center transition-colors hover:border-blue-500 hover:bg-blue-50/50"
                                onDragOver={(e) => {
                                    e.preventDefault();
                                }}
                                onDrop={(e) => {
                                    e.preventDefault();

                                    if (e.dataTransfer.files?.length) {
                                        setData(
                                            'attachments',
                                            Array.from(e.dataTransfer.files),
                                        );
                                    }
                                }}
                            >
                                <label className="cursor-pointer">
                                    <CloudArrowUpIcon className="mx-auto mb-2 h-10 w-10 text-gray-400" />
                                    <p className="text-sm font-bold text-gray-600">
                                        {data.attachments.length > 0
                                            ? t(
                                                  'lab_tests.results.enter.files_selected',
                                                  {
                                                      count: data.attachments
                                                          .length,
                                                  },
                                              )
                                            : t(
                                                  'lab_tests.results.enter.drop_files',
                                              )}
                                    </p>
                                    <p className="mt-1 text-xs text-gray-400">
                                        {t(
                                            'lab_tests.results.enter.max_file_size',
                                        )}
                                    </p>
                                    <input
                                        type="file"
                                        multiple
                                        onChange={(e) =>
                                            setData(
                                                'attachments',
                                                Array.from(
                                                    e.target.files || [],
                                                ),
                                            )
                                        }
                                        className="hidden"
                                    />
                                </label>
                            </div>
                            {data.attachments.length > 0 && (
                                <ul className="mt-3 space-y-2">
                                    {Array.from(data.attachments).map(
                                        (file, i) => (
                                            <li
                                                key={i}
                                                className="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-2"
                                            >
                                                <div className="flex items-center gap-2">
                                                    <DocumentTextIcon className="h-4 w-4 text-blue-500" />
                                                    <span className="text-sm font-medium text-gray-700">
                                                        {file.name}
                                                    </span>
                                                    <span className="text-xs text-gray-400">
                                                        (
                                                        {(
                                                            file.size / 1024
                                                        ).toFixed(1)}{' '}
                                                        KB)
                                                    </span>
                                                </div>
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        const files =
                                                            Array.from(
                                                                data.attachments,
                                                            );
                                                        files.splice(i, 1);
                                                        setData(
                                                            'attachments',
                                                            files,
                                                        );
                                                    }}
                                                    className="p-1 text-gray-400 hover:text-red-600"
                                                >
                                                    <XMarkIcon className="h-4 w-4" />
                                                </button>
                                            </li>
                                        ),
                                    )}
                                </ul>
                            )}
                        </div>

                        {/* Action Buttons */}
                        <div className="flex items-center justify-between border-t border-gray-100 pt-8">
                            <p className="text-xs text-gray-400 italic">
                                {t('lab_tests.results.enter.finalizing_notice')}
                            </p>
                            <div className="flex gap-4">
                                <Link
                                    href="/lab/test-requests"
                                    className="rounded-xl px-8 py-3 text-sm font-bold text-gray-500 transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700"
                                >
                                    {t('common.cancel')}
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex items-center gap-2 rounded-xl bg-blue-600 px-10 py-3 text-sm font-bold text-white shadow-xl shadow-blue-200 transition-all hover:bg-blue-700 active:scale-95 disabled:opacity-50"
                                >
                                    {processing ? (
                                        <ArrowPathIcon className="h-5 w-5 animate-spin" />
                                    ) : (
                                        <CheckIcon className="h-5 w-5" />
                                    )}
                                    {isEdit
                                        ? t(
                                              'lab_tests.results.enter.update_record',
                                          )
                                        : t(
                                              'lab_tests.results.enter.submit_result',
                                          )}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
