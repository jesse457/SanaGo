import {
    ArrowPathIcon,
    ArrowRightIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    CheckIcon,
    ClockIcon,
    ListBulletIcon,
    NoSymbolIcon,
    PlusIcon,
    Squares2X2Icon,
    UserGroupIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Button,
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
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    AppointmentIdentity,
    AppointmentStatusBadge,
} from '@/pages/Appointments/components/AppointmentIdentity';

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

export default function AppointmentIndex({
    appointments,
    doctors,
    filters,
}: Props) {
    const { t } = useTranslation();

    // Persist the selected view format (grid vs table) locally so it survives pagination reloads
    const [viewMode, setViewMode] = useState<'grid' | 'table'>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('appointments_view_mode');

            if (saved === 'grid' || saved === 'table') {
                return saved;
            }
        }

        return 'table';
    });

    const [params, setParams] = useState({
        search: filters.search || '',
        status: filters.status || '',
        doctor_id: filters.doctor_id || '',
        date: filters.date || '',
    });

    // Synchronize local params with server-side filters on pagination or navigation back/forward
    useEffect(() => {
        // Server-driven filters changed (pagination, back/forward): mirror them
        // into the local param state. External event, not derived render state.
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
        const newParams = {
            ...params,
            [key]: value,
            page: '1', // Explicitly force result set to start on page 1 when filters are changed
        };
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

    return (
        <AppLayout
            headerTitle={t('manageAppointments')}
            headerSubtitle={t('monitorPatientQueues')}
        >
            <Head title={t('appointments')} />

            <div className="space-y-5">
                <PageHeader
                    eyebrow={t('queueManagement')}
                    title={t('manageAppointments')}
                    description={t('monitorPatientQueues')}
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
                        label={t('waitingInHall')}
                        value={stats.waiting}
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
                            handleFilterChange('status', event.target.value)
                        }
                        className="sm:w-44"
                    >
                        <option value="">{t('allStatuses')}</option>
                        <option value="Scheduled">{t('scheduled')}</option>
                        <option value="Waiting">{t('waiting')}</option>
                        <option value="In Consultation">
                            {t('inConsultation')}
                        </option>
                        <option value="Completed">{t('completed')}</option>
                        <option value="Canceled">{t('canceled')}</option>
                    </Select>
                    <Select
                        name="doctor_id"
                        aria-label={t('doctor')}
                        value={params.doctor_id}
                        onChange={(event) =>
                            handleFilterChange('doctor_id', event.target.value)
                        }
                        className="sm:w-48"
                    >
                        <option value="">{t('allDoctors')}</option>
                        {doctors.map((doctor) => (
                            <option key={doctor.id} value={doctor.id}>
                                {doctor.name}
                            </option>
                        ))}
                    </Select>
                    <Input
                        name="date"
                        type="date"
                        aria-label={t('date')}
                        value={params.date}
                        onChange={(event) =>
                            handleFilterChange('date', event.target.value)
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
                                            firstName={apt.patient.first_name}
                                            lastName={apt.patient.last_name}
                                            patientUid={apt.patient.patient_uid}
                                        />
                                        <AppointmentStatusBadge
                                            status={apt.status}
                                            pulse={
                                                apt.status === 'In Consultation'
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
                                                {apt.doctor?.name ?? '—'}
                                            </dd>
                                        </div>
                                        <div className="flex items-center gap-2 text-[12px] text-ink-muted">
                                            <CalendarDaysIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                            <dt className="sr-only">
                                                {t('schedule')}
                                            </dt>
                                            <dd className="truncate tabular-nums">
                                                {apt.appointment_date} ·{' '}
                                                {apt.appointment_time}
                                            </dd>
                                        </div>
                                    </dl>

                                    <div className="flex items-center justify-between border-t border-line pt-3">
                                        <div className="flex gap-1.5">
                                            {apt.status === 'Scheduled' && (
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                    icon={CheckIcon}
                                                    onClick={() =>
                                                        handleStatusUpdate(
                                                            apt.id,
                                                            'Waiting',
                                                        )
                                                    }
                                                    title={t('checkIn')}
                                                >
                                                    {t('checkIn')}
                                                </Button>
                                            )}
                                        </div>
                                        <Button
                                            size="sm"
                                            variant="secondary"
                                            trailingIcon={ArrowRightIcon}
                                            href={route(
                                                'appointments.show',
                                                apt.id,
                                            )}
                                        >
                                            {t('manage')}
                                        </Button>
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
                                                        apt.patient.first_name
                                                    }
                                                    lastName={
                                                        apt.patient.last_name
                                                    }
                                                    patientUid={
                                                        apt.patient.patient_uid
                                                    }
                                                    size="sm"
                                                />
                                            </TableCell>
                                            <TableCell muted>
                                                <p className="text-[13px] font-medium text-ink">
                                                    {apt.doctor?.name ?? '—'}
                                                </p>
                                                <p className="text-[11px] text-ink-subtle">
                                                    {apt.doctor?.department
                                                        ?.name ?? ''}
                                                </p>
                                            </TableCell>
                                            <TableCell muted>
                                                <p className="text-[13px] font-medium text-ink tabular-nums">
                                                    {apt.appointment_date}
                                                </p>
                                                <p className="text-[11px] text-ink-subtle tabular-nums">
                                                    {apt.appointment_time}
                                                </p>
                                            </TableCell>
                                            <TableCell>
                                                <AppointmentStatusBadge
                                                    status={apt.status}
                                                    pulse={
                                                        apt.status ===
                                                        'In Consultation'
                                                    }
                                                />
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center justify-end gap-1">
                                                    {apt.status ===
                                                        'Scheduled' && (
                                                        <Button
                                                            size="icon"
                                                            variant="ghost"
                                                            icon={CheckIcon}
                                                            onClick={() =>
                                                                handleStatusUpdate(
                                                                    apt.id,
                                                                    'Waiting',
                                                                )
                                                            }
                                                            aria-label={t(
                                                                'checkIn',
                                                            )}
                                                            title={t('checkIn')}
                                                        />
                                                    )}
                                                    {[
                                                        'Scheduled',
                                                        'Waiting',
                                                    ].includes(apt.status) && (
                                                        <Button
                                                            size="icon"
                                                            variant="ghost"
                                                            icon={NoSymbolIcon}
                                                            onClick={() =>
                                                                handleStatusUpdate(
                                                                    apt.id,
                                                                    'Canceled',
                                                                )
                                                            }
                                                            aria-label={t(
                                                                'canceled',
                                                            )}
                                                            title={t(
                                                                'canceled',
                                                            )}
                                                            className="text-critical-ink hover:bg-critical-soft"
                                                        />
                                                    )}
                                                    <Link
                                                        href={route(
                                                            'appointments.show',
                                                            apt.id,
                                                        )}
                                                        aria-label={t('manage')}
                                                        title={t('manage')}
                                                        className="inline-flex h-9 w-9 items-center justify-center rounded-md text-ink-subtle transition-colors duration-200 ease-premium hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                                    >
                                                        <ArrowRightIcon className="h-4 w-4" />
                                                    </Link>
                                                </div>
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
            </div>
        </AppLayout>
    );
}
