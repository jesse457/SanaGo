import {
    ArrowPathIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    ClockIcon,
    EyeIcon,
    ListBulletIcon,
    NoSymbolIcon,
    PlusIcon,
    Squares2X2Icon,
    UserGroupIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Button,
    Dialog,
    EmptyState,
    Input,
    PageHeader,
    SegmentedControl,
    Select,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Tabs,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
    swal,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { AppointmentCalendar } from '@/pages/Appointments/components/AppointmentCalendar';
import type { CalendarAppointment } from '@/pages/Appointments/components/AppointmentCalendar';
import {
    AppointmentIdentity,
    AppointmentStatusBadge,
} from '@/pages/Appointments/components/AppointmentIdentity';
import type { AppointmentStatus } from '@/pages/Appointments/components/status';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
}

interface Doctor {
    id: number;
    name: string;
    department?: { name: string } | null;
}

interface Appointment {
    id: number;
    appointment_date: string;
    appointment_time: string;
    status: AppointmentStatus;
    consultation_fee?: number | string | null;
    reason_for_visit?: string | null;
    patient: Patient;
    doctor?: Doctor | null;
}

interface Stats {
    total: number;
    scheduled: number;
    completed: number;
    today: number;
    upcoming: number;
    cancelled: number;
}

interface Props {
    appointments: {
        data: Appointment[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    calendarAppointments: CalendarAppointment[];
    stats: Stats;
    doctors: Doctor[];
    filters: {
        search?: string;
        status?: string;
        doctor_id?: string;
        date?: string;
        view?: 'table' | 'calendar';
    };
}

const viewOptions = [
    {
        value: 'table' as const,
        icon: ListBulletIcon,
        ariaLabel: 'Table view',
    },
    {
        value: 'grid' as const,
        icon: Squares2X2Icon,
        ariaLabel: 'Grid view',
    },
];

const statusOptions: AppointmentStatus[] = [
    'scheduled',
    'in_consultation',
    'pending',
    'completed',
    'cancelled',
];

const cancellableStatuses: AppointmentStatus[] = ['scheduled', 'pending'];

export default function AppointmentIndex({
    appointments,
    calendarAppointments,
    stats,
    doctors,
    filters,
}: Props) {
    const { t } = useTranslation();

    const activeTab = filters.view === 'calendar' ? 'calendar' : 'table';

    const statusLabel = (status: string) =>
        t(`page.appointment_calendar.status.${status}`, status);

    // Persist the selected view format (grid vs table) locally so it survives
    // pagination reloads.
    const [viewMode, setViewMode] = useState<'grid' | 'table'>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('appointments_view_mode');

            if (saved === 'grid' || saved === 'table') {
                return saved;
            }
        }

        return 'table';
    });

    const [detail, setDetail] = useState<Appointment | null>(null);

    const [params, setParams] = useState({
        search: filters.search || '',
        status: filters.status || '',
        doctor_id: filters.doctor_id || '',
        date: filters.date || '',
    });

    // Synchronize local params with server-side filters on pagination or
    // navigation back/forward.
    useEffect(() => {
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setParams({
            search: filters.search || '',
            status: filters.status || '',
            doctor_id: filters.doctor_id || '',
            date: filters.date || '',
        });
    }, [filters]);

    const handleViewModeChange = (mode: 'grid' | 'table') => {
        setViewMode(mode);

        if (typeof window !== 'undefined') {
            localStorage.setItem('appointments_view_mode', mode);
        }
    };

    const handleTabChange = (value: string) => {
        const view = value === 'calendar' ? 'calendar' : 'table';

        if (view === activeTab) {
            return;
        }

        router.get(
            route('appointments.index'),
            { ...params, view },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['filters'],
            },
        );
    };

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
        const newParams = {
            ...params,
            [key]: value,
            page: '1',
        };
        setParams(newParams);
        requestUpdate(newParams);
    };

    const handleStatusUpdate = async (
        id: number,
        newStatus: AppointmentStatus,
        closeDetail = false,
    ) => {
        /* Cancelling is destructive and was the only such action in the app
           that fired without asking. */
        if (newStatus === 'cancelled') {
            const confirmed = await swal.confirm({
                tone: 'warning',
                danger: true,
                title: t(
                    'page.appointment_calendar.cancel_confirm',
                    'Cancel this appointment?',
                ),
                message: t(
                    'page.appointment_calendar.cancel_message',
                    'The slot is released and the patient is notified.',
                ),
                confirmLabel: t(
                    'page.appointment_calendar.cancel_action',
                    'Cancel appointment',
                ),
                cancelLabel: t(
                    'page.appointment_calendar.keep_action',
                    'Keep appointment',
                ),
            });

            if (!confirmed) {
                return;
            }
        }

        router.patch(
            route('appointments.update', id),
            { status: newStatus },
            {
                preserveScroll: true,
                onSuccess: () => {
                    if (closeDetail) {
                        setDetail(null);
                    }
                },
            },
        );
    };

    const canCancel = (status: string) =>
        cancellableStatuses.includes(status as AppointmentStatus);

    const renderAppointmentActions = (
        appointment: Appointment,
        mode: 'table' | 'grid',
    ) => {
        if (mode === 'grid') {
            return (
                <div className="flex gap-1.5">
                    <Button
                        size="sm"
                        variant="secondary"
                        icon={EyeIcon}
                        onClick={() => setDetail(appointment)}
                    >
                        {t('page.appointment_calendar.common.details')}
                    </Button>
                </div>
            );
        }

        return (
            <div className="flex items-center justify-end gap-1">
                {canCancel(appointment.status) && (
                    <Button
                        size="icon"
                        variant="ghost"
                        icon={NoSymbolIcon}
                        onClick={() =>
                            handleStatusUpdate(appointment.id, 'cancelled')
                        }
                        aria-label={t('cancel')}
                        title={t('cancel')}
                        className="text-critical-ink hover:bg-critical-soft"
                    />
                )}
                <Button
                    size="icon"
                    variant="ghost"
                    icon={EyeIcon}
                    onClick={() => setDetail(appointment)}
                    aria-label={t('page.appointment_calendar.common.details')}
                    title={t('page.appointment_calendar.common.details')}
                />
            </div>
        );
    };

    return (
        <AppLayout
            headerTitle={t('manageAppointments')}
            headerSubtitle={t('schedulePatientVisits')}
        >
            <Head title={t('appointments')} />

            <div className="space-y-5">
                <PageHeader
                    eyebrow={t('appointments')}
                    title={t('manageAppointments')}
                    description={t('schedulePatientVisits')}
                    actions={
                        <Button
                            href={route('appointments.create')}
                            variant="accent"
                            icon={PlusIcon}
                        >
                            {t('bookNew')}
                        </Button>
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        icon={UserGroupIcon}
                        label={t('totalAppointments')}
                        value={stats.total}
                        tone="accent"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label={t('upcomingAppointments')}
                        value={stats.upcoming}
                        tone="warning"
                    />
                    <StatCard
                        icon={CalendarDaysIcon}
                        label={t('todaysScheduled')}
                        value={stats.scheduled}
                        tone="neutral"
                    />
                    <StatCard
                        icon={CheckBadgeIcon}
                        label={t('totalCompleted')}
                        value={stats.completed}
                        tone="positive"
                    />
                </StatGrid>

                <Tabs
                    value={activeTab}
                    onChange={handleTabChange}
                    items={[
                        {
                            value: 'table',
                            label: t('page.appointment_calendar.tabs.table'),
                            icon: ListBulletIcon,
                            count: stats.total,
                        },
                        {
                            value: 'calendar',
                            label: t('page.appointment_calendar.tabs.calendar'),
                            icon: CalendarDaysIcon,
                        },
                    ]}
                />

                {activeTab === 'calendar' ? (
                    <AppointmentCalendar appointments={calendarAppointments} />
                ) : (
                    <>
                        <Toolbar>
                            <ToolbarSearch
                                value={params.search}
                                onChange={(value) =>
                                    handleFilterChange('search', value)
                                }
                                placeholder={t('searchPatient')}
                            />
                            <Select
                                name="status"
                                aria-label={t('status')}
                                value={params.status}
                                onChange={(event) =>
                                    handleFilterChange(
                                        'status',
                                        event.target.value,
                                    )
                                }
                                className="sm:w-44"
                            >
                                <option value="">{t('allStatuses')}</option>
                                {statusOptions.map((status) => (
                                    <option key={status} value={status}>
                                        {statusLabel(status)}
                                    </option>
                                ))}
                            </Select>
                            {doctors.length > 0 && (
                                <Select
                                    name="doctor_id"
                                    aria-label={t('doctor')}
                                    value={params.doctor_id}
                                    onChange={(event) =>
                                        handleFilterChange(
                                            'doctor_id',
                                            event.target.value,
                                        )
                                    }
                                    className="sm:w-48"
                                >
                                    <option value="">{t('allDoctors')}</option>
                                    {doctors.map((doctor) => (
                                        <option
                                            key={doctor.id}
                                            value={doctor.id}
                                        >
                                            {doctor.name}
                                        </option>
                                    ))}
                                </Select>
                            )}
                            <Input
                                name="date"
                                type="date"
                                aria-label={t('date')}
                                value={params.date}
                                onChange={(event) =>
                                    handleFilterChange(
                                        'date',
                                        event.target.value,
                                    )
                                }
                                className="sm:w-40"
                            />
                            <ToolbarSpacer />
                            <SegmentedControl
                                options={viewOptions}
                                value={viewMode}
                                onChange={handleViewModeChange}
                            />
                        </Toolbar>

                        <AnimatePresence mode="wait">
                            {appointments.data.length === 0 ? (
                                <motion.div
                                    key="empty"
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    exit={{ opacity: 0 }}
                                    transition={{ duration: 0.2 }}
                                >
                                    <EmptyState
                                        icon={ArrowPathIcon}
                                        title={t('noAppointmentsFound')}
                                        description={t('adjustFiltersOrBook')}
                                        action={{
                                            label: t('bookNew'),
                                            href: route('appointments.create'),
                                        }}
                                    />
                                </motion.div>
                            ) : viewMode === 'grid' ? (
                                <motion.div
                                    key="grid"
                                    initial={{ opacity: 0, y: 8 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -8 }}
                                    transition={{ duration: 0.22 }}
                                    className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                                >
                                    {appointments.data.map((apt) => (
                                        <div
                                            key={apt.id}
                                            className="flex h-full flex-col rounded-xl border border-line bg-surface p-4 transition-[border-color,box-shadow] duration-300 ease-premium hover:border-line-strong hover:shadow-surface"
                                        >
                                            <div className="mb-3 flex items-start justify-between gap-3">
                                                <AppointmentIdentity
                                                    firstName={
                                                        apt.patient.first_name
                                                    }
                                                    lastName={
                                                        apt.patient.last_name
                                                    }
                                                    patientUid={
                                                        apt.patient.patient_uid
                                                    }
                                                />
                                                <AppointmentStatusBadge
                                                    status={apt.status}
                                                    label={statusLabel(
                                                        apt.status,
                                                    )}
                                                    pulse={
                                                        apt.status ===
                                                        'in_consultation'
                                                    }
                                                />
                                            </div>

                                            <dl className="mb-3 flex-1 space-y-2 rounded-lg border border-line bg-surface-muted px-3 py-2.5">
                                                <div className="flex items-center gap-2 text-[12px]">
                                                    <UserIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                                    <dt className="sr-only">
                                                        {t('doctor')}
                                                    </dt>
                                                    <dd className="truncate font-medium text-ink">
                                                        {apt.doctor?.name ??
                                                            '—'}
                                                    </dd>
                                                </div>
                                                <div className="flex items-center gap-2 text-[12px] text-ink-muted">
                                                    <CalendarDaysIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                                    <dt className="sr-only">
                                                        {t('schedule')}
                                                    </dt>
                                                    <dd className="truncate tabular-nums">
                                                        {apt.appointment_date} ·{' '}
                                                        {apt.appointment_time.slice(
                                                            0,
                                                            5,
                                                        )}
                                                    </dd>
                                                </div>
                                            </dl>

                                            <div className="flex items-center justify-between border-t border-line pt-3">
                                                {renderAppointmentActions(
                                                    apt,
                                                    'grid',
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </motion.div>
                            ) : (
                                <motion.div
                                    key="table"
                                    initial={{ opacity: 0, y: 8 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -8 }}
                                    transition={{ duration: 0.22 }}
                                >
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHeaderCell>
                                                    {t('patient')}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t('doctor')}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t('schedule')}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t('status')}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    <span className="sr-only">
                                                        {t('actions')}
                                                    </span>
                                                </TableHeaderCell>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {appointments.data.map((apt) => (
                                                <TableRow key={apt.id}>
                                                    <TableCell>
                                                        <AppointmentIdentity
                                                            firstName={
                                                                apt.patient
                                                                    .first_name
                                                            }
                                                            lastName={
                                                                apt.patient
                                                                    .last_name
                                                            }
                                                            patientUid={
                                                                apt.patient
                                                                    .patient_uid
                                                            }
                                                            size="sm"
                                                        />
                                                    </TableCell>
                                                    <TableCell muted>
                                                        <p className="text-[13px] font-medium text-ink">
                                                            {apt.doctor?.name ??
                                                                '—'}
                                                        </p>
                                                        <p className="text-[11px] text-ink-subtle">
                                                            {apt.doctor
                                                                ?.department
                                                                ?.name ?? ''}
                                                        </p>
                                                    </TableCell>
                                                    <TableCell muted>
                                                        <p className="text-[13px] font-medium text-ink tabular-nums">
                                                            {
                                                                apt.appointment_date
                                                            }
                                                        </p>
                                                        <p className="text-[11px] text-ink-subtle tabular-nums">
                                                            {apt.appointment_time.slice(
                                                                0,
                                                                5,
                                                            )}
                                                        </p>
                                                    </TableCell>
                                                    <TableCell>
                                                        <AppointmentStatusBadge
                                                            status={apt.status}
                                                            label={statusLabel(
                                                                apt.status,
                                                            )}
                                                            pulse={
                                                                apt.status ===
                                                                'in_consultation'
                                                            }
                                                        />
                                                    </TableCell>
                                                    <TableCell>
                                                        {renderAppointmentActions(
                                                            apt,
                                                            'table',
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </motion.div>
                            )}
                        </AnimatePresence>

                        <Pagination
                            links={appointments.links}
                            meta={{
                                from: appointments.from,
                                to: appointments.to,
                                total: appointments.total,
                            }}
                        />
                    </>
                )}
            </div>

            <Dialog
                open={detail !== null}
                onClose={() => setDetail(null)}
                title={t('page.appointment_calendar.details.title')}
                size="md"
                footer={
                    detail ? (
                        <>
                            <Button
                                variant="ghost"
                                onClick={() => setDetail(null)}
                            >
                                {t('page.appointment_calendar.common.close')}
                            </Button>
                            {canCancel(detail.status) && (
                                <Button
                                    variant="danger"
                                    icon={NoSymbolIcon}
                                    onClick={() =>
                                        handleStatusUpdate(
                                            detail.id,
                                            'cancelled',
                                            true,
                                        )
                                    }
                                >
                                    {t('cancel')}
                                </Button>
                            )}
                        </>
                    ) : null
                }
            >
                {detail && (
                    <div className="space-y-5">
                        <div className="flex items-start justify-between gap-3">
                            <AppointmentIdentity
                                firstName={detail.patient.first_name}
                                lastName={detail.patient.last_name}
                                patientUid={detail.patient.patient_uid}
                            />
                            <AppointmentStatusBadge
                                status={detail.status}
                                label={statusLabel(detail.status)}
                            />
                        </div>

                        <dl className="grid grid-cols-2 gap-x-4 gap-y-3 rounded-lg border border-line bg-surface-muted px-4 py-3">
                            <div>
                                <dt className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.doctor',
                                    )}
                                </dt>
                                <dd className="mt-0.5 text-[13px] font-medium text-ink">
                                    {detail.doctor?.name ?? '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.date_time',
                                    )}
                                </dt>
                                <dd className="mt-0.5 text-[13px] font-medium text-ink tabular-nums">
                                    {detail.appointment_date} ·{' '}
                                    {detail.appointment_time.slice(0, 5)}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.consultation_fee',
                                    )}
                                </dt>
                                <dd className="mt-0.5 text-[13px] font-medium text-ink tabular-nums">
                                    {detail.consultation_fee ?? '—'}
                                </dd>
                            </div>
                        </dl>

                        {detail.reason_for_visit && (
                            <div>
                                <p className="text-[10px] font-semibold tracking-[0.12em] text-ink-subtle uppercase">
                                    {t(
                                        'page.appointment_calendar.details.reason',
                                        'Reason for visit',
                                    )}
                                </p>
                                <p className="mt-1 text-[13px] leading-relaxed text-ink-muted">
                                    {detail.reason_for_visit}
                                </p>
                            </div>
                        )}

                        <Button
                            href={route('patients.show', detail.patient.id)}
                            variant="secondary"
                            size="sm"
                        >
                            {t('page.appointment_calendar.common.view_profile')}
                        </Button>
                    </div>
                )}
            </Dialog>
        </AppLayout>
    );
}
