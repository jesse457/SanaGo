import {
    BeakerIcon,
    CheckCircleIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    UserGroupIcon,
    UserIcon,
    UserPlusIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, usePage } from '@inertiajs/react';
import React, { useMemo, useState } from 'react';
import { Alert, Button, PageHeader, SectionCard } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { ContactStep } from '@/pages/Patients/components/ContactStep';
import type { ContactFields } from '@/pages/Patients/components/ContactStep';
import { FormActionBar } from '@/pages/Patients/components/FormActionBar';
import { IdentityStep } from '@/pages/Patients/components/IdentityStep';
import type { PatientIdentityFields } from '@/pages/Patients/components/IdentityStep';
import { PatientPhotoField } from '@/pages/Patients/components/PatientPhotoField';
import { StepProgress } from '@/pages/Patients/components/StepProgress';

interface PatientForm {
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    date_of_birth: string;
    gender: string;
    address: string;
    blood_type: string;
    allergies: string;
    emergency_contact_name: string;
    emergency_contact_phone: string;
    photo: File | null;
}

/** Field names owned by each intake step, used to gate navigation. */
const STEP_FIELDS: (keyof PatientForm)[][] = [
    [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_type',
        'allergies',
    ],
    ['photo'],
    [
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
    ],
];

export default function CreatePatient() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } =
        useForm<PatientForm>({
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            date_of_birth: '',
            gender: '',
            address: '',
            blood_type: '',
            allergies: '',
            emergency_contact_name: '',
            emergency_contact_phone: '',
            photo: null as File | null,
        });

    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const [step, setStep] = useState(0);
    const [touchedSteps, setTouchedSteps] = useState<number[]>([]);

    // Tracks which submit action is actively being processed ('save' or 'vitals')
    // Where to go after registering: the desk that sent us here (cashier,
    // vitals, plain list) keeps its own flow instead of dumping the patient on
    // the index page.
    const { url } = usePage();
    const redirectTarget = useMemo(() => {
        const query = url.split('?')[1] ?? '';

        return new URLSearchParams(query).get('redirect_to') ?? 'save';
    }, [url]);

    const [submitType, setSubmitType] = useState<string | null>(null);

    const stepMeta = useMemo(
        () => [
            {
                label: t('page.patients_create.basic_information'),
                description: t('page.patients_create.identity_details'),
                icon: UserPlusIcon,
            },
            {
                label: t('page.patients_create.patient_photo'),
                description: t('page.patients_create.max_photo_size'),
                icon: UserIcon,
            },
            {
                label: t('page.patients_create.contact_emergency'),
                description: t('page.patients_create.identity_details'),
                icon: UserGroupIcon,
            },
        ],
        [t],
    );

    const stepErrors = errors as Partial<Record<keyof PatientForm, string>>;

    const hasErrorsInStep = (index: number) =>
        STEP_FIELDS[index].some((field) => Boolean(stepErrors[field]));

    const goNext = () => {
        setTouchedSteps((previous) =>
            previous.includes(step) ? previous : [...previous, step],
        );
        setStep((current) => Math.min(current + 1, stepMeta.length - 1));
    };

    const goBack = () => setStep((current) => Math.max(current - 1, 0));

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        // Fallback to 'save' if the form was submitted via keyboard 'Enter'
        const action = submitType || redirectTarget;
        setSubmitType(action);

        post(route('patients.store', { redirect_to: action }), {
            forceFormData: true,
            onSuccess: () => {
                reset();
                setImagePreview(null);
            },
            onFinish: () => {
                setSubmitType(null);
            },
        });
    };

    const currentStep = stepMeta[step];
    const isLastStep = step === stepMeta.length - 1;
    const StepIcon = currentStep?.icon ?? UserPlusIcon;

    return (
        <AppLayout
            headerTitle={t('page.patients_create.title')}
            headerSubtitle={t('page.patients_create.subtitle')}
        >
            <Head title={t('page.patients_create.head_title')} />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.patients_create.new_registration')}
                    title={t('page.patients_create.title')}
                    description={t('page.patients_create.subtitle')}
                    icon={UserPlusIcon}
                    breadcrumbs={[
                        {
                            label: t('page.patients_create.patient_management'),
                            href: route('patients.index'),
                        },
                        { label: t('page.patients_create.new_registration') },
                    ]}
                />

                <SectionCard
                    title={currentStep?.label}
                    description={currentStep?.description}
                    icon={StepIcon}
                    contentClassName="px-0 pb-0 sm:px-0"
                >
                    <div className="px-5 sm:px-6">
                        <StepProgress steps={stepMeta} current={step} />
                    </div>

                    <form onSubmit={submit} className="mt-6">
                        <div className="px-5 pb-6 sm:px-6">
                            {step === 0 && (
                                <IdentityStep
                                    data={data as PatientIdentityFields}
                                    errors={stepErrors}
                                    setData={setData as never}
                                />
                            )}

                            {step === 1 && (
                                <PatientPhotoField
                                    previewUrl={imagePreview}
                                    error={errors.photo}
                                    onSelect={(file) => {
                                        setData('photo', file);
                                        setImagePreview(
                                            URL.createObjectURL(file),
                                        );
                                    }}
                                    onClear={() => {
                                        setImagePreview(null);
                                        setData('photo', null);
                                    }}
                                />
                            )}

                            {step === 2 && (
                                <ContactStep
                                    data={data as ContactFields}
                                    errors={stepErrors}
                                    setData={setData as never}
                                />
                            )}

                            {touchedSteps.includes(step) &&
                                hasErrorsInStep(step) && (
                                    <Alert
                                        tone="critical"
                                        title={t('error')}
                                        className="mt-5"
                                    >
                                        {t('someFieldsRequireAttention')}
                                    </Alert>
                                )}
                        </div>

                        <FormActionBar>
                            {step > 0 ? (
                                <Button
                                    type="button"
                                    variant="secondary"
                                    icon={ChevronLeftIcon}
                                    onClick={goBack}
                                    className="w-full sm:w-auto"
                                >
                                    {t('common.back')}
                                </Button>
                            ) : (
                                <Button
                                    href={route('patients.index')}
                                    variant="ghost"
                                    className="w-full sm:w-auto"
                                >
                                    {t('common.cancel')}
                                </Button>
                            )}

                            {!isLastStep && (
                                <Button
                                    type="button"
                                    variant="primary"
                                    trailingIcon={ChevronRightIcon}
                                    onClick={goNext}
                                    className="w-full sm:w-auto"
                                >
                                    {t('common.continue') || 'Continue'}
                                </Button>
                            )}

                            {isLastStep && (
                                <>
                                    <Button
                                        type="submit"
                                        variant="secondary"
                                        icon={CheckCircleIcon}
                                        loading={
                                            processing &&
                                            submitType !== 'vitals'
                                        }
                                        onClick={() =>
                                            setSubmitType(
                                                redirectTarget === 'vitals'
                                                    ? 'save'
                                                    : redirectTarget,
                                            )
                                        }
                                        className="w-full sm:w-auto"
                                    >
                                        {t(
                                            'page.patients_create.save_registration',
                                        )}
                                    </Button>
                                    <Button
                                        type="submit"
                                        variant="primary"
                                        icon={BeakerIcon}
                                        loading={
                                            processing &&
                                            submitType === 'vitals'
                                        }
                                        onClick={() => setSubmitType('vitals')}
                                        className="w-full sm:w-auto"
                                    >
                                        {t(
                                            'page.patients_create.save_and_take_vitals',
                                        ) || 'Save & Record Vitals'}
                                    </Button>
                                </>
                            )}
                        </FormActionBar>
                    </form>
                </SectionCard>
            </div>
        </AppLayout>
    );
}
