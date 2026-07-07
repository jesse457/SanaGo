import {
    HomeIcon,
    ChevronRightIcon,
    UserIcon,
    MapPinIcon,
    CalendarDaysIcon,
    DocumentTextIcon,
    ArrowLeftIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    ArrowRightOnRectangleIcon,
    InboxIcon,
    FolderOpenIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import React from 'react';
import { toast, Toaster } from 'sonner';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    gender: string;
}

interface Ward {
    name: string;
}

interface BedType {
    name: string;
}

interface Bed {
    code?: string;
    bed_number?: string;
    ward?: Ward;
    bed_type?: BedType;
    bedType?: BedType;
}

interface Doctor {
    id: number;
    name: string;
}

interface Admission {
    id: number;
    admission_date: string;
    discharge_date: string | null;
    status: 'Pending' | 'Admitted' | 'Discharged';
    reason_for_admission: string;
    attending_doctor?: Doctor;
    attendingDoctor?: Doctor;
    bed?: Bed;
}

interface DropdownAdmission {
    id: number;
    admission_date: string;
    status: string;
}

interface Props {
    patient: Patient;
    admissions: DropdownAdmission[];
    selectedAdmission: Admission | null;
}

export default function AdmissionDetails({
    patient,
    admissions,
    selectedAdmission,
}: Props) {
    const { t } = useTranslation();

    const statusBannerColors = {
        Admitted: 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300',
        Discharged: 'bg-slate-100 border-slate-200 text-slate-600 dark:bg-neutral-800/40 dark:border-neutral-700 dark:text-slate-400',
        Pending: 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300',
    };

    // Form setup for the dedicated discharge route
    const { post, processing } = useForm();

    const handleAdmissionChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const id = e.target.value;
        router.get(route('admissions.show', id), {}, { preserveState: true });
    };

    const handleDischarge = () => {
        if (!selectedAdmission) return;

        // Triggers a POST request to admissions.discharge
        post(route('admissions.discharge', selectedAdmission.id), {
            onSuccess: () => {
                toast.success(t('flash.admission.discharged') || 'Patient discharged successfully.');
            },
            onError: () => {
                toast.error(t('flash.error') || 'Failed to complete discharge operation.');
            },
        });
    };

    // Helper references for rendering fields cleanly
    const doctorName = selectedAdmission?.attending_doctor?.name || selectedAdmission?.attendingDoctor?.name;
    const bedDesignation = selectedAdmission?.bed?.bed_number || selectedAdmission?.bed?.code || 'N/A';
    const wardName = selectedAdmission?.bed?.ward?.name;

    return (
        <AppLayout
            headerTitle={t('page.checkin.title') || 'Admission Overview'}
            headerSubtitle={t('page.checkin.subtitle') || 'Detailed view of patient admission history and current status.'}
        >
            <Head title={t('page.checkin.admission_details') || 'Admission Overview'} />
            <Toaster position="top-right" richColors />

            <div className="max-w-7xl mx-auto space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* 1. Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        {
                            label: t('page.checkin.breadcrumb_admissions') || 'Admissions',
                            href: route('admissions.index'),
                        },
                        { label: t('page.checkin.details') || 'Admission Details' },
                    ]}
                />

                {/* 2. Main Content Card */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">

                    {/* Patient Header Info */}
                    <div className="px-6 py-5 bg-gray-50/50 dark:bg-neutral-900/20 border-b border-gray-150 dark:border-neutral-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-lg ring-4 ring-white dark:ring-neutral-800 shadow-md">
                                {patient.first_name.charAt(0)}
                                {patient.last_name.charAt(0)}
                            </div>
                            <div>
                                <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                                    {patient.first_name} {patient.last_name}
                                </h2>
                                <div className="flex items-center gap-3 text-sm text-gray-500 dark:text-neutral-400 font-mono mt-0.5">
                                    <span className="bg-white dark:bg-neutral-700 px-1.5 py-0.5 rounded text-xs font-bold text-gray-700 dark:text-neutral-300 border border-gray-100 dark:border-neutral-600">
                                        {patient.patient_uid}
                                    </span>
                                    <span className="w-1 h-1 bg-gray-400 rounded-full"></span>
                                    <span className="capitalize">{t(`common.${patient.gender}`) || patient.gender}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="p-6 md:p-8">
                        {admissions.length > 0 ? (
                            <>
                                {/* Admission Selector */}
                                <div className="mb-8 max-w-md space-y-1">
                                    <label htmlFor="admissionSelector" className="block text-sm font-bold text-gray-700 dark:text-neutral-300">
                                        {t('page.checkin.select_admission_record') || 'Select Admission Record'}
                                    </label>
                                    <div className="relative">
                                        <select
                                            id="admissionSelector"
                                            value={selectedAdmission?.id || ''}
                                            onChange={handleAdmissionChange}
                                            className="block w-full appearance-none rounded-lg border border-gray-300 py-2.5 pl-3 pr-10 text-sm shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-750 dark:bg-neutral-700 dark:text-white"
                                        >
                                            {admissions.map((option) => (
                                                <option key={option.id} value={option.id}>
                                                    {option.admission_date} — {option.status}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                {selectedAdmission ? (
                                    <>
                                        {/* Status Banner */}
                                        <div className={`mb-8 flex items-center justify-between p-4 rounded-xl border shadow-sm ${statusBannerColors[selectedAdmission.status] || ''}`}>
                                            <div className="flex items-center gap-3">
                                                <div className="p-1 rounded-full border-2 border-current">
                                                    <CheckCircleIcon className="w-5 h-5" />
                                                </div>
                                                <span className="font-bold text-lg">{selectedAdmission.status}</span>
                                            </div>
                                            <span className="text-xs font-bold opacity-75 uppercase tracking-wider">
                                                {t('page.checkin.current_status') || 'Current Status'}
                                            </span>
                                        </div>

                                        {/* Details Grid */}
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

                                            {/* Doctor Card */}
                                            <div className="bg-gray-50/50 dark:bg-neutral-900/10 p-5 rounded-xl border border-gray-100 dark:border-neutral-700 flex items-start gap-4 transition-colors">
                                                <div className="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400 shadow-sm">
                                                    <UserIcon className="w-6 h-6" />
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                                        {t('page.checkin.assigned_doctor') || 'Assigned Doctor'}
                                                    </p>
                                                    <p className="text-base font-bold text-gray-900 dark:text-white mt-1">
                                                        {doctorName ?? t('common.not_assigned') ?? 'Not Assigned'}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Bed Card */}
                                            <div className="bg-gray-50/50 dark:bg-neutral-900/10 p-5 rounded-xl border border-gray-100 dark:border-neutral-700 flex items-start gap-4 transition-colors">
                                                <div className="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400 shadow-sm">
                                                    <MapPinIcon className="w-6 h-6" />
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                                        {t('page.checkin.bed_allocation') || 'Bed Allocation'}
                                                    </p>
                                                    <p className="text-base font-bold text-gray-900 dark:text-white mt-1">
                                                        {t('page.checkin.bed') || 'Bed'} {bedDesignation}
                                                        <span className="text-sm font-medium text-gray-500 dark:text-neutral-400 ml-1">
                                                            ({wardName ?? t('common.ward_na') ?? 'Ward N/A'})
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Admission Date Card */}
                                            <div className="bg-gray-50/50 dark:bg-neutral-900/10 p-5 rounded-xl border border-gray-100 dark:border-neutral-700 flex items-start gap-4 transition-colors">
                                                <div className="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400 shadow-sm">
                                                    <CalendarDaysIcon className="w-6 h-6" />
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                                        {t('page.checkin.admission_date') || 'Admission Date'}
                                                    </p>
                                                    <p className="text-base font-bold text-gray-900 dark:text-white mt-1">
                                                        {selectedAdmission.admission_date}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Discharge Date Card */}
                                            <div className="bg-gray-50/50 dark:bg-neutral-900/10 p-5 rounded-xl border border-gray-100 dark:border-neutral-700 flex items-start gap-4 transition-colors">
                                                <div className="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg text-rose-600 dark:text-rose-400 shadow-sm">
                                                    <ArrowRightOnRectangleIcon className="w-6 h-6" />
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                                        {t('page.checkin.discharge_date') || 'Discharge Date'}
                                                    </p>
                                                    <p className="text-base font-bold text-gray-900 dark:text-white mt-1">
                                                        {selectedAdmission.discharge_date ?? t('page.checkin.ongoing') ?? 'Ongoing'}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Reason Card */}
                                            <div className="md:col-span-2 bg-gray-50/50 dark:bg-neutral-900/10 p-5 rounded-xl border border-gray-100 dark:border-neutral-700 flex items-start gap-4">
                                                <div className="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg text-amber-600 dark:text-amber-400 shadow-sm shrink-0">
                                                    <DocumentTextIcon className="w-6 h-6" />
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                                                        {t('page.checkin.reason_for_admission') || 'Reason for Admission'}
                                                    </p>
                                                    <p className="text-sm text-gray-700 dark:text-neutral-300 mt-1 leading-relaxed">
                                                        {selectedAdmission.reason_for_admission}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Actions Footer */}
                                        <div className="mt-8 pt-6 border-t border-gray-100 dark:border-neutral-700 flex flex-col sm:flex-row justify-end gap-3">
                                            <Link
                                                href={route('admissions.index')}
                                                className="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50 sm:w-auto dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                            >
                                                <ArrowLeftIcon className="h-4 w-4 text-gray-500" />
                                                {t('common.back_to_list') || 'Back to List'}
                                            </Link>

                                            {selectedAdmission.status === 'Admitted' ? (
                                                <button
                                                    onClick={handleDischarge}
                                                    disabled={processing}
                                                    className="flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-red-700 disabled:opacity-50 sm:w-auto dark:bg-red-500 dark:hover:bg-red-600"
                                                >
                                                    {processing ? (
                                                        <ArrowPathIcon className="h-4 w-4 animate-spin text-white" />
                                                    ) : (
                                                        <ArrowRightOnRectangleIcon className="h-4 w-4" />
                                                    )}
                                                    {t('page.checkin.discharge_patient') || 'Discharge Patient'}
                                                </button>
                                            ) : (
                                                <button
                                                    disabled
                                                    className="flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-400 cursor-not-allowed sm:w-auto dark:bg-neutral-800 dark:text-neutral-500"
                                                >
                                                    <CheckCircleIcon className="w-5 h-5" />
                                                    {t('page.checkin.patient_discharged') || 'Patient Discharged'}
                                                </button>
                                            )}
                                        </div>
                                    </>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-200 dark:border-neutral-700 rounded-xl bg-gray-50 dark:bg-neutral-900/10">
                                        <InboxIcon className="w-16 h-16 text-gray-300 dark:text-neutral-600 mb-4" />
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-white">
                                            {t('page.checkin.no_record_selected') || 'No record selected'}
                                        </h3>
                                        <p className="text-gray-500 dark:text-neutral-400">
                                            {t('page.checkin.select_to_view') || 'Please select an admission record from the dropdown above.'}
                                        </p>
                                    </div>
                                )}
                            </>
                        ) : (
                            /* Empty State */
                            <div className="flex flex-col items-center justify-center py-16 text-center">
                                <div className="h-20 w-20 bg-gray-50 dark:bg-neutral-900/20 rounded-full flex items-center justify-center mb-4 border border-gray-150 dark:border-neutral-700">
                                    <FolderOpenIcon className="w-10 h-10 text-gray-400 dark:text-neutral-500" />
                                </div>
                                <h3 className="text-lg font-bold text-gray-900 dark:text-white">
                                    {t('page.checkin.no_records_found') || 'No Admission Records Found'}
                                </h3>
                                <p className="mt-2 text-sm text-gray-500 dark:text-neutral-400 max-w-sm">
                                    {t('page.checkin.no_history') || 'This patient has no admission history on file.'}
                                </p>
                                <Link
                                    href={route('admissions.index')}
                                    className="mt-6 inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-500 dark:text-blue-400"
                                >
                                    <ArrowLeftIcon className="w-4 h-4 mr-1" />
                                    {t('page.checkin.return_to_list') || 'Return to Patient List'}
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
