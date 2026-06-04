import {
    HeartIcon,
    FireIcon,
    AdjustmentsHorizontalIcon,
    ClockIcon,
    ClipboardDocumentCheckIcon,
    CheckIcon,
    ArrowPathIcon,
    ArrowLeftIcon,
    ChatBubbleBottomCenterTextIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React from 'react';
import FormInput from '@/Components/FormInput';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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
        recorded_at: new Date().toISOString().slice(0, 16),
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

    const customInputStyle = (error: string | undefined) => `
        w-full rounded-lg border bg-white py-3 pl-12 pr-4 text-sm font-semibold outline-none transition-all dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500
        ${error ? 'border-red-500 focus:ring-2 focus:ring-red-500' : 'border-gray-200 focus:ring-2 focus:ring-blue-500 dark:border-neutral-700'}
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

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        {
                            label: t('page.vitals_create.patient_records'),
                            href: route('patients.show', patient.id),
                        },
                        { label: t('page.vitals_create.vitals_entry') },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <PageSection
                        icon={HeartIcon}
                        title={t('page.vitals_create.vitals_biometrics')}
                        description={`${t('page.vitals_create.patient_id')}: ${patient.patient_uid}`}
                    >
                        {/* Recording Timestamp */}
                        <div className="border-b border-gray-100 px-6 py-4 dark:border-neutral-700">
                            <div className="inline-flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-neutral-700 dark:bg-neutral-800">
                                <ClockIcon className="h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                <div className="flex flex-col">
                                    <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.vitals_create.recording_timestamp',
                                        )}
                                    </span>
                                    <input
                                        type="datetime-local"
                                        value={data.recorded_at}
                                        onChange={(e) =>
                                            setData(
                                                'recorded_at',
                                                e.target.value,
                                            )
                                        }
                                        className="border-none p-0 text-sm font-semibold outline-none focus:ring-0 dark:bg-neutral-800 dark:text-neutral-100"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Metric Grid */}
                        <div className="p-6">
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <FormInput
                                    name="temperature"
                                    label={t('page.vitals_create.temperature')}
                                    type="number"
                                    step="0.1"
                                    icon={FireIcon}
                                    required
                                    errors={errors}
                                    value={data.temperature}
                                    onChange={(val) =>
                                        setData('temperature', val)
                                    }
                                    placeholder={t(
                                        'page.vitals_create.temp_placeholder',
                                    )}
                                />

                                <FormInput
                                    name="respiratory_rate"
                                    label={t(
                                        'page.vitals_create.respiratory_rate',
                                    )}
                                    type="number"
                                    icon={AdjustmentsHorizontalIcon}
                                    required
                                    errors={errors}
                                    value={data.respiratory_rate}
                                    onChange={(val) =>
                                        setData('respiratory_rate', val)
                                    }
                                    placeholder={t(
                                        'page.vitals_create.resp_placeholder',
                                    )}
                                />

                                <FormInput
                                    name="heart_rate"
                                    label={t('page.vitals_create.heart_rate')}
                                    type="number"
                                    icon={HeartIcon}
                                    errors={errors}
                                    value={data.heart_rate}
                                    onChange={(val) =>
                                        setData('heart_rate', val)
                                    }
                                    placeholder={t(
                                        'page.vitals_create.hr_placeholder',
                                    )}
                                />
                            </div>

                            {/* BP & Oxygenation Divider */}
                            <div className="my-6 border-t border-gray-100 pt-6 dark:border-neutral-700">
                                <h4 className="text-xs font-semibold tracking-wide text-blue-600 uppercase dark:text-blue-400">
                                    {t('page.vitals_create.bp_oxygenation')}
                                </h4>
                            </div>

                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                {/* Systolic BP */}
                                <div>
                                    <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.vitals_create.systolic_bp')}
                                    </span>
                                    <div className="relative mt-1.5">
                                        <div className="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-xs font-semibold text-gray-400 italic dark:text-neutral-500">
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
                                            className={customInputStyle(
                                                errors.systolic_bp,
                                            )}
                                            placeholder={t(
                                                'page.vitals_create.sys_placeholder',
                                            )}
                                        />
                                    </div>
                                    {errors.systolic_bp && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.systolic_bp}
                                        </p>
                                    )}
                                </div>

                                {/* Diastolic BP */}
                                <div>
                                    <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.vitals_create.diastolic_bp')}
                                    </span>
                                    <div className="relative mt-1.5">
                                        <div className="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-xs font-semibold text-gray-400 italic dark:text-neutral-500">
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
                                            className={customInputStyle(
                                                errors.diastolic_bp,
                                            )}
                                            placeholder={t(
                                                'page.vitals_create.dia_placeholder',
                                            )}
                                        />
                                    </div>
                                    {errors.diastolic_bp && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.diastolic_bp}
                                        </p>
                                    )}
                                </div>

                                {/* Oxygen Saturation */}
                                <div>
                                    <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.vitals_create.oxygen_saturation',
                                        )}
                                    </span>
                                    <div className="relative mt-1.5">
                                        <div className="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-xs font-semibold text-gray-400 dark:text-neutral-500">
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
                                            className={customInputStyle(
                                                errors.oxygen_saturation,
                                            )}
                                            placeholder={t(
                                                'page.vitals_create.oxy_placeholder',
                                            )}
                                        />
                                    </div>
                                    {errors.oxygen_saturation && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.oxygen_saturation}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Status & Flags */}
                            <div className="mt-6 grid grid-cols-1 gap-6 rounded-xl border border-gray-200 bg-gray-50/50 p-6 lg:grid-cols-3 dark:border-neutral-700 dark:bg-neutral-700/50">
                                {/* Abnormal Checkbox */}
                                <div>
                                    <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.vitals_create.clinical_status',
                                        )}
                                    </span>
                                    <div className="mt-1.5 flex items-center gap-4">
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
                                            <div className="flex h-6 w-6 items-center justify-center rounded-md border-2 border-gray-300 transition-all peer-checked:border-red-500 peer-checked:bg-red-500 dark:border-neutral-600">
                                                <CheckIcon className="h-4 w-4 text-white opacity-0 peer-checked:opacity-100" />
                                            </div>
                                            <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase transition-colors peer-checked:text-red-600 dark:text-neutral-400 dark:peer-checked:text-red-400">
                                                {t(
                                                    'page.vitals_create.mark_abnormal',
                                                )}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                {/* Notes */}
                                <FormTextarea
                                    name="notes"
                                    label={t(
                                        'page.vitals_create.observation_notes',
                                    )}
                                    icon={ChatBubbleBottomCenterTextIcon}
                                    errors={errors}
                                    value={data.notes}
                                    onChange={(val) => setData('notes', val)}
                                    placeholder={t(
                                        'page.vitals_create.notes_placeholder',
                                    )}
                                    rows={2}
                                    className="lg:col-span-2"
                                />
                            </div>
                        </div>
                    </PageSection>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-4 sm:flex-row">
                        <Link
                            href={route('patients.show', patient.id)}
                            className="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-8 py-4 font-bold text-gray-500 shadow-sm transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700"
                        >
                            <ArrowLeftIcon className="h-5 w-5" />
                            {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-10 py-4 font-bold text-white shadow-lg transition-all hover:bg-blue-700 disabled:opacity-50 sm:w-auto"
                        >
                            {processing ? (
                                <ArrowPathIcon className="h-5 w-5 animate-spin" />
                            ) : (
                                <ClipboardDocumentCheckIcon className="h-5 w-5" />
                            )}
                            {t('page.vitals_create.commit_record')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
