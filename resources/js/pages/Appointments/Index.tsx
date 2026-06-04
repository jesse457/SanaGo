import {
    MagnifyingGlassIcon,
    PlusIcon,
    CheckIcon,
    ArrowPathIcon,
    ClockIcon,
    CheckBadgeIcon,
    UserGroupIcon,
    CalendarIcon,
    UserIcon,
    ArrowRightIcon,
    NoSymbolIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import debounce from 'lodash/debounce';
import React, { useState, useMemo } from 'react';

import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    first_name: string;
    last_name: string;
    patient_uid: string;
}

interface Doctor {
    id: number;
    name: string;
    department?: { name: string };
}

interface Appointment {
    id: number;
    appointment_date: string;
    appointment_time: string;
    status:
        | 'Scheduled'
        | 'Waiting'
        | 'In Consultation'
        | 'Completed'
        | 'Canceled';
    queue_position?: number;
    patient: Patient;
    doctor?: Doctor;
}

interface Props {
    appointments: {
        data: Appointment[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    doctors: Doctor[];
    filters: {
        search?: string;
        status?: string;
        doctor_id?: string;
        date?: string;
    };
}

const containerVariants = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

const itemVariants = {
    hidden: { y: 20, opacity: 0 },
    visible: {
        y: 0,
        opacity: 1,
        transition: { type: 'spring' as const, stiffness: 300, damping: 24 },
    },
};

export default function AppointmentIndex({
    appointments,
    doctors,
    filters,
}: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    const [params, setParams] = useState({
        search: filters.search || '',
        status: filters.status || '',
        doctor_id: filters.doctor_id || '',
        date: filters.date || '',
    });

    const stats = useMemo(
        () => ({
            total: appointments.total,
            waiting: appointments.data.filter((a) => a.status === 'Waiting')
                .length,
            scheduled: appointments.data.filter((a) => a.status === 'Scheduled')
                .length,
            completed: appointments.data.filter((a) => a.status === 'Completed')
                .length,
        }),
        [appointments],
    );

    const requestUpdate = useMemo(
        () =>
            debounce((query) => {
                router.get(route('appointments.index'), query, {
                    preserveState: true,
                    replace: true,
                    only: ['appointments'],
                });
            }, 300),
        [],
    );

    const handleFilterChange = (key: string, value: string) => {
        const newParams = { ...params, [key]: value };
        setParams(newParams);
        requestUpdate(newParams);
    };

    const handleStatusUpdate = (id: number, newStatus: string) => {
        router.patch(
            route('appointments.update', id),
            {
                status: newStatus,
            },
            {
                preserveScroll: true,
            },
        );
    };

    const statusColor = (status: string) => {
        switch (status) {
            case 'Waiting':
                return 'amber';
            case 'In Consultation':
                return 'blue';
            case 'Canceled':
                return 'red';
            case 'Completed':
                return 'emerald';
            case 'Scheduled':
                return 'purple';
            default:
                return 'gray';
        }
    };

    return (
        <AppLayout
            headerTitle={t('manageAppointments')}
            headerSubtitle={t('monitorPatientQueues')}
        >
            <Head title={t('appointments')} />

            <motion.div
                initial="hidden"
                animate="visible"
                variants={containerVariants}
                className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8"
            >
                <PageBreadcrumb
                    items={[{ label: t('home') }, { label: t('appointments') }]}
                />

                {/* Stats Overview */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <motion.div
                        initial={{ scale: 0.95, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{
                            delay: 0.1,
                            type: 'spring',
                            stiffness: 200,
                        }}
                    >
                        <StatCard
                            icon={UserGroupIcon}
                            label={t('totalAppointments')}
                            value={stats.total}
                            color="blue"
                        />
                    </motion.div>
                    <motion.div
                        initial={{ scale: 0.95, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{
                            delay: 0.2,
                            type: 'spring',
                            stiffness: 200,
                        }}
                    >
                        <StatCard
                            icon={ClockIcon}
                            label={t('waitingInHall')}
                            value={stats.waiting}
                            color="amber"
                        />
                    </motion.div>
                    <motion.div
                        initial={{ scale: 0.95, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{
                            delay: 0.3,
                            type: 'spring',
                            stiffness: 200,
                        }}
                    >
                        <StatCard
                            icon={CalendarIcon}
                            label={t('todaysScheduled')}
                            value={stats.scheduled}
                            color="purple"
                        />
                    </motion.div>
                    <motion.div
                        initial={{ scale: 0.95, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{
                            delay: 0.4,
                            type: 'spring',
                            stiffness: 200,
                        }}
                    >
                        <StatCard
                            icon={CheckBadgeIcon}
                            label={t('totalCompleted')}
                            value={stats.completed}
                            color="emerald"
                        />
                    </motion.div>
                </div>

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div className="grid w-full grid-cols-1 items-center gap-3 sm:grid-cols-2 lg:flex lg:w-auto">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t('searchPatient')}
                            value={params.search}
                            onChange={(value) =>
                                handleFilterChange('search', value)
                            }
                            className="flex-grow sm:min-w-[240px]"
                        />
                        <FormSelect
                            name="status"
                            value={params.status}
                            onChange={(value) =>
                                handleFilterChange('status', value)
                            }
                            options={[
                                { value: '', label: t('allStatuses') },
                                { value: 'Scheduled', label: t('scheduled') },
                                { value: 'Waiting', label: t('waiting') },
                                {
                                    value: 'In Consultation',
                                    label: t('inConsultation'),
                                },
                                { value: 'Completed', label: t('completed') },
                                { value: 'Canceled', label: t('canceled') },
                            ]}
                        />
                        <FormSelect
                            name="doctor_id"
                            value={params.doctor_id}
                            onChange={(value) =>
                                handleFilterChange('doctor_id', value)
                            }
                            options={[
                                { value: '', label: t('allDoctors') },
                                ...doctors.map((d) => ({
                                    value: d.id.toString(),
                                    label: d.name,
                                })),
                            ]}
                        />
                        <FormInput
                            name="date"
                            type="date"
                            value={params.date}
                            onChange={(value) =>
                                handleFilterChange('date', value)
                            }
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('appointments.create')}
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" /> {t('bookNew')}
                        </Link>
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                    </div>
                </div>

                {/* Main Content */}
                <AnimatePresence mode="wait">
                    {appointments.data.length === 0 ? (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                        >
                            <EmptyState
                                icon={ArrowPathIcon}
                                title={t('noAppointmentsFound')}
                                description={t('adjustFiltersOrBook')}
                            />
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: 20 }}
                            className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                        >
                            {appointments.data.map((apt) => (
                                <motion.div
                                    key={apt.id}
                                    variants={itemVariants}
                                    whileHover={{ y: -4 }}
                                    className="flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-500"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-lg font-bold text-blue-600 shadow-sm dark:bg-blue-900/20 dark:text-blue-400">
                                            {apt.patient.first_name[0]}
                                            {apt.patient.last_name[0]}
                                        </div>
                                        <StatusBadge
                                            label={apt.status}
                                            color={statusColor(apt.status)}
                                            className={
                                                apt.status === 'In Consultation'
                                                    ? 'animate-pulse'
                                                    : ''
                                            }
                                        />
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="mb-1 text-lg leading-tight font-bold text-gray-900 dark:text-neutral-100">
                                            {apt.patient.first_name}{' '}
                                            {apt.patient.last_name}
                                        </h3>
                                        <p className="mb-4 font-mono text-xs text-gray-400 dark:text-neutral-500">
                                            {apt.patient.patient_uid}
                                        </p>
                                        <div className="space-y-2 rounded-lg border border-gray-100 bg-gray-50 p-3 dark:border-neutral-700 dark:bg-neutral-700/50">
                                            <div className="flex items-center gap-2">
                                                <UserIcon className="h-4 w-4 text-blue-500" />
                                                <span className="text-xs font-bold text-gray-700 dark:text-neutral-300">
                                                    {apt.doctor?.name}
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <CalendarIcon className="h-4 w-4 text-gray-400" />
                                                <span className="text-xs font-medium text-gray-600 dark:text-neutral-400">
                                                    {apt.appointment_date} @{' '}
                                                    {apt.appointment_time}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-neutral-700">
                                        <div className="flex gap-2">
                                            {apt.status === 'Scheduled' && (
                                                <button
                                                    onClick={() =>
                                                        handleStatusUpdate(
                                                            apt.id,
                                                            'Waiting',
                                                        )
                                                    }
                                                    className="rounded-md bg-emerald-50 p-1.5 text-emerald-600 transition-colors hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30"
                                                    title={t('checkIn')}
                                                >
                                                    <CheckIcon className="h-4 w-4" />
                                                </button>
                                            )}
                                        </div>
                                        <Link
                                            href={route(
                                                'appointments.show',
                                                apt.id,
                                            )}
                                            className="flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 dark:bg-blue-900/20 dark:text-blue-400"
                                        >
                                            {t('manage')}{' '}
                                            <ArrowRightIcon className="h-4 w-4" />
                                        </Link>
                                    </div>
                                </motion.div>
                            ))}
                        </motion.div>
                    ) : (
                        <motion.div
                            key="table"
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: 1, x: 0 }}
                            exit={{ opacity: 0, x: -20 }}
                            className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-800">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            {t('patient')}
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            {t('doctor')}
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            {t('schedule')}
                                        </th>
                                        <th className="px-6 py-3 text-center text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                            {t('status')}
                                        </th>
                                        <th className="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {appointments.data.map((apt) => (
                                        <tr
                                            key={apt.id}
                                            className="transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                        >
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                                        {
                                                            apt.patient
                                                                .first_name[0]
                                                        }
                                                        {
                                                            apt.patient
                                                                .last_name[0]
                                                        }
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                            {
                                                                apt.patient
                                                                    .first_name
                                                            }{' '}
                                                            {
                                                                apt.patient
                                                                    .last_name
                                                            }
                                                        </div>
                                                        <div className="font-mono text-[10px] text-gray-400 dark:text-neutral-500">
                                                            {
                                                                apt.patient
                                                                    .patient_uid
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                    {apt.doctor?.name}
                                                </div>
                                                <div className="text-[10px] text-gray-400 uppercase dark:text-neutral-500">
                                                    {
                                                        apt.doctor?.department
                                                            ?.name
                                                    }
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-sm font-bold text-gray-800 dark:text-neutral-100">
                                                    {apt.appointment_date}
                                                </div>
                                                <div className="text-[10px] text-gray-500 dark:text-neutral-400">
                                                    {apt.appointment_time}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge
                                                    label={apt.status}
                                                    color={statusColor(
                                                        apt.status,
                                                    )}
                                                    className={
                                                        apt.status ===
                                                        'In Consultation'
                                                            ? 'animate-pulse'
                                                            : ''
                                                    }
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    {apt.status ===
                                                        'Scheduled' && (
                                                        <button
                                                            onClick={() =>
                                                                handleStatusUpdate(
                                                                    apt.id,
                                                                    'Waiting',
                                                                )
                                                            }
                                                            className="rounded-lg p-1.5 text-emerald-600 transition-all hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20"
                                                        >
                                                            <CheckIcon className="h-5 w-5" />
                                                        </button>
                                                    )}
                                                    {[
                                                        'Scheduled',
                                                        'Waiting',
                                                    ].includes(apt.status) && (
                                                        <button
                                                            onClick={() =>
                                                                handleStatusUpdate(
                                                                    apt.id,
                                                                    'Canceled',
                                                                )
                                                            }
                                                            className="rounded-lg p-1.5 text-red-500 transition-all hover:bg-red-50 dark:hover:bg-red-900/20"
                                                        >
                                                            <NoSymbolIcon className="h-5 w-5" />
                                                        </button>
                                                    )}
                                                    <Link
                                                        href={route(
                                                            'appointments.show',
                                                            apt.id,
                                                        )}
                                                        className="p-1.5 text-gray-400 transition-all hover:text-blue-600 dark:hover:text-blue-400"
                                                    >
                                                        <ArrowRightIcon className="h-5 w-5" />
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* Pagination */}
                <Pagination
                    links={appointments.links}
                    meta={{
                        from: appointments.from,
                        to: appointments.to,
                        total: appointments.total,
                    }}
                />
            </motion.div>
        </AppLayout>
    );
}
