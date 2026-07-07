import {
    HeartIcon,
    FireIcon,
    AdjustmentsHorizontalIcon,
    ClockIcon,
    ClipboardDocumentCheckIcon,
    CheckIcon,
    ArrowPathIcon,
    HomeIcon,
    ChevronRightIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    ChatBubbleBottomCenterTextIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
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
    const { data, setData, post, processing, errors, reset } = useForm({
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

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('vitals.store'), {
            onSuccess: () => {
                setSuccessMessage(t('page.vitals_create.recordedSuccessfully'));
                reset();
            },
            onError: () => {
                setErrorMessage(t('page.vitals_create.pleaseCheckForm'));
            },
        });
    };

    const customInputStyle = (error: string | undefined) => `
        w-full rounded-lg border bg-white py-2 pl-14 pr-3 text-sm outline-none transition-all dark:bg-neutral-700 dark:text-neutral-100 dark:placeholder:text-neutral-500
        ${error ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 focus:ring-2 focus:ring-blue-500 dark:border-neutral-600'}
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

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('page.vitals_create.patient_records')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('page.vitals_create.vitals_entry')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <h3 className="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    {t('common.success')}
                                </h3>
                                <div className="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                    {successMessage}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSuccessMessage(null)}
                            className="rounded-lg p-1.5 text-emerald-500 transition hover:text-emerald-700 dark:hover:text-emerald-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <div className="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div className="flex items-center gap-3">
                            <ExclamationTriangleIcon className="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('common.error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage || t('common.someFieldsRequireAttention')}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setErrorMessage(null)}
                            className="rounded-lg p-1.5 text-red-500 transition hover:text-red-700 dark:hover:text-red-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {/* Main Form Card */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={handleSubmit} className="space-y-8 p-6">
                        {/* Section 1: Vitals & Biometrics */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <HeartIcon className="h-5 w-5" />
                                {t('page.vitals_create.vitals_biometrics')}
                            </h3>

                            {/* Recording Timestamp */}
                            <div className="inline-flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-neutral-700 dark:bg-neutral-800">
                                <ClockIcon className="h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                <div className="flex flex-col">
                                    <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.vitals_create.recording_timestamp')}
                                    </span>
                                    <input
                                        type="datetime-local"
                                        value={data.recorded_at}
                                        onChange={(e) => setData('recorded_at', e.target.value)}
                                        className="border-none p-0 text-sm font-semibold outline-none focus:ring-0 dark:bg-neutral-800 dark:text-neutral-100"
                                    />
                                </div>
                            </div>

                            {/* Patient Info */}
                            <p className="text-xs text-gray-500 dark:text-neutral-400">
                                {t('page.vitals_create.patient_id')}: {patient.patient_uid}
                            </p>

                            {/* Metric Grid */}
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.temperature')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <FireIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            step="0.1"
                                            value={data.temperature}
                                            onChange={(e) => setData('temperature', e.target.value)}
                                            placeholder={t('page.vitals_create.temp_placeholder')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.temperature ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.temperature && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.temperature}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.respiratory_rate')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <AdjustmentsHorizontalIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.respiratory_rate}
                                            onChange={(e) => setData('respiratory_rate', e.target.value)}
                                            placeholder={t('page.vitals_create.resp_placeholder')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.respiratory_rate ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.respiratory_rate && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.respiratory_rate}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.heart_rate')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <HeartIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.heart_rate}
                                            onChange={(e) => setData('heart_rate', e.target.value)}
                                            placeholder={t('page.vitals_create.hr_placeholder')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.heart_rate ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.heart_rate && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.heart_rate}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* BP & Oxygenation Divider */}
                            <div className="border-t border-gray-100 pt-4 dark:border-neutral-700">
                                <h4 className="text-xs font-semibold tracking-wide text-blue-600 uppercase dark:text-blue-400">
                                    {t('page.vitals_create.bp_oxygenation')}
                                </h4>
                            </div>

                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                {/* Systolic BP */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.systolic_bp')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span className="text-xs font-semibold text-gray-400 italic dark:text-neutral-500">SYS</span>
                                        </div>
                                        <input
                                            type="number"
                                            value={data.systolic_bp}
                                            onChange={(e) => setData('systolic_bp', e.target.value)}
                                            className={customInputStyle(errors.systolic_bp)}
                                            placeholder={t('page.vitals_create.sys_placeholder')}
                                        />
                                    </div>
                                    {errors.systolic_bp && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.systolic_bp}
                                        </p>
                                    )}
                                </div>

                                {/* Diastolic BP */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.diastolic_bp')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span className="text-xs font-semibold text-gray-400 italic dark:text-neutral-500">DIA</span>
                                        </div>
                                        <input
                                            type="number"
                                            value={data.diastolic_bp}
                                            onChange={(e) => setData('diastolic_bp', e.target.value)}
                                            className={customInputStyle(errors.diastolic_bp)}
                                            placeholder={t('page.vitals_create.dia_placeholder')}
                                        />
                                    </div>
                                    {errors.diastolic_bp && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.diastolic_bp}
                                        </p>
                                    )}
                                </div>

                                {/* Oxygen Saturation */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.oxygen_saturation')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span className="text-xs font-semibold text-gray-400 dark:text-neutral-500">O₂</span>
                                        </div>
                                        <input
                                            type="number"
                                            value={data.oxygen_saturation}
                                            onChange={(e) => setData('oxygen_saturation', e.target.value)}
                                            className={customInputStyle(errors.oxygen_saturation)}
                                            placeholder={t('page.vitals_create.oxy_placeholder')}
                                        />
                                    </div>
                                    {errors.oxygen_saturation && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.oxygen_saturation}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Status & Notes */}
                            <div className="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50/50 p-4 lg:grid-cols-3 dark:border-neutral-700 dark:bg-neutral-700/50">
                                {/* Abnormal Checkbox */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.clinical_status')}
                                    </label>
                                    <div className="flex items-center gap-4 pt-1">
                                        <label className="group flex cursor-pointer items-center gap-3">
                                            <input
                                                type="checkbox"
                                                checked={data.is_abnormal}
                                                onChange={(e) => setData('is_abnormal', e.target.checked)}
                                                className="peer hidden"
                                            />
                                            <div className="flex h-6 w-6 items-center justify-center rounded-md border-2 border-gray-300 transition-all peer-checked:border-red-500 peer-checked:bg-red-500 dark:border-neutral-600">
                                                <CheckIcon className="h-4 w-4 text-white opacity-0 peer-checked:opacity-100" />
                                            </div>
                                            <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase transition-colors peer-checked:text-red-600 dark:text-neutral-400 dark:peer-checked:text-red-400">
                                                {t('page.vitals_create.mark_abnormal')}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                {/* Notes */}
                                <div className="space-y-1 lg:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.vitals_create.observation_notes')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute top-3 left-0 flex items-start pl-3">
                                            <ChatBubbleBottomCenterTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <textarea
                                            value={data.notes}
                                            onChange={(e) => setData('notes', e.target.value)}
                                            rows={2}
                                            placeholder={t('page.vitals_create.notes_placeholder')}
                                            className={`w-full resize-none rounded-lg border px-3 py-2 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.notes ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.notes && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.notes}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={route('patients.show', patient.id)}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </Link>

                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        {t('page.vitals_create.saving')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <ClipboardDocumentCheckIcon className="h-4 w-4" />
                                        {t('page.vitals_create.commit_record')}
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
