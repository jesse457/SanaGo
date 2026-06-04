import React, { useState, useMemo, useCallback, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import debounce from 'lodash/debounce';
import {
    HomeIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    CalendarIcon,
    PlusIcon,
    CheckIcon,
    ArrowPathIcon,
    ClockIcon,
    CheckBadgeIcon,
    UserGroupIcon,
    Squares2X2Icon,
    ListBulletIcon,
    UserIcon,
    ArrowRightIcon,
    NoSymbolIcon
} from '@heroicons/react/24/outline';

import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
    status: 'Scheduled' | 'Waiting' | 'In Consultation' | 'Completed' | 'Canceled';
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
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } }
};

const itemVariants = {
    hidden: { y: 20, opacity: 0 },
    visible: { y: 0, opacity: 1, transition: { type: 'spring', stiffness: 300, damping: 24 } }
};

export default function AppointmentIndex({ appointments, doctors, filters }: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    const [params, setParams] = useState({
        search: filters.search || '',
        status: filters.status || '',
        doctor_id: filters.doctor_id || '',
        date: filters.date || '',
    });

    const stats = useMemo(() => ({
        total: appointments.total,
        waiting: appointments.data.filter(a => a.status === 'Waiting').length,
        scheduled: appointments.data.filter(a => a.status === 'Scheduled').length,
        completed: appointments.data.filter(a => a.status === 'Completed').length,
    }), [appointments]);

    const requestUpdate = useCallback(
        debounce((query) => {
            router.get(route('appointments.index'), query, {
                preserveState: true,
                replace: true,
                only: ['appointments'],
            });
        }, 300),
        []
    );

    const handleFilterChange = (key: string, value: string) => {
        const newParams = { ...params, [key]: value };
        setParams(newParams);
        requestUpdate(newParams);
    };

    const handleStatusUpdate = (id: number, newStatus: string) => {
        router.patch(route('appointments.update', id), {
            status: newStatus
        }, {
            preserveScroll: true,
        });
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
                className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12"
            >
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="hover:text-blue-600 cursor-pointer transition-colors flex items-center">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('home')}
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-medium">{t('appointments')}</li>
                    </ol>
                </nav>

                {/* Stats Overview */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label={t('totalAppointments')} value={stats.total} icon={<UserGroupIcon />} color="blue" delay={0.1} />
                    <StatCard label={t('waitingInHall')} value={stats.waiting} icon={<ClockIcon />} color="amber" delay={0.2} />
                    <StatCard label={t('todaysScheduled')} value={stats.scheduled} icon={<CalendarIcon />} color="purple" delay={0.3} />
                    <StatCard label={t('totalCompleted')} value={stats.completed} icon={<CheckBadgeIcon />} color="emerald" delay={0.4} />
                </div>

                {/* Header Controls */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:flex items-center gap-3 w-full lg:w-auto">
                        <div className="relative flex-grow sm:min-w-[240px]">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t('searchPatient')}
                                value={params.search}
                                onChange={(e) => handleFilterChange('search', e.target.value)}
                                className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:text-neutral-100"
                            />
                        </div>

                        <select
                            value={params.status}
                            onChange={(e) => handleFilterChange('status', e.target.value)}
                            className="pl-3 pr-10 py-2 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none dark:text-neutral-100"
                        >
                            <option value="">{t('allStatuses')}</option>
                            <option value="Scheduled">{t('scheduled')}</option>
                            <option value="Waiting">{t('waiting')}</option>
                            <option value="In Consultation">{t('inConsultation')}</option>
                            <option value="Completed">{t('completed')}</option>
                            <option value="Canceled">{t('canceled')}</option>
                        </select>

                        <select
                            value={params.doctor_id}
                            onChange={(e) => handleFilterChange('doctor_id', e.target.value)}
                            className="pl-3 pr-10 py-2 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none dark:text-neutral-100"
                        >
                            <option value="">{t('allDoctors')}</option>
                            {doctors.map(d => <option key={d.id} value={d.id.toString()}>{d.name}</option>)}
                        </select>

                        <input
                            type="date"
                            value={params.date}
                            onChange={(e) => handleFilterChange('date', e.target.value)}
                            className="pl-3 pr-3 py-2 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none dark:text-neutral-100"
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href={route('appointments.create')}
                            className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition-all"
                        >
                            <PlusIcon className="w-4 h-4" /> {t('bookNew')}
                        </Link>
                        <div className="flex bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-1 shadow-sm">
                            <button onClick={() => setViewMode('grid')} className={`p-1.5 rounded-md transition-all ${viewMode === 'grid' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 shadow-sm' : 'text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-neutral-300'}`}>
                                <Squares2X2Icon className="w-5 h-5" />
                            </button>
                            <button onClick={() => setViewMode('table')} className={`p-1.5 rounded-md transition-all ${viewMode === 'table' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 shadow-sm' : 'text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-neutral-300'}`}>
                                <ListBulletIcon className="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                {/* Main Content */}
                <AnimatePresence mode="wait">
                    {appointments.data.length === 0 ? (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0 }} animate={{ opacity: 1 }}
                            className="text-center py-20 bg-white dark:bg-neutral-800 rounded-xl border border-dashed border-gray-300 dark:border-neutral-600"
                        >
                            <ArrowPathIcon className="w-12 h-12 text-gray-300 dark:text-neutral-600 mx-auto mb-4" />
                            <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100">{t('noAppointmentsFound')}</h3>
                            <p className="text-xs text-gray-500 dark:text-neutral-400 mt-1">{t('adjustFiltersOrBook')}</p>
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0, x: -20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: 20 }}
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            {appointments.data.map((apt) => (
                                <motion.div
                                    key={apt.id}
                                    variants={itemVariants}
                                    whileHover={{ y: -4 }}
                                    className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all flex flex-col h-full"
                                >
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="h-12 w-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg shadow-sm">
                                            {apt.patient.first_name[0]}{apt.patient.last_name[0]}
                                        </div>
                                        <StatusBadge status={apt.status} />
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 leading-tight mb-1">
                                            {apt.patient.first_name} {apt.patient.last_name}
                                        </h3>
                                        <p className="text-xs font-mono text-gray-400 dark:text-neutral-500 mb-4">{apt.patient.patient_uid}</p>
                                        <div className="bg-gray-50 dark:bg-neutral-700/50 rounded-lg p-3 border border-gray-100 dark:border-neutral-700 space-y-2">
                                            <div className="flex items-center gap-2">
                                                <UserIcon className="w-4 h-4 text-blue-500" />
                                                <span className="text-xs font-bold text-gray-700 dark:text-neutral-300">{apt.doctor?.name}</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <CalendarIcon className="w-4 h-4 text-gray-400" />
                                                <span className="text-xs text-gray-600 dark:text-neutral-400 font-medium">{apt.appointment_date} @ {apt.appointment_time}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="pt-4 border-t border-gray-100 dark:border-neutral-700 flex items-center justify-between">
                                        <div className="flex gap-2">
                                            {apt.status === 'Scheduled' && (
                                                <button
                                                    onClick={() => handleStatusUpdate(apt.id, 'Waiting')}
                                                    className="p-1.5 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-md hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors"
                                                    title={t('checkIn')}
                                                >
                                                    <CheckIcon className="w-4 h-4" />
                                                </button>
                                            )}
                                        </div>
                                        <Link
                                            href={route('appointments.show', apt.id)}
                                            className="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg font-bold text-xs"
                                        >
                                            {t('manage')} <ArrowRightIcon className="w-4 h-4" />
                                        </Link>
                                    </div>
                                </motion.div>
                            ))}
                        </motion.div>
                    ) : (
                        <motion.div
                            key="table"
                            initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }}
                            className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden"
                        >
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-800">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('patient')}</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('doctor')}</th>
                                        <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('schedule')}</th>
                                        <th className="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wide">{t('status')}</th>
                                        <th className="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {appointments.data.map((apt) => (
                                        <tr key={apt.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">
                                                        {apt.patient.first_name[0]}{apt.patient.last_name[0]}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="font-bold text-sm text-gray-900 dark:text-neutral-100">{apt.patient.first_name} {apt.patient.last_name}</div>
                                                        <div className="text-[10px] font-mono text-gray-400 dark:text-neutral-500">{apt.patient.patient_uid}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-sm font-medium text-gray-700 dark:text-neutral-300">{apt.doctor?.name}</div>
                                                <div className="text-[10px] text-gray-400 dark:text-neutral-500 uppercase">{apt.doctor?.department?.name}</div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-sm font-bold text-gray-800 dark:text-neutral-100">{apt.appointment_date}</div>
                                                <div className="text-[10px] text-gray-500 dark:text-neutral-400">{apt.appointment_time}</div>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge status={apt.status} />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                     {apt.status === 'Scheduled' && (
                                                        <button
                                                            onClick={() => handleStatusUpdate(apt.id, 'Waiting')}
                                                            className="p-1.5 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-all"
                                                        >
                                                            <CheckIcon className="w-5 h-5" />
                                                        </button>
                                                     )}
                                                     {['Scheduled', 'Waiting'].includes(apt.status) && (
                                                         <button
                                                            onClick={() => handleStatusUpdate(apt.id, 'Canceled')}
                                                            className="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                                         >
                                                            <NoSymbolIcon className="w-5 h-5" />
                                                         </button>
                                                     )}
                                                     <Link
                                                        href={route('appointments.show', apt.id)}
                                                        className="p-1.5 text-gray-400 hover:text-blue-600 transition-all"
                                                     >
                                                        <ArrowRightIcon className="w-5 h-5" />
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
                <div className="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-400">
                    <p>{t('showing')} <b className="dark:text-neutral-100">{appointments.from}</b> {t('to')} <b className="dark:text-neutral-100">{appointments.to}</b> {t('of')} <b className="dark:text-neutral-100">{appointments.total}</b> {t('entries')}</p>
                    <div className="flex gap-1">
                        {appointments.links.map((link, i) => (
                            <button
                                key={i}
                                disabled={!link.url}
                                onClick={() => router.get(link.url!, params, { preserveState: true })}
                                className={`px-3 py-1.5 border rounded-lg transition-all ${
                                    link.active
                                    ? 'bg-blue-600 text-white border-blue-600 font-bold'
                                    : 'bg-white dark:bg-neutral-800 text-gray-600 dark:text-neutral-400 hover:border-blue-300 dark:border-neutral-600'
                                } ${!link.url ? 'opacity-30 cursor-not-allowed' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            </motion.div>
        </AppLayout>
    );
}

function StatCard({ label, value, icon, color, delay }: any) {
    const colorClasses: any = {
        blue: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
        emerald: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400',
        purple: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
        amber: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400'
    };

    return (
        <motion.div
            initial={{ scale: 0.95, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            transition={{ delay, type: 'spring', stiffness: 200 }}
            className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4"
        >
            <div className={`p-3 rounded-lg ${colorClasses[color]}`}>
                {React.isValidElement(icon) && React.cloneElement(icon as React.ReactElement, { className: "w-5 h-5" } as any)}
            </div>
            <div>
                <p className="text-xl font-bold text-gray-900 dark:text-neutral-100 leading-none">{value}</p>
                <p className="text-xs font-semibold text-gray-500 dark:text-neutral-400 mt-1">{label}</p>
            </div>
        </motion.div>
    );
}

function StatusBadge({ status }: { status: string }) {
    const styles: any = {
        'Waiting': 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
        'In Consultation': 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800 animate-pulse',
        'Canceled': 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-800',
        'Completed': 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
        'Scheduled': 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    };

    return (
        <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border ${styles[status] || 'bg-gray-50 dark:bg-neutral-700 text-gray-600 dark:text-neutral-400 border-gray-100 dark:border-neutral-600'}`}>
            {status}
        </span>
    );
}
