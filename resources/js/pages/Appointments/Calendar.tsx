import {
    CalendarDaysIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    FunnelIcon,
    MagnifyingGlassIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import {
    addMonths,
    eachDayOfInterval,
    endOfMonth,
    endOfWeek,
    format,
    isSameDay,
    isSameMonth,
    isToday,
    parseISO,
    startOfMonth,
    startOfWeek,
    subMonths,
} from 'date-fns';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState, useMemo } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';

// ---------- Types ----------
interface Patient {
    id: number;
    first_name: string;
    last_name: string;
}

interface Doctor {
    id: number;
    name: string;
}

interface Appointment {
    id: number;
    patient: Patient;
    doctor: Doctor;
    appointment_date: string;
    appointment_time: string;
    status: 'scheduled' | 'confirmed' | 'completed' | 'cancelled' | 'no_show';
    queue_position: number;
    consultation_fee: number;
}

interface Props {
    appointments: Appointment[];
    stats: {
        today: number;
        upcoming: number;
        completed: number;
        cancelled: number;
    };
}

const statusColors: Record<string, 'blue' | 'emerald' | 'gray' | 'red' | 'amber'> = {
    scheduled: 'blue',
    confirmed: 'emerald',
    completed: 'gray',
    cancelled: 'red',
    no_show: 'amber',
};

// ---------- Component ----------
export default function AppointmentCalendar({ appointments, stats }: Props) {
    const { t } = useTranslation();

    const [currentMonth, setCurrentMonth] = useState(new Date());
    const [selectedDate, setSelectedDate] = useState<Date | null>(new Date());
    const [selectedAppointment, setSelectedAppointment] = useState<Appointment | null>(null);
    const [modalOpen, setModalOpen] = useState(false);

    // Navigation
    const prevMonth = () => setCurrentMonth(subMonths(currentMonth, 1));
    const nextMonth = () => setCurrentMonth(addMonths(currentMonth, 1));
    const goToToday = () => {
        setCurrentMonth(new Date());
        setSelectedDate(new Date());
    };

    // Calendar grid
    const calendarDays = useMemo(() => {
        const monthStart = startOfMonth(currentMonth);
        const monthEnd = endOfMonth(currentMonth);
        const calStart = startOfWeek(monthStart, { weekStartsOn: 1 });
        const calEnd = endOfWeek(monthEnd, { weekStartsOn: 1 });
        return eachDayOfInterval({ start: calStart, end: calEnd });
    }, [currentMonth]);

    // Group appointments by date
    const appointmentsByDate = useMemo(() => {
        const map: Record<string, Appointment[]> = {};
        appointments.forEach((apt) => {
            if (!map[apt.appointment_date]) map[apt.appointment_date] = [];
            map[apt.appointment_date].push(apt);
        });
        return map;
    }, [appointments]);

    const selectedAppointments = useMemo(() => {
        if (!selectedDate) return [];
        const dateStr = format(selectedDate, 'yyyy-MM-dd');
        return appointmentsByDate[dateStr] || [];
    }, [selectedDate, appointmentsByDate]);

    // Week day names from translation
    const weekDays = useMemo(
        () => [
            t('page.appointment_calendar.weekdays.mon'),
            t('page.appointment_calendar.weekdays.tue'),
            t('page.appointment_calendar.weekdays.wed'),
            t('page.appointment_calendar.weekdays.thu'),
            t('page.appointment_calendar.weekdays.fri'),
            t('page.appointment_calendar.weekdays.sat'),
            t('page.appointment_calendar.weekdays.sun'),
        ],
        [t],
    );

    return (
        <AppLayout
            headerTitle={t('page.appointment_calendar.title')}
            headerSubtitle={t('page.appointment_calendar.subtitle')}
        >
            <Head title={t('page.appointment_calendar.title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.appointment_calendar.title') },
                    ]}
                />

                {/* Stats */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('page.appointment_calendar.stats.today')}
                        value={stats.today}
                        icon={CalendarDaysIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('page.appointment_calendar.stats.upcoming')}
                        value={stats.upcoming}
                        icon={ChevronRightIcon}
                        color="emerald"
                    />
                    <StatCard
                        label={t('page.appointment_calendar.stats.completed')}
                        value={stats.completed}
                        icon={XMarkIcon}
                        color="purple"
                    />
                    <StatCard
                        label={t('page.appointment_calendar.stats.cancelled')}
                        value={stats.cancelled}
                        icon={XMarkIcon}
                        color="red"
                    />
                </div>

                {/* Header Controls */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex items-center gap-3">
                        <button
                            onClick={prevMonth}
                            className="rounded-xl border border-gray-200 bg-white p-2 text-gray-500 transition hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            <ChevronLeftIcon className="h-5 w-5" />
                        </button>
                        <h2 className="min-w-[200px] text-center text-lg font-bold text-gray-900 dark:text-neutral-100">
                            {format(currentMonth, 'MMMM yyyy')}
                        </h2>
                        <button
                            onClick={nextMonth}
                            className="rounded-xl border border-gray-200 bg-white p-2 text-gray-500 transition hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            <ChevronRightIcon className="h-5 w-5" />
                        </button>
                        {!isToday(currentMonth) && (
                            <button
                                onClick={goToToday}
                                className="ml-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-600 transition hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('page.appointment_calendar.common.today')}
                            </button>
                        )}
                    </div>

                    
                </div>

                {/* Calendar Grid */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="grid grid-cols-7 border-b border-gray-200 dark:border-neutral-700">
                        {weekDays.map((day) => (
                            <div
                                key={day}
                                className="px-2 py-3 text-center text-[10px] font-black tracking-widest text-gray-400 uppercase dark:text-neutral-400"
                            >
                                {day}
                            </div>
                        ))}
                    </div>

                    <div className="grid grid-cols-7">
                        {calendarDays.map((day, idx) => {
                            const dateStr = format(day, 'yyyy-MM-dd');
                            const dayAppts = appointmentsByDate[dateStr] || [];
                            const isSelected = selectedDate && isSameDay(day, selectedDate);
                            const isCurrentMonth = isSameMonth(day, currentMonth);
                            const today = isToday(day);

                            return (
                                <button
                                    key={idx}
                                    onClick={() => setSelectedDate(day)}
                                    className={`relative flex flex-col items-center border-b border-r border-gray-100 p-2 transition-colors hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-blue-900/20 ${
                                        !isCurrentMonth
                                            ? 'text-gray-300 dark:text-neutral-600'
                                            : ''
                                    } ${
                                        isSelected
                                            ? 'bg-blue-50 ring-2 ring-inset ring-blue-500 dark:bg-blue-900/20 dark:ring-blue-400'
                                            : ''
                                    }`}
                                >
                                    <span
                                        className={`flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold ${
                                            today
                                                ? 'bg-blue-600 text-white'
                                                : isSelected
                                                  ? 'text-blue-600 dark:text-blue-400'
                                                  : 'text-gray-900 dark:text-neutral-100'
                                        }`}
                                    >
                                        {format(day, 'd')}
                                    </span>

                                    <div className="mt-1 flex flex-wrap justify-center gap-1">
                                        {dayAppts.slice(0, 3).map((apt) => (
                                            <span
                                                key={apt.id}
                                                className={`h-1.5 w-1.5 rounded-full ${
                                                    statusColors[apt.status] === 'blue'
                                                        ? 'bg-blue-500'
                                                        : statusColors[apt.status] === 'emerald'
                                                          ? 'bg-emerald-500'
                                                          : statusColors[apt.status] === 'gray'
                                                            ? 'bg-gray-400'
                                                            : statusColors[apt.status] === 'red'
                                                              ? 'bg-red-500'
                                                              : 'bg-amber-500'
                                                }`}
                                                title={`${apt.patient.first_name} ${apt.patient.last_name}`}
                                            />
                                        ))}
                                        {dayAppts.length > 3 && (
                                            <span className="text-[8px] font-bold text-gray-400 dark:text-neutral-500">
                                                +{dayAppts.length - 3}
                                            </span>
                                        )}
                                    </div>
                                </button>
                            );
                        })}
                    </div>
                </div>

                {/* Selected Day Appointments */}
                <AnimatePresence mode="wait">
                    {selectedDate && (
                        <motion.div
                            key={selectedDate.toISOString()}
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -10 }}
                            className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="mb-4 flex items-center justify-between">
                                <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                    {format(selectedDate, 'EEEE, MMM dd')}
                                </h3>
                                <span className="text-xs font-bold text-gray-400 dark:text-neutral-400">
                                    {selectedAppointments.length}{' '}
                                    {t('page.appointment_calendar.appointments')}
                                </span>
                            </div>

                            {selectedAppointments.length === 0 ? (
                                <EmptyState
                                    icon={CalendarDaysIcon}
                                    title={t('page.appointment_calendar.no_appointments')}
                                    description={t('page.appointment_calendar.no_appointments_desc')}
                                />
                            ) : (
                                <div className="space-y-3">
                                    {selectedAppointments.map((apt) => (
                                        <button
                                            key={apt.id}
                                            onClick={() => {
                                                setSelectedAppointment(apt);
                                                setModalOpen(true);
                                            }}
                                            className="flex w-full items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-4 text-left transition hover:border-blue-300 hover:bg-white dark:border-neutral-700 dark:bg-neutral-900 dark:hover:border-blue-800 dark:hover:bg-neutral-800"
                                        >
                                            <div className="flex items-center gap-4">
                                                <StatusBadge
                                                    label={t(`page.appointment_calendar.status.${apt.status}`)}
                                                    color={statusColors[apt.status]}
                                                />
                                                <div>
                                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {apt.patient.first_name}{' '}
                                                        {apt.patient.last_name}
                                                    </p>
                                                    <p className="text-xs text-gray-500 dark:text-neutral-400">
                                                        {t('page.appointment_calendar.common.doctor_label', {
                                                            name: apt.doctor.name,
                                                        })}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm font-bold text-gray-700 dark:text-neutral-200">
                                                    {apt.appointment_time}
                                                </p>
                                                <p className="text-[10px] font-bold uppercase text-gray-400 dark:text-neutral-500">
                                                    {t('page.appointment_calendar.details.queue_position')}:{' '}
                                                    {apt.queue_position}
                                                </p>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* Detail Modal */}
                <Modal
                    isOpen={modalOpen}
                    onClose={() => {
                        setModalOpen(false);
                        setSelectedAppointment(null);
                    }}
                    title={t('page.appointment_calendar.details.title')}
                >
                    {selectedAppointment && (
                        <div className="space-y-6">
                            <div className="flex items-center gap-4">
                                <div className="h-12 w-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold dark:bg-blue-900/30 dark:text-blue-400">
                                    {selectedAppointment.patient.first_name[0]}
                                    {selectedAppointment.patient.last_name[0]}
                                </div>
                                <div>
                                    <p className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                        {selectedAppointment.patient.first_name}{' '}
                                        {selectedAppointment.patient.last_name}
                                    </p>
                                    <Link
                                        href={route('patients.show', selectedAppointment.patient.id)}
                                        className="text-xs font-bold text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        {t('page.appointment_calendar.common.view_profile')}
                                    </Link>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.appointment_calendar.details.doctor')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {selectedAppointment.doctor.name}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.appointment_calendar.details.date_time')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {format(parseISO(selectedAppointment.appointment_date), 'MMM dd, yyyy')}{' '}
                                        {selectedAppointment.appointment_time}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.appointment_calendar.details.status')}
                                    </p>
                                    <StatusBadge
                                        label={t(`page.appointment_calendar.status.${selectedAppointment.status}`)}
                                        color={statusColors[selectedAppointment.status]}
                                    />
                                </div>
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.appointment_calendar.details.queue_position')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {selectedAppointment.queue_position}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                        {t('page.appointment_calendar.details.consultation_fee')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        ${selectedAppointment.consultation_fee}
                                    </p>
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                                <button
                                    onClick={() => {
                                        setModalOpen(false);
                                        setSelectedAppointment(null);
                                    }}
                                    className="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                >
                                    {t('page.appointment_calendar.common.close')}
                                </button>
                                <Link
                                    href={route('appointments.edit', selectedAppointment.id)}
                                    className="flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                                >
                                    <PencilSquareIcon className="h-5 w-5" />
                                    {t('page.appointment_calendar.common.edit')}
                                </Link>
                            </div>
                        </div>
                    )}
                </Modal>
            </div>
        </AppLayout>
    );
}
