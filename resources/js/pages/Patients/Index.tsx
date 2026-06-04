import {
    PencilSquareIcon,
    MagnifyingGlassIcon,
    UserPlusIcon,
    CalendarDaysIcon,
    ClipboardDocumentListIcon,
    ArrowPathIcon,
    UserGroupIcon,
    ClockIcon,
    UserMinusIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useState, useMemo, useEffect } from 'react';
import { toast, Toaster } from 'sonner';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    age: number;
    photo_url: string | null;
    gender: 'male' | 'female' | 'other';
    address: string | null;
    last_visit_date: string | null;
}

interface Props {
    patients: Patient[];
    filters: { search?: string };
}

export default function PatientManagement({ patients, filters }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
    const [isSearching, setIsSearching] = useState(false);
    const [modalOpen, setModalOpen] = useState(false);
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        null,
    );

    const { data, setData, put, processing, errors, reset, clearErrors } =
        useForm({
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            age: 0,
            gender: 'male' as 'male' | 'female' | 'other',
            address: '',
        });

    useEffect(() => {
        if (searchQuery !== filters.search) {
            setIsSearching(true);
            const timer = setTimeout(() => {
                router.get(
                    route('patients.index'),
                    { search: searchQuery },
                    {
                        preserveState: true,
                        replace: true,
                        onFinish: () => setIsSearching(false),
                    },
                );
            }, 400);

            return () => clearTimeout(timer);
        }
    }, [searchQuery]);

    const openEditModal = (patient: Patient) => {
        setSelectedPatient(patient);
        setData({
            first_name: patient.first_name,
            last_name: patient.last_name,
            email: patient.email || '',
            phone: patient.phone || '',
            age: patient.age,
            gender: patient.gender,
            address: patient.address || '',
        });
        clearErrors();
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setSelectedPatient(null);
        reset();
    };

    const navigateToProfile = (patient: Patient) => {
        router.get(`patients/${patient.id}/consultations`);
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (!selectedPatient) {
            return;
        }

        put(route('patients.update', selectedPatient.id), {
            onSuccess: () => {
                closeModal();
                toast.success(t('flash.patient.updated'));
            },
        });
    };

    const stats = useMemo(
        () => ({
            total: patients.length,
            newPatients: patients.filter((p) => !p.last_visit_date).length,
            recentVisits: patients.filter((p) => {
                if (!p.last_visit_date) {
                    return false;
                }

                const daysDiff =
                    (new Date().getTime() -
                        new Date(p.last_visit_date).getTime()) /
                    (1000 * 3600 * 24);

                return daysDiff <= 30;
            }).length,
            totalVisits: patients.reduce(
                (acc, p) => acc + (p.last_visit_date ? 1 : 0),
                0,
            ),
        }),
        [patients],
    );

    const formatDate = (dateString: string | null) => {
        if (!dateString) {
            return t('common.never');
        }

        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.patients_index.title')}
            headerSubtitle={t('page.patients_index.subtitle')}
        >
            <Head title={t('page.patients_index.title')} />
            <Toaster position="top-right" richColors />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.patients_index.title') },
                    ]}
                />

                {/* Stats Overview */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('page.patients_index.total_patients')}
                        value={stats.total}
                        icon={UserGroupIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('page.patients_index.new_patients')}
                        value={stats.newPatients}
                        icon={UserPlusIcon}
                        color="emerald"
                    />
                    <StatCard
                        label={t('page.patients_index.recent_visits')}
                        value={stats.recentVisits}
                        icon={ClockIcon}
                        color="purple"
                    />
                    <StatCard
                        label={t('page.patients_index.active_records')}
                        value={stats.totalVisits}
                        icon={ClipboardDocumentListIcon}
                        color="amber"
                    />
                </div>

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            placeholder={t(
                                'page.patients_index.search_placeholder',
                            )}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full rounded-xl border border-gray-200 bg-white py-2.5 pr-10 pl-9 text-sm transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        {isSearching && (
                            <ArrowPathIcon className="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 animate-spin text-blue-500" />
                        )}
                    </div>

                    <div className="flex items-center gap-3">
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                        <Link
                            href={route('patients.create')}
                            className="flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold whitespace-nowrap text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <UserPlusIcon className="h-4 w-4" />{' '}
                            {t('page.patients_index.add_patient')}
                        </Link>
                    </div>
                </div>

                {/* Content: Grid or Table */}
                {viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {patients.map((patient) => (
                            <div
                                key={patient.id}
                                className="group relative rounded-xl border border-gray-200 bg-white p-6 transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                            >
                                <div className="mb-4 flex items-start justify-between">
                                    <div className="h-12 w-12 overflow-hidden rounded-full ring-2 ring-white dark:ring-neutral-700">
                                        {patient.photo_url ? (
                                            <img
                                                src={patient.photo_url}
                                                alt=""
                                                className="h-full w-full object-cover"
                                            />
                                        ) : (
                                            <div className="flex h-full w-full items-center justify-center bg-blue-100 text-sm font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                {patient.first_name.charAt(0)}
                                                {patient.last_name.charAt(0)}
                                            </div>
                                        )}
                                    </div>
                                    <StatusBadge
                                        label={
                                            patient.last_visit_date
                                                ? t('common.active')
                                                : t('common.new')
                                        }
                                        color={
                                            patient.last_visit_date
                                                ? 'emerald'
                                                : 'amber'
                                        }
                                    />
                                </div>
                                <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                    {patient.first_name} {patient.last_name}
                                </h3>
                                <div className="mt-1 mb-4 flex items-center gap-2">
                                    <span className="rounded bg-blue-50 px-1.5 py-0.5 font-mono text-xs font-bold text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                        {patient.patient_uid}
                                    </span>
                                    <span className="text-xs font-bold tracking-tight text-gray-400 uppercase dark:text-neutral-500">
                                        {patient.gender}
                                    </span>
                                </div>
                                <div className="mb-4 flex items-center justify-between border-t border-gray-100 py-4 dark:border-neutral-700">
                                    <div className="flex gap-4">
                                        <div className="text-center">
                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {patient.age}
                                            </p>
                                            <p className="text-[10px] font-bold tracking-tight text-gray-400 uppercase dark:text-neutral-500">
                                                {t('page.patients_index.years')}
                                            </p>
                                        </div>
                                        <div className="text-center">
                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {patient.phone?.slice(-4) ||
                                                    '--'}
                                            </p>
                                            <p className="text-[10px] font-bold tracking-tight text-gray-400 uppercase dark:text-neutral-500">
                                                {t('page.patients_index.phone')}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-1.5 rounded-lg bg-gray-50 px-3 py-1.5 text-[10px] font-bold text-gray-500 dark:bg-neutral-700/50 dark:text-neutral-400">
                                        <CalendarDaysIcon className="h-3.5 w-3.5" />{' '}
                                        {formatDate(patient.last_visit_date)}
                                    </div>
                                </div>
                                <div className="flex gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        onClick={() =>
                                            navigateToProfile(patient)
                                        }
                                        className="flex-1 rounded-lg bg-gray-900 py-2 text-xs font-bold text-white transition-colors hover:bg-black dark:bg-neutral-700 dark:hover:bg-neutral-600"
                                    >
                                        {t('page.patients_index.records')}
                                    </button>
                                    <button
                                        onClick={() => openEditModal(patient)}
                                        className="rounded-lg bg-gray-50 px-3 py-2 text-gray-400 transition-colors hover:text-blue-600 dark:bg-neutral-700"
                                    >
                                        <PencilSquareIcon className="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead className="bg-gray-50 dark:bg-neutral-800">
                                <tr>
                                    <th className="px-6 py-4 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.patients_index.table_patient_details',
                                        )}
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.patients_index.table_contact')}
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.patients_index.table_demographics',
                                        )}
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t(
                                            'page.patients_index.table_last_visit',
                                        )}
                                    </th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                {patients.map((patient) => (
                                    <tr
                                        key={patient.id}
                                        className="transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center gap-4">
                                                <div className="h-10 w-10 overflow-hidden rounded-full">
                                                    {patient.photo_url ? (
                                                        <img
                                                            src={
                                                                patient.photo_url
                                                            }
                                                            alt=""
                                                            className="h-full w-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="flex h-full w-full items-center justify-center bg-blue-100 text-xs font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                            {
                                                                patient
                                                                    .first_name[0]
                                                            }
                                                        </div>
                                                    )}
                                                </div>
                                                <div>
                                                    <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {patient.first_name}{' '}
                                                        {patient.last_name}
                                                    </div>
                                                    <div className="font-mono text-[10px] font-bold text-blue-600 dark:text-blue-400">
                                                        {patient.patient_uid}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-xs font-bold whitespace-nowrap text-gray-500 dark:text-neutral-400">
                                            {patient.phone}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <StatusBadge
                                                label={`${patient.age} ${t('page.patients_index.yrs')} • ${patient.gender}`}
                                                color="gray"
                                            />
                                        </td>
                                        <td className="px-6 py-4 text-xs font-bold tracking-tight whitespace-nowrap text-gray-400 uppercase dark:text-neutral-500">
                                            {formatDate(
                                                patient.last_visit_date,
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <button
                                                onClick={() =>
                                                    openEditModal(patient)
                                                }
                                                className="p-2 text-gray-400 transition-colors hover:text-blue-600"
                                            >
                                                <PencilSquareIcon className="h-5 w-5" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {/* Empty State */}
                {patients.length === 0 && (
                    <EmptyState
                        icon={UserMinusIcon}
                        title={t('page.patients_index.empty_title')}
                        description={t('page.patients_index.empty_description')}
                        action={{
                            label: t('page.patients_index.add_patient'),
                            href: route('patients.create'),
                        }}
                    />
                )}
            </div>

            {/* Edit Modal */}
            <Modal
                isOpen={modalOpen}
                onClose={closeModal}
                title={t('page.patients_index.edit_patient')}
            >
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="col-span-2">
                            <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                {t('page.patients_index.patient_uid')}
                            </label>
                            <input
                                type="text"
                                value={selectedPatient?.patient_uid || ''}
                                readOnly
                                className="mt-1.5 w-full rounded-xl border-none bg-gray-100 px-4 py-3 text-sm font-bold text-gray-500 dark:bg-neutral-700 dark:text-neutral-400"
                            />
                        </div>
                        <FormInput
                            name="first_name"
                            label={t('page.patients_index.first_name')}
                            value={data.first_name}
                            onChange={(v) => setData('first_name', v)}
                            errors={errors}
                            required
                            className="col-span-2 sm:col-span-1"
                        />
                        <FormInput
                            name="last_name"
                            label={t('page.patients_index.last_name')}
                            value={data.last_name}
                            onChange={(v) => setData('last_name', v)}
                            errors={errors}
                            required
                            className="col-span-2 sm:col-span-1"
                        />
                        <FormSelect
                            name="gender"
                            label={t('page.patients_index.gender')}
                            value={data.gender}
                            onChange={(v) =>
                                setData(
                                    'gender',
                                    v as 'male' | 'female' | 'other',
                                )
                            }
                            errors={errors}
                            options={[
                                { value: 'male', label: t('common.male') },
                                { value: 'female', label: t('common.female') },
                                { value: 'other', label: t('common.other') },
                            ]}
                            className="col-span-2 sm:col-span-1"
                        />
                        <FormInput
                            name="age"
                            label={t('page.patients_index.age')}
                            type="number"
                            value={data.age}
                            onChange={(v) => setData('age', parseInt(v) || 0)}
                            errors={errors}
                            className="col-span-2 sm:col-span-1"
                        />
                        <FormInput
                            name="phone"
                            label={t('page.patients_index.phone_number')}
                            value={data.phone}
                            onChange={(v) => setData('phone', v)}
                            errors={errors}
                            className="col-span-2"
                        />
                        <FormTextarea
                            name="address"
                            label={t('page.patients_index.address')}
                            value={data.address}
                            onChange={(v) => setData('address', v)}
                            errors={errors}
                            rows={2}
                            className="col-span-2"
                        />
                    </div>
                    <div className="flex gap-4 border-t border-gray-100 pt-4 dark:border-neutral-700">
                        <button
                            type="button"
                            onClick={closeModal}
                            className="flex-1 rounded-xl border border-gray-200 bg-white py-4 font-bold text-gray-400 transition-all hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.cancel')}
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex-[2] rounded-xl bg-blue-600 py-4 font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            {processing
                                ? t('common.processing')
                                : t('common.save_changes')}
                        </button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
