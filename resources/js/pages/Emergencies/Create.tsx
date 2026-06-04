import {
    MagnifyingGlassIcon,
    UserCircleIcon,
    HeartIcon,
    ExclamationTriangleIcon,
    MapPinIcon,
    CalendarDaysIcon,
    DocumentTextIcon,
    ClipboardDocumentListIcon,
    CurrencyDollarIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, Link } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    date_of_birth: string | null;
    gender: string | null;
}

interface Props {
    patients: Patient[];
}

export default function EmergencyCreate({ patients }: Props) {
    const { t } = useTranslation();
    const [patientQuery, setPatientQuery] = useState('');
    const [showPatientDropdown, setShowPatientDropdown] = useState(false);
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        null,
    );

    const { data, setData, post, processing, errors } = useForm({
        patient_id: '',
        attending_doctor_id: '',
        triage_nurse_id: '',
        arrival_mode: '',
        chief_complaint: '',
        primary_assessment: '',
        triage_level: 'normal',
        arrived_at: new Date().toISOString().slice(0, 16),
        emergency_fee: '',
        treatment_notes: '',
    });

    const filteredPatients = useMemo(() => {
        if (!patientQuery.trim()) {
            return patients.slice(0, 10);
        }

        const q = patientQuery.toLowerCase();

        return patients
            .filter(
                (p) =>
                    `${p.first_name} ${p.last_name}`
                        .toLowerCase()
                        .includes(q) || p.patient_uid.toLowerCase().includes(q),
            )
            .slice(0, 10);
    }, [patients, patientQuery]);

    const selectPatient = (p: Patient) => {
        setSelectedPatient(p);
        setData('patient_id', String(p.id));
        setPatientQuery(`${p.first_name} ${p.last_name} (${p.patient_uid})`);
        setShowPatientDropdown(false);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('emergencies.store'));
    };

    return (
        <AppLayout
            headerTitle={t('newEmergency')}
            headerSubtitle={t('registerEmergencyCase')}
        >
            <Head title={t('newEmergency')} />
            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('home'), href: route('dashboard') },
                        {
                            label: t('emergencies'),
                            href: route('emergencies.index'),
                        },
                        { label: t('newEmergency') },
                    ]}
                />

                <form onSubmit={handleSubmit}>
                    <PageSection
                        icon={HeartIcon}
                        title={t('registerEmergencyCase')}
                    >
                        <div className="space-y-6 p-6">
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                {/* Patient Search */}
                                <div className="md:col-span-2">
                                    <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        {t('patient')}
                                    </label>
                                    <div className="relative mt-1.5">
                                        <MagnifyingGlassIcon className="absolute top-1/2 left-4 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                        <input
                                            type="text"
                                            value={patientQuery}
                                            onChange={(e) => {
                                                setPatientQuery(e.target.value);
                                                setShowPatientDropdown(true);
                                            }}
                                            onFocus={() =>
                                                setShowPatientDropdown(true)
                                            }
                                            onBlur={() =>
                                                setTimeout(
                                                    () =>
                                                        setShowPatientDropdown(
                                                            false,
                                                        ),
                                                    200,
                                                )
                                            }
                                            placeholder={t(
                                                'searchPatientNameOrUid',
                                            )}
                                            className="w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-12 text-sm font-semibold transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800"
                                        />
                                        {showPatientDropdown &&
                                            filteredPatients.length > 0 && (
                                                <div className="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                                                    {filteredPatients.map(
                                                        (p) => (
                                                            <button
                                                                key={p.id}
                                                                type="button"
                                                                onClick={() =>
                                                                    selectPatient(
                                                                        p,
                                                                    )
                                                                }
                                                                className="flex w-full items-center gap-3 border-b border-gray-50 px-4 py-3 text-left transition-colors last:border-0 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-blue-900/20"
                                                            >
                                                                <UserCircleIcon className="h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                                                <div>
                                                                    <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            p.first_name
                                                                        }{' '}
                                                                        {
                                                                            p.last_name
                                                                        }
                                                                    </p>
                                                                    <p className="text-xs text-gray-500 dark:text-neutral-400">
                                                                        {
                                                                            p.patient_uid
                                                                        }{' '}
                                                                        &middot;{' '}
                                                                        {
                                                                            p.gender
                                                                        }{' '}
                                                                        &middot;{' '}
                                                                        {
                                                                            p.date_of_birth
                                                                        }
                                                                    </p>
                                                                </div>
                                                            </button>
                                                        ),
                                                    )}
                                                </div>
                                            )}
                                    </div>
                                    {selectedPatient && (
                                        <p className="mt-2 text-xs text-blue-600">
                                            <Link
                                                href={`/patients/${selectedPatient.id}`}
                                                className="hover:underline"
                                            >
                                                {t('viewProfile')}{' '}
                                                {selectedPatient.first_name}
                                                {t('sProfile')}
                                            </Link>
                                        </p>
                                    )}
                                    <input
                                        type="hidden"
                                        value={data.patient_id}
                                    />
                                    {errors.patient_id && (
                                        <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                            {errors.patient_id}
                                        </p>
                                    )}
                                </div>

                                <FormSelect
                                    name="triage_level"
                                    label={t('triageLevel')}
                                    icon={ExclamationTriangleIcon}
                                    value={data.triage_level}
                                    onChange={(v) => setData('triage_level', v)}
                                    options={[
                                        { value: 'normal', label: t('normal') },
                                        { value: 'urgent', label: t('urgent') },
                                        {
                                            value: 'emergency',
                                            label: t('emergency'),
                                        },
                                    ]}
                                    errors={errors}
                                />

                                <FormInput
                                    name="attending_doctor_id"
                                    label={t('doctorId')}
                                    type="number"
                                    value={data.attending_doctor_id}
                                    onChange={(v) =>
                                        setData('attending_doctor_id', v)
                                    }
                                    errors={errors}
                                />

                                <FormInput
                                    name="triage_nurse_id"
                                    label={t('triageNurseId')}
                                    type="number"
                                    value={data.triage_nurse_id}
                                    onChange={(v) =>
                                        setData('triage_nurse_id', v)
                                    }
                                    errors={errors}
                                />

                                <FormInput
                                    name="arrival_mode"
                                    label={t('arrivalMode')}
                                    icon={MapPinIcon}
                                    value={data.arrival_mode}
                                    onChange={(v) => setData('arrival_mode', v)}
                                    placeholder={t('ambulanceWalkIn')}
                                    errors={errors}
                                />

                                <FormInput
                                    name="arrived_at"
                                    label={t('arrivalDateTime')}
                                    type="datetime-local"
                                    icon={CalendarDaysIcon}
                                    value={data.arrived_at}
                                    onChange={(v) => setData('arrived_at', v)}
                                    required
                                    errors={errors}
                                />

                                <div className="md:col-span-2">
                                    <FormInput
                                        name="chief_complaint"
                                        label={t('chiefComplaint')}
                                        icon={DocumentTextIcon}
                                        value={data.chief_complaint}
                                        onChange={(v) =>
                                            setData('chief_complaint', v)
                                        }
                                        required
                                        errors={errors}
                                    />
                                </div>

                                <div className="md:col-span-2">
                                    <FormTextarea
                                        name="primary_assessment"
                                        label={t('primaryAssessment')}
                                        icon={ClipboardDocumentListIcon}
                                        value={data.primary_assessment}
                                        onChange={(v) =>
                                            setData('primary_assessment', v)
                                        }
                                        rows={3}
                                        errors={errors}
                                    />
                                </div>

                                <FormInput
                                    name="emergency_fee"
                                    label={t('feeFcfa')}
                                    type="number"
                                    icon={CurrencyDollarIcon}
                                    value={data.emergency_fee}
                                    onChange={(v) =>
                                        setData('emergency_fee', v)
                                    }
                                    step="0.01"
                                    errors={errors}
                                />
                            </div>

                            <div className="flex gap-4 pt-4">
                                <button
                                    type="button"
                                    onClick={() => window.history.back()}
                                    className="flex-1 rounded-xl border border-gray-200 bg-white py-3 font-bold text-gray-500 transition-all hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700"
                                >
                                    {t('cancel')}
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex-[2] rounded-xl bg-blue-600 py-3 font-bold text-white transition-all hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing
                                        ? t('saving')
                                        : t('registerEmergency')}
                                </button>
                            </div>
                        </div>
                    </PageSection>
                </form>
            </div>
        </AppLayout>
    );
}
