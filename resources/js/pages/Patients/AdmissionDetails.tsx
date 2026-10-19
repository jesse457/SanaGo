import {
    ArrowLeftIcon,
    ArrowRightOnRectangleIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    DocumentTextIcon,
    FolderOpenIcon,
    InboxIcon,
    MapPinIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, router, useForm } from '@inertiajs/react';
import React from 'react';
import { toast } from 'sonner';
import {
    Button,
    Card,
    CardContent,
    CardHeader,
    Field,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    Select,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { AdmissionStatusBadge } from '@/pages/Patients/components/AdmissionStatusBadge';
import { PatientEmptyState } from '@/pages/Patients/components/PatientEmptyState';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';

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

    // Form setup for the dedicated discharge route
    const { post, processing } = useForm();

    const handleAdmissionChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const id = e.target.value;
        router.get(route('admissions.show', id), {}, { preserveState: true });
    };

    const handleDischarge = () => {
        if (!selectedAdmission) {
            return;
        }

        // Triggers a POST request to admissions.discharge
        post(route('admissions.discharge', selectedAdmission.id), {
            onSuccess: () => {
                toast.success(
                    t('flash.admission.discharged') ||
                        'Patient discharged successfully.',
                );
            },
            onError: () => {
                toast.error(
                    t('flash.error') ||
                        'Failed to complete discharge operation.',
                );
            },
        });
    };

    // Helper references for rendering fields cleanly
    const doctorName =
        selectedAdmission?.attending_doctor?.name ||
        selectedAdmission?.attendingDoctor?.name;
    const bedDesignation =
        selectedAdmission?.bed?.bed_number ||
        selectedAdmission?.bed?.code ||
        'N/A';
    const wardName = selectedAdmission?.bed?.ward?.name;

    return (
        <AppLayout
            headerTitle={t('page.checkin.title') || 'Admission Overview'}
            headerSubtitle={
                t('page.checkin.subtitle') ||
                'Detailed view of patient admission history and current status.'
            }
        >
            <Head
                title={
                    t('page.checkin.admission_details') || 'Admission Overview'
                }
            />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.checkin.breadcrumb_admissions')}
                    title={
                        t('page.checkin.admission_details') ||
                        'Admission Overview'
                    }
                    description={
                        t('page.checkin.subtitle') ||
                        'Detailed view of patient admission history and current status.'
                    }
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        {
                            label:
                                t('page.checkin.breadcrumb_admissions') ||
                                'Admissions',
                            href: route('admissions.index'),
                        },
                        {
                            label:
                                t('page.checkin.details') ||
                                'Admission Details',
                        },
                    ]}
                    actions={
                        <Button
                            href={route('admissions.index')}
                            variant="secondary"
                            icon={ArrowLeftIcon}
                        >
                            {t('common.back_to_list') || 'Back to List'}
                        </Button>
                    }
                />

                <Card>
                    <CardHeader>
                        <PatientIdentity
                            firstName={patient.first_name}
                            lastName={patient.last_name}
                            patientUid={patient.patient_uid}
                            demographics={
                                t(`common.${patient.gender}`) || patient.gender
                            }
                            size="lg"
                        />
                    </CardHeader>

                    <CardContent>
                        {admissions.length > 0 ? (
                            <>
                                <div className="max-w-md">
                                    <Field
                                        label={
                                            t(
                                                'page.checkin.select_admission_record',
                                            ) || 'Select Admission Record'
                                        }
                                        htmlFor="admissionSelector"
                                    >
                                        <Select
                                            id="admissionSelector"
                                            value={selectedAdmission?.id || ''}
                                            onChange={handleAdmissionChange}
                                        >
                                            {admissions.map((option) => (
                                                <option
                                                    key={option.id}
                                                    value={option.id}
                                                >
                                                    {option.admission_date} —{' '}
                                                    {option.status}
                                                </option>
                                            ))}
                                        </Select>
                                    </Field>
                                </div>

                                {selectedAdmission ? (
                                    <div className="mt-6 space-y-6">
                                        <div className="flex flex-wrap items-center gap-3 rounded-lg border border-line bg-surface-muted px-4 py-3">
                                            <CheckCircleIcon className="h-4 w-4 shrink-0 text-accent" />
                                            <AdmissionStatusBadge
                                                status={
                                                    selectedAdmission.status
                                                }
                                            />
                                            <span className="ml-auto text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                {t(
                                                    'page.checkin.current_status',
                                                ) || 'Current Status'}
                                            </span>
                                        </div>

                                        <KeyValueGrid columns={2}>
                                            <KeyValue
                                                label={
                                                    t(
                                                        'page.checkin.assigned_doctor',
                                                    ) || 'Assigned Doctor'
                                                }
                                                value={
                                                    doctorName ??
                                                    t('common.not_assigned') ??
                                                    'Not Assigned'
                                                }
                                                icon={UserIcon}
                                            />
                                            <KeyValue
                                                label={
                                                    t(
                                                        'page.checkin.bed_allocation',
                                                    ) || 'Bed Allocation'
                                                }
                                                value={
                                                    <>
                                                        {t(
                                                            'page.checkin.bed',
                                                        ) || 'Bed'}{' '}
                                                        {bedDesignation}
                                                        <span className="ml-1 text-[12px] font-normal text-ink-subtle">
                                                            (
                                                            {wardName ??
                                                                t(
                                                                    'common.ward_na',
                                                                ) ??
                                                                'Ward N/A'}
                                                            )
                                                        </span>
                                                    </>
                                                }
                                                icon={MapPinIcon}
                                            />
                                            <KeyValue
                                                label={
                                                    t(
                                                        'page.checkin.admission_date',
                                                    ) || 'Admission Date'
                                                }
                                                value={
                                                    selectedAdmission.admission_date
                                                }
                                                icon={CalendarDaysIcon}
                                            />
                                            <KeyValue
                                                label={
                                                    t(
                                                        'page.checkin.discharge_date',
                                                    ) || 'Discharge Date'
                                                }
                                                value={
                                                    selectedAdmission.discharge_date ??
                                                    t('page.checkin.ongoing') ??
                                                    'Ongoing'
                                                }
                                                icon={ArrowRightOnRectangleIcon}
                                            />
                                        </KeyValueGrid>

                                        <div className="rounded-lg border border-line bg-surface-muted px-4 py-3">
                                            <p className="flex items-center gap-1.5 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                <DocumentTextIcon className="h-3.5 w-3.5" />
                                                {t(
                                                    'page.checkin.reason_for_admission',
                                                ) || 'Reason for Admission'}
                                            </p>
                                            <p className="mt-1.5 text-[13px] leading-relaxed text-ink-muted">
                                                {
                                                    selectedAdmission.reason_for_admission
                                                }
                                            </p>
                                        </div>

                                        <div className="flex flex-col gap-2 border-t border-line pt-5 sm:flex-row sm:justify-end">
                                            <Button
                                                href={route('admissions.index')}
                                                variant="secondary"
                                                icon={ArrowLeftIcon}
                                            >
                                                {t('common.back_to_list') ||
                                                    'Back to List'}
                                            </Button>

                                            {selectedAdmission.status ===
                                            'Admitted' ? (
                                                <Button
                                                    variant="danger"
                                                    icon={
                                                        ArrowRightOnRectangleIcon
                                                    }
                                                    loading={processing}
                                                    onClick={handleDischarge}
                                                >
                                                    {t(
                                                        'page.checkin.discharge_patient',
                                                    ) || 'Discharge Patient'}
                                                </Button>
                                            ) : (
                                                <Button
                                                    variant="secondary"
                                                    icon={CheckCircleIcon}
                                                    disabled
                                                >
                                                    {t(
                                                        'page.checkin.patient_discharged',
                                                    ) || 'Patient Discharged'}
                                                </Button>
                                            )}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="mt-6">
                                        <PatientEmptyState
                                            icon={InboxIcon}
                                            title={
                                                t(
                                                    'page.checkin.no_record_selected',
                                                ) || 'No record selected'
                                            }
                                            description={
                                                t(
                                                    'page.checkin.select_to_view',
                                                ) ||
                                                'Please select an admission record from the dropdown above.'
                                            }
                                        />
                                    </div>
                                )}
                            </>
                        ) : (
                            <PatientEmptyState
                                icon={FolderOpenIcon}
                                title={
                                    t('page.checkin.no_records_found') ||
                                    'No Admission Records Found'
                                }
                                description={
                                    t('page.checkin.no_history') ||
                                    'This patient has no admission history on file.'
                                }
                                actionLabel={
                                    t('page.checkin.return_to_list') ||
                                    'Return to Patient List'
                                }
                                actionHref={route('admissions.index')}
                                actionIcon={ArrowLeftIcon}
                            />
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
