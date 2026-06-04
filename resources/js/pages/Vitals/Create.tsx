import {
    HomeIcon,
    ChevronRightIcon,
    HeartIcon,
    FireIcon,
    AdjustmentsHorizontalIcon,
    ClockIcon,
    ClipboardDocumentCheckIcon,
    ExclamationTriangleIcon,
    CheckIcon,
    ArrowPathIcon,
    ArrowLeftIcon,
    UserIcon,
    ChatBubbleBottomCenterTextIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { FormEvent } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
}

export default function VitalsForm({ patient }: { patient: Patient }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        patient_id: patient.id,
        recorded_at: new Date().toISOString().slice(0, 16), // Format for datetime-local input
        temperature: '',
        systolic_bp: '',
        diastolic_bp: '',
        heart_rate: '',
        oxygen_saturation: '',
        respiratory_rate: '',
        is_abnormal: false,
        notes: '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('vitals.store'));
    };

    // Shared styling logic
    const inputStyle = (error: string | undefined) => `
        w-full pl-12 pr-4 py-4 bg-white dark:bg-neutral-800 border-2 rounded-lg
        font-bold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:border-red-600' : 'border-gray-200 dark:border-neutral-700 focus:border-rose-600 focus:ring-0'}
    `;

    return (
        <AppLayout
            headerTitle={t('page.vitals_create.title')}
            headerSubtitle={t('page.vitals_create.subtitle', {
                firstName: patient.first_name,
                lastName: patient.last_name,
            })}
        >
            <Head title={t('page.vitals_create.head_title')} />

            <div className="mx-auto max-w-6xl space-y-8 px-4 pb-20 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-[10px] font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-rose-600">
                            <HomeIcon className="mr-1 h-3.5 w-3.5" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-rose-600">
                            {t('page.vitals_create.patient_records')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="text-rose-600">
                            {t('page.vitals_create.vitals_entry')}
                        </li>
                    </ol>
                </nav>

                <form onSubmit={handleSubmit} className="space-y-8">
                    <div className="overflow-hidden rounded-lg border-2 border-gray-100 bg-white shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                        {/* Session Information Header */}
                        <div className="flex flex-col justify-between gap-4 border-b-2 border-gray-50 bg-gray-50/30 p-8 md:flex-row md:items-center dark:bg-neutral-700/30">
                            <div className="flex items-center gap-5">
                                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white shadow-xl shadow-rose-100">
                                    <HeartIcon className="h-7 w-7" />
                                </div>
                                <div>
                                    <h3 className="text-2xl font-black tracking-tight text-gray-900 dark:text-neutral-100">
                                        {t(
                                            'page.vitals_create.vitals_biometrics',
                                        )}
                                    </h3>
                                    <p className="text-[10px] font-bold tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.vitals_create.patient_id')}:{' '}
                                        <span className="text-rose-500">
                                            {patient.patient_uid}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 rounded-xl border-2 border-gray-100 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                                <ClockIcon className="h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                <div className="flex flex-col">
                                    <label className="text-[9px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t(
                                            'page.vitals_create.recording_timestamp',
                                        )}
                                    </label>
                                    <input
                                        type="datetime-local"
                                        value={data.recorded_at}
                                        onChange={(e) =>
                                            setData(
                                                'recorded_at',
                                                e.target.value,
                                            )
                                        }
                                        className="border-none p-0 text-xs font-bold outline-none focus:ring-0"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Metric Group 1: Temperature & Respiration */}
                        <div className="grid grid-cols-1 gap-8 p-10 md:grid-cols-2 lg:grid-cols-3">
                            {/* Temperature */}
                            <div className="space-y-2">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.vitals_create.temperature')}{' '}
                                    <span className="text-rose-500">*</span>
                                </label>
                                <div className="relative">
                                    <FireIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="number"
                                        step="0.1"
                                        value={data.temperature}
                                        onChange={(e) =>
                                            setData(
                                                'temperature',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.temperature,
                                        )}
                                        placeholder={t(
                                            'page.vitals_create.temp_placeholder',
                                        )}
                                    />
                                </div>
                                {errors.temperature && (
                                    <p className="mt-1 ml-1 text-[10px] font-bold text-red-500">
                                        {errors.temperature}
                                    </p>
                                )}
                            </div>

                            {/* Respiratory Rate */}
                            <div className="space-y-2">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.vitals_create.respiratory_rate')}{' '}
                                    <span className="text-rose-500">*</span>
                                </label>
                                <div className="relative">
                                    <AdjustmentsHorizontalIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="number"
                                        value={data.respiratory_rate}
                                        onChange={(e) =>
                                            setData(
                                                'respiratory_rate',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.respiratory_rate,
                                        )}
                                        placeholder={t(
                                            'page.vitals_create.resp_placeholder',
                                        )}
                                    />
                                </div>
                            </div>

                            {/* Heart Rate */}
                            <div className="space-y-2">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.vitals_create.heart_rate')}
                                </label>
                                <div className="relative">
                                    <HeartIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                    <input
                                        type="number"
                                        value={data.heart_rate}
                                        onChange={(e) =>
                                            setData(
                                                'heart_rate',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.heart_rate,
                                        )}
                                        placeholder={t(
                                            'page.vitals_create.hr_placeholder',
                                        )}
                                    />
                                </div>
                            </div>

                            {/* Divider for Visual Organization */}
                            <div className="border-t-2 border-gray-50 pt-4 lg:col-span-3">
                                <h4 className="text-[10px] font-black tracking-widest text-rose-500 uppercase">
                                    {t('page.vitals_create.bp_oxygenation')}
                                </h4>
                            </div>

                            {/* Systolic BP */}
                            <div className="space-y-2">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.vitals_create.systolic_bp')}
                                </label>
                                <div className="relative">
                                    <div className="absolute top-1/2 left-4 -translate-y-1/2 text-xs font-black text-gray-300 italic">
                                        SYS
                                    </div>
                                    <input
                                        type="number"
                                        value={data.systolic_bp}
                                        onChange={(e) =>
                                            setData(
                                                'systolic_bp',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.systolic_bp,
                                        )}
                                        placeholder={t(
                                            'page.vitals_create.sys_placeholder',
                                        )}
                                    />
                                </div>
                            </div>

                            {/* Diastolic BP */}
                            <div className="space-y-2">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.vitals_create.diastolic_bp')}
                                </label>
                                <div className="relative">
                                    <div className="absolute top-1/2 left-4 -translate-y-1/2 text-xs font-black text-gray-300 italic">
                                        DIA
                                    </div>
                                    <input
                                        type="number"
                                        value={data.diastolic_bp}
                                        onChange={(e) =>
                                            setData(
                                                'diastolic_bp',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.diastolic_bp,
                                        )}
                                        placeholder={t(
                                            'page.vitals_create.dia_placeholder',
                                        )}
                                    />
                                </div>
                            </div>

                            {/* Oxygen Saturation */}
                            <div className="space-y-2">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.vitals_create.oxygen_saturation')}
                                </label>
                                <div className="relative">
                                    <div className="absolute top-1/2 left-4 -translate-y-1/2 text-xs font-black text-gray-400 dark:text-neutral-500">
                                        O₂
                                    </div>
                                    <input
                                        type="number"
                                        value={data.oxygen_saturation}
                                        onChange={(e) =>
                                            setData(
                                                'oxygen_saturation',
                                                e.target.value,
                                            )
                                        }
                                        className={inputStyle(
                                            errors.oxygen_saturation,
                                        )}
                                        placeholder={t(
                                            'page.vitals_create.oxy_placeholder',
                                        )}
                                    />
                                </div>
                            </div>

                            {/* Status & Flags */}
                            <div className="grid grid-cols-1 gap-8 rounded-2xl border-2 border-gray-100 bg-gray-50/50 p-6 lg:col-span-3 lg:grid-cols-3 dark:border-neutral-700 dark:bg-neutral-700/50">
                                <div className="space-y-2">
                                    <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t(
                                            'page.vitals_create.clinical_status',
                                        )}
                                    </label>
                                    <div className="flex h-14 items-center gap-4">
                                        <label className="group flex cursor-pointer items-center gap-3">
                                            <input
                                                type="checkbox"
                                                checked={data.is_abnormal}
                                                onChange={(e) =>
                                                    setData(
                                                        'is_abnormal',
                                                        e.target.checked,
                                                    )
                                                }
                                                className="peer hidden"
                                            />
                                            <div className="flex h-6 w-6 items-center justify-center rounded-md border-2 border-gray-300 transition-all peer-checked:border-red-500 peer-checked:bg-red-500">
                                                <CheckIcon className="h-4 w-4 text-white opacity-0 peer-checked:opacity-100" />
                                            </div>
                                            <span className="text-xs font-black text-gray-500 uppercase transition-colors peer-checked:text-red-600 dark:text-neutral-500">
                                                {t(
                                                    'page.vitals_create.mark_abnormal',
                                                )}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div className="space-y-2 lg:col-span-2">
                                    <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t(
                                            'page.vitals_create.observation_notes',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <ChatBubbleBottomCenterTextIcon className="absolute top-4 left-4 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                        <textarea
                                            rows={2}
                                            value={data.notes}
                                            onChange={(e) =>
                                                setData('notes', e.target.value)
                                            }
                                            className="w-full resize-none rounded-lg border-2 border-gray-200 bg-white py-4 pr-4 pl-12 text-sm font-bold transition-all outline-none focus:border-rose-600 dark:border-neutral-700 dark:bg-neutral-800"
                                            placeholder={t(
                                                'page.vitals_create.notes_placeholder',
                                            )}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-6 sm:flex-row">
                        <Link
                            href={route('patients.show', 1)}
                            className="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-gray-200 bg-white px-10 py-5 font-black text-gray-500 transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500 dark:hover:bg-neutral-700"
                        >
                            <ArrowLeftIcon className="h-5 w-5" />{' '}
                            {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-12 py-5 font-black text-white shadow-2xl transition-all hover:bg-black disabled:opacity-50 sm:w-auto dark:bg-neutral-800"
                        >
                            {processing ? (
                                <ArrowPathIcon className="h-6 w-6 animate-spin" />
                            ) : (
                                <ClipboardDocumentCheckIcon className="h-6 w-6" />
                            )}
                            {t('page.vitals_create.commit_record')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
