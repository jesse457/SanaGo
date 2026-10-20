import {
    BeakerIcon,
    CalendarDaysIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    ListBulletIcon,
    PencilSquareIcon,
    Squares2X2Icon,
    UserGroupIcon,
    UserMinusIcon,
    UserPlusIcon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useEffect, useMemo, useState } from 'react';
import { toast } from 'sonner';
import { Pagination } from '@/Components/Pagination';
import {
    Alert,
    Badge,
    Button,
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    Dialog,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SegmentedControl,
    Select,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Textarea,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { PatientEmptyState } from '@/pages/Patients/components/PatientEmptyState';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';
import { PatientStatGrid } from '@/pages/Patients/components/PatientStatGrid';
import type { PatientStat } from '@/pages/Patients/components/PatientStatGrid';

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
    patients: {
        data: Patient[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number;
        to: number;
        total: number;
    };
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
        const currentSearch = filters.search || '';

        if (searchQuery !== currentSearch) {
            // Debounced search: the spinner reflects a pending request started
            // by this effect, it is not derived render state.
            // eslint-disable-next-line react-hooks/set-state-in-effect
            setIsSearching(true);
            const timer = setTimeout(() => {
                router.get(
                    route('patients.index'),
                    {
                        search: searchQuery,
                        page: 1,
                    },
                    {
                        preserveState: true,
                        replace: true,
                        onFinish: () => setIsSearching(false),
                    },
                );
            }, 400);

            return () => clearTimeout(timer);
        }
    }, [searchQuery, filters.search]);

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

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

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
            total: patients.total,
            newPatients: patients.data.filter((p) => !p.last_visit_date).length,
            recentVisits: patients.data.filter((p) => {
                if (!p.last_visit_date) {
                    return false;
                }

                const daysDiff =
                    (new Date().getTime() -
                        new Date(p.last_visit_date).getTime()) /
                    (1000 * 3600 * 24);

                return daysDiff <= 30;
            }).length,
            totalVisits: patients.data.reduce(
                (acc, p) => acc + (p.last_visit_date ? 1 : 0),
                0,
            ),
        }),
        [patients.data, patients.total],
    );

    const statCards: PatientStat[] = [
        {
            label: t('page.patients_index.total_patients'),
            value: stats.total,
            icon: UserGroupIcon,
            tone: 'accent',
        },
        {
            label: t('page.patients_index.new_patients'),
            value: stats.newPatients,
            icon: UserPlusIcon,
            tone: 'positive',
        },
        {
            label: t('page.patients_index.recent_visits'),
            value: stats.recentVisits,
            icon: ClockIcon,
            tone: 'neutral',
        },
        {
            label: t('page.patients_index.active_records'),
            value: stats.totalVisits,
            icon: ClipboardDocumentListIcon,
            tone: 'warning',
        },
    ];

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

    const hasPatients = patients.data.length > 0;

    return (
        <AppLayout
            headerTitle={t('page.patients_index.title')}
            headerSubtitle={t('page.patients_index.subtitle')}
        >
            <Head title={t('page.patients_index.title')} />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.patients_index.subtitle')}
                    title={t('page.patients_index.title')}
                    breadcrumbs={[{ label: t('page.patients_index.title') }]}
                    actions={
                        <Button
                            href={route('patients.create')}
                            icon={UserPlusIcon}
                        >
                            {t('page.patients_index.add_patient')}
                        </Button>
                    }
                />

                <PatientStatGrid stats={statCards} />

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t(
                            'page.patients_index.search_placeholder',
                        )}
                        busy={isSearching}
                    />
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

                {!hasPatients && (
                    <PatientEmptyState
                        icon={UserMinusIcon}
                        title={t('page.patients_index.empty_title')}
                        description={t('page.patients_index.empty_description')}
                        actionLabel={t('page.patients_index.add_patient')}
                        actionHref={route('patients.create')}
                        actionIcon={UserPlusIcon}
                    />
                )}

                {hasPatients && viewMode === 'grid' && (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {patients.data.map((patient) => (
                            <Card
                                key={patient.id}
                                className="group flex flex-col transition-colors duration-200 ease-premium hover:border-line-strong"
                            >
                                <CardHeader
                                    actions={
                                        <Badge
                                            tone={
                                                patient.last_visit_date
                                                    ? 'positive'
                                                    : 'warning'
                                            }
                                            variant="soft"
                                            dot
                                        >
                                            {patient.last_visit_date
                                                ? t('common.active')
                                                : t('common.new')}
                                        </Badge>
                                    }
                                >
                                    <PatientIdentity
                                        firstName={patient.first_name}
                                        lastName={patient.last_name}
                                        patientUid={patient.patient_uid}
                                        photoUrl={patient.photo_url}
                                        demographics={patient.gender}
                                    />
                                </CardHeader>

                                <CardContent className="flex-1">
                                    <div className="flex items-end gap-7">
                                        <div>
                                            <p className="text-[15px] font-semibold text-ink tabular-nums">
                                                {patient.age}
                                            </p>
                                            <p className="mt-0.5 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                {t('page.patients_index.years')}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-[15px] font-semibold text-ink tabular-nums">
                                                {patient.phone?.slice(-4) ||
                                                    '--'}
                                            </p>
                                            <p className="mt-0.5 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                {t('page.patients_index.phone')}
                                            </p>
                                        </div>
                                        <div className="ml-auto flex items-center gap-1.5 text-[12px] text-ink-subtle tabular-nums">
                                            <CalendarDaysIcon className="h-3.5 w-3.5" />
                                            {formatDate(
                                                patient.last_visit_date,
                                            )}
                                        </div>
                                    </div>
                                </CardContent>

                                <CardFooter className="justify-start gap-2">
                                    <Button
                                        size="sm"
                                        variant="secondary"
                                        icon={ClipboardDocumentListIcon}
                                        onClick={() =>
                                            navigateToProfile(patient)
                                        }
                                        className="flex-1"
                                    >
                                        {t('page.patients_index.records')}
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        icon={BeakerIcon}
                                        href={route(
                                            'vitals.create',
                                            patient.id,
                                        )}
                                        className="flex-1"
                                    >
                                        {t('page.patients_index.vitals')}
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        icon={PencilSquareIcon}
                                        aria-label={t('common.edit')}
                                        onClick={() => openEditModal(patient)}
                                    />
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}

                {hasPatients && viewMode === 'table' && (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t(
                                        'page.patients_index.table_patient_details',
                                    )}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.patients_index.table_contact')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t(
                                        'page.patients_index.table_demographics',
                                    )}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.patients_index.table_last_visit')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('common.actions')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {patients.data.map((patient) => (
                                <TableRow key={patient.id}>
                                    <TableCell>
                                        <PatientIdentity
                                            firstName={patient.first_name}
                                            lastName={patient.last_name}
                                            patientUid={patient.patient_uid}
                                            photoUrl={patient.photo_url}
                                            size="sm"
                                        />
                                    </TableCell>
                                    <TableCell muted>
                                        {patient.phone ?? '—'}
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="tabular-nums">
                                            {patient.age}{' '}
                                            {t('page.patients_index.yrs')}
                                        </span>
                                        {' · '}
                                        {patient.gender}
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="tabular-nums">
                                            {formatDate(
                                                patient.last_visit_date,
                                            )}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                icon={BeakerIcon}
                                                aria-label={t(
                                                    'page.patients_index.vitals',
                                                )}
                                                href={route(
                                                    'vitals.create',
                                                    patient.id,
                                                )}
                                            />
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                icon={ClipboardDocumentListIcon}
                                                aria-label={t(
                                                    'page.patients_index.records',
                                                )}
                                                onClick={() =>
                                                    navigateToProfile(patient)
                                                }
                                            />
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                icon={PencilSquareIcon}
                                                aria-label={t('common.edit')}
                                                onClick={() =>
                                                    openEditModal(patient)
                                                }
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                {hasPatients && (
                    <Pagination
                        links={patients.links}
                        meta={{
                            from: patients.from,
                            to: patients.to,
                            total: patients.total,
                        }}
                    />
                )}
            </div>

            <Dialog
                open={modalOpen}
                onClose={closeModal}
                title={t('page.patients_index.edit_patient')}
                description={t('page.patients_index.patient_uid')}
                size="lg"
                footer={
                    <>
                        <Button variant="secondary" onClick={closeModal}>
                            {t('common.cancel')}
                        </Button>
                        <Button
                            type="submit"
                            form="patient-edit-form"
                            loading={processing}
                            disabled={processing}
                        >
                            {processing
                                ? t('common.processing')
                                : t('common.save_changes')}
                        </Button>
                    </>
                }
            >
                <form
                    id="patient-edit-form"
                    onSubmit={handleSubmit}
                    className="flex flex-col gap-5"
                >
                    {Object.keys(errors).length > 0 && (
                        <Alert tone="critical" title={t('common.edit')}>
                            {errors.first_name ??
                                errors.last_name ??
                                errors.gender ??
                                errors.age ??
                                errors.phone ??
                                errors.address}
                        </Alert>
                    )}

                    <Field
                        label={t('page.patients_index.patient_uid')}
                        htmlFor="patient_uid"
                    >
                        <Input
                            id="patient_uid"
                            value={selectedPatient?.patient_uid || ''}
                            readOnly
                            disabled
                        />
                    </Field>

                    <FieldSet columns={2}>
                        <Field
                            label={t('page.patients_index.first_name')}
                            htmlFor="first_name"
                            required
                            error={errors.first_name}
                        >
                            <Input
                                id="first_name"
                                name="first_name"
                                value={data.first_name}
                                invalid={Boolean(errors.first_name)}
                                onChange={(event) =>
                                    setData('first_name', event.target.value)
                                }
                            />
                        </Field>
                        <Field
                            label={t('page.patients_index.last_name')}
                            htmlFor="last_name"
                            required
                            error={errors.last_name}
                        >
                            <Input
                                id="last_name"
                                name="last_name"
                                value={data.last_name}
                                invalid={Boolean(errors.last_name)}
                                onChange={(event) =>
                                    setData('last_name', event.target.value)
                                }
                            />
                        </Field>
                        <Field
                            label={t('page.patients_index.gender')}
                            htmlFor="gender"
                            error={errors.gender}
                        >
                            <Select
                                id="gender"
                                name="gender"
                                value={data.gender}
                                invalid={Boolean(errors.gender)}
                                onChange={(event) =>
                                    setData(
                                        'gender',
                                        event.target.value as
                                            | 'male'
                                            | 'female'
                                            | 'other',
                                    )
                                }
                            >
                                <option value="male">{t('common.male')}</option>
                                <option value="female">
                                    {t('common.female')}
                                </option>
                                <option value="other">
                                    {t('common.other')}
                                </option>
                            </Select>
                        </Field>
                        <Field
                            label={t('page.patients_index.age')}
                            htmlFor="age"
                            error={errors.age}
                        >
                            <Input
                                id="age"
                                name="age"
                                type="number"
                                value={data.age}
                                invalid={Boolean(errors.age)}
                                onChange={(event) =>
                                    setData(
                                        'age',
                                        parseInt(event.target.value) || 0,
                                    )
                                }
                            />
                        </Field>
                        <Field
                            label={t('page.patients_index.phone_number')}
                            htmlFor="phone"
                            error={errors.phone}
                            className="sm:col-span-2"
                        >
                            <Input
                                id="phone"
                                name="phone"
                                value={data.phone}
                                invalid={Boolean(errors.phone)}
                                onChange={(event) =>
                                    setData('phone', event.target.value)
                                }
                            />
                        </Field>
                        <Field
                            label={t('page.patients_index.address')}
                            htmlFor="address"
                            error={errors.address}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                id="address"
                                name="address"
                                rows={2}
                                value={data.address}
                                invalid={Boolean(errors.address)}
                                onChange={(event) =>
                                    setData('address', event.target.value)
                                }
                            />
                        </Field>
                    </FieldSet>
                </form>
            </Dialog>
        </AppLayout>
    );
}
