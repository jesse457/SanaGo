import {
    ArchiveBoxIcon,
    ArrowRightIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    ClockIcon,
    EyeIcon,
    InboxIcon,
    ListBulletIcon,
    Squares2X2Icon,
    UsersIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import React, { useEffect, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Button,
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    PageHeader,
    SegmentedControl,
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
import { AdmissionLocation } from '@/pages/Patients/components/AdmissionMeta';
import { AdmissionStatusBadge } from '@/pages/Patients/components/AdmissionStatusBadge';
import { PatientEmptyState } from '@/pages/Patients/components/PatientEmptyState';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';
import { PatientStatGrid } from '@/pages/Patients/components/PatientStatGrid';
import type { PatientStat } from '@/pages/Patients/components/PatientStatGrid';

interface Admission {
    id: number;
    patient: { first_name: string; last_name: string; patient_uid: string };
    bed: { code: string; ward: { name: string } };
    attending_doctor: { name: string };
    status: 'pending' | 'admitted' | 'discharged';
    admission_date: string;
    reason_for_admission: string;
}

interface Props {
    admissions: {
        data: Admission[];
        links: { url: string | null; label: string; active: boolean }[];
        total?: number;
        from?: number;
        to?: number;
    };
    stats: {
        total: number;
        active: number;
        pending: number;
        discharged: number;
    };
    filters: { search?: string; status?: string };
}

const statusFilters = ['admitted', 'pending', 'discharged'] as const;

export default function AdmissionsIndex({ admissions, stats, filters }: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');
    const [searchQuery, setSearchQuery] = useState(filters.search || '');

    // Server-side search logic with 300ms debounce
    useEffect(() => {
        const currentSearch = filters.search || '';

        if (searchQuery !== currentSearch) {
            const timeout = setTimeout(() => {
                router.get(
                    route('admissions.index'),
                    {
                        search: searchQuery,
                        status: filters.status || '',
                        page: 1,
                    },
                    { preserveState: true, replace: true },
                );
            }, 300);

            return () => clearTimeout(timeout);
        }
        // `filters.status` is intentionally read without being a dependency:
        // adding it would re-fire the debounced visit on every status change.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [searchQuery, filters.search]);

    const handleFilter = (status: string | null) => {
        router.get(
            route('admissions.index'),
            {
                search: searchQuery,
                status: status || '',
                page: 1,
            },
            { preserveState: true },
        );
    };

    const clearFilters = () => {
        setSearchQuery('');
        router.get(route('admissions.index'), {}, { preserveState: true });
    };

    const statusLabel = (status: string) =>
        status === 'admitted'
            ? t('page.checkin.admitted')
            : status === 'pending'
              ? t('page.checkin.pending')
              : t('page.checkin.discharged');

    const statCards: PatientStat[] = [
        {
            label: t('page.checkin.total_registry'),
            value: stats.total,
            icon: UsersIcon,
            tone: 'neutral',
        },
        {
            label: t('page.checkin.currently_admitted'),
            value: stats.active,
            icon: CheckBadgeIcon,
            tone: 'positive',
        },
        {
            label: t('page.checkin.pending_placement'),
            value: stats.pending,
            icon: ClockIcon,
            tone: 'warning',
        },
        {
            label: t('page.checkin.total_discharged'),
            value: stats.discharged,
            icon: ArchiveBoxIcon,
            tone: 'accent',
        },
    ];

    const hasAdmissions = admissions.data.length > 0;

    return (
        <AppLayout
            headerTitle={t('page.checkin.title')}
            headerSubtitle={t('page.checkin.subtitle')}
        >
            <Head title={t('page.checkin.head_title')} />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.checkin.breadcrumb_admissions')}
                    title={t('page.checkin.title')}
                    description={t('page.checkin.subtitle')}
                    breadcrumbs={[
                        { label: t('page.checkin.breadcrumb_admissions') },
                    ]}
                    actions={
                        <Button
                            href={route('appointments.create')}
                            variant="primary"
                        >
                            {t('page.checkin.new_admission')}
                        </Button>
                    }
                />

                <PatientStatGrid stats={statCards} />

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('page.checkin.search_placeholder')}
                    />
                    <div className="flex flex-wrap items-center gap-1.5">
                        {statusFilters.map((status) => {
                            const active = filters.status === status;

                            return (
                                <Button
                                    key={status}
                                    size="sm"
                                    variant={active ? 'primary' : 'outline'}
                                    onClick={() =>
                                        handleFilter(active ? '' : status)
                                    }
                                >
                                    {statusLabel(status)}
                                </Button>
                            );
                        })}
                    </div>
                    <ToolbarSpacer />
                    <SegmentedControl
                        value={viewMode}
                        onChange={setViewMode}
                        options={[
                            {
                                value: 'grid',
                                icon: Squares2X2Icon,
                                ariaLabel: 'Grid view',
                            },
                            {
                                value: 'table',
                                icon: ListBulletIcon,
                                ariaLabel: 'Table view',
                            },
                        ]}
                    />
                </Toolbar>

                <AnimatePresence mode="wait">
                    {!hasAdmissions && (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -12 }}
                        >
                            <PatientEmptyState
                                icon={InboxIcon}
                                title={t('page.checkin.empty_title')}
                                description={t(
                                    'page.checkin.empty_description',
                                )}
                                actionLabel={t('page.checkin.new_admission')}
                                actionHref={route('appointments.create')}
                                secondaryAction={
                                    <Button
                                        variant="secondary"
                                        icon={XMarkIcon}
                                        onClick={clearFilters}
                                    >
                                        {t('page.checkin.clear_filters')}
                                    </Button>
                                }
                            />
                        </motion.div>
                    )}

                    {hasAdmissions && viewMode === 'grid' && (
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                        >
                            {admissions.data.map((admission) => (
                                <Card
                                    key={admission.id}
                                    className="flex flex-col transition-colors duration-200 ease-premium hover:border-line-strong"
                                >
                                    <CardHeader
                                        actions={
                                            <AdmissionStatusBadge
                                                status={admission.status}
                                                label={statusLabel(
                                                    admission.status,
                                                )}
                                            />
                                        }
                                    >
                                        <PatientIdentity
                                            firstName={
                                                admission.patient.first_name
                                            }
                                            lastName={
                                                admission.patient.last_name
                                            }
                                            patientUid={
                                                admission.patient.patient_uid
                                            }
                                        />
                                    </CardHeader>

                                    <CardContent className="flex-1">
                                        <div className="flex items-center gap-2 text-[12px] font-medium text-ink-muted">
                                            <CalendarDaysIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                            {admission.admission_date}
                                        </div>
                                        <p className="mt-2 line-clamp-2 text-[12.5px] leading-relaxed text-ink-subtle">
                                            {admission.reason_for_admission ||
                                                t('page.checkin.no_reason')}
                                        </p>
                                    </CardContent>

                                    <CardFooter className="justify-between gap-2">
                                        <AdmissionLocation
                                            code={admission.bed?.code}
                                            ward={admission.bed?.ward?.name}
                                        />
                                        <div className="flex items-center gap-1.5">
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                icon={EyeIcon}
                                                href={route(
                                                    'admissions.show',
                                                    admission.id,
                                                )}
                                                aria-label={
                                                    t('common.view_details') ||
                                                    'View details'
                                                }
                                            />
                                            {admission.status == 'pending' && (
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    icon={ArrowRightIcon}
                                                    href={route(
                                                        'admissions.create',
                                                        admission.id,
                                                    )}
                                                    aria-label={
                                                        t('common.manage') ||
                                                        'Manage'
                                                    }
                                                />
                                            )}
                                        </div>
                                    </CardFooter>
                                </Card>
                            ))}
                        </motion.div>
                    )}

                    {hasAdmissions && viewMode === 'table' && (
                        <motion.div
                            key="table"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                        >
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHeaderCell>
                                            {t('page.checkin.table_patient')}
                                        </TableHeaderCell>
                                        <TableHeaderCell>
                                            {t(
                                                'page.checkin.table_date_reason',
                                            )}
                                        </TableHeaderCell>
                                        <TableHeaderCell>
                                            {t('page.checkin.table_location')}
                                        </TableHeaderCell>
                                        <TableHeaderCell>
                                            {t('page.checkin.table_status')}
                                        </TableHeaderCell>
                                        <TableHeaderCell numeric>
                                            {t('common.actions')}
                                        </TableHeaderCell>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    {admissions.data.map((admission) => (
                                        <TableRow key={admission.id}>
                                            <TableCell>
                                                <PatientIdentity
                                                    firstName={
                                                        admission.patient
                                                            .first_name
                                                    }
                                                    lastName={
                                                        admission.patient
                                                            .last_name
                                                    }
                                                    patientUid={
                                                        admission.patient
                                                            .patient_uid
                                                    }
                                                    size="sm"
                                                />
                                            </TableCell>
                                            <TableCell muted>
                                                <div className="flex items-center gap-2 text-[12.5px]">
                                                    <CalendarDaysIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                                    {admission.admission_date}
                                                </div>
                                                <div className="mt-0.5 line-clamp-1 max-w-[220px] text-[12px] text-ink-subtle">
                                                    {
                                                        admission.reason_for_admission
                                                    }
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <AdmissionLocation
                                                    code={admission.bed?.code}
                                                    ward={
                                                        admission.bed?.ward
                                                            ?.name
                                                    }
                                                />
                                            </TableCell>
                                            <TableCell>
                                                <AdmissionStatusBadge
                                                    status={admission.status}
                                                    label={statusLabel(
                                                        admission.status,
                                                    )}
                                                />
                                            </TableCell>
                                            <TableCell numeric>
                                                <div className="flex items-center justify-end gap-1.5">
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        icon={EyeIcon}
                                                        href={route(
                                                            'admissions.show',
                                                            admission.id,
                                                        )}
                                                        aria-label={
                                                            t(
                                                                'common.view_details',
                                                            ) || 'View details'
                                                        }
                                                    />

                                                    {admission.status ==
                                                        'pending' && (
                                                        <Button
                                                            size="icon"
                                                            variant="ghost"
                                                            icon={
                                                                ArrowRightIcon
                                                            }
                                                            href={route(
                                                                'admissions.create',
                                                                admission.id,
                                                            )}
                                                            aria-label={
                                                                t(
                                                                    'common.manage',
                                                                ) || 'Manage'
                                                            }
                                                        />
                                                    )}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </motion.div>
                    )}
                </AnimatePresence>

                {hasAdmissions && (
                    <Pagination
                        links={admissions.links}
                        meta={{
                            from: admissions.from || 0,
                            to: admissions.to || 0,
                            total: admissions.total || admissions.data.length,
                        }}
                    />
                )}
            </div>
        </AppLayout>
    );
}
