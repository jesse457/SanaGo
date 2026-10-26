import {
    BeakerIcon,
    DocumentTextIcon,
    PlusIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Button,
    EmptyState,
    Field,
    PageHeader,
    SectionCard,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Patient } from '@/types';
import { PrescriptionActionBar } from './components/PrescriptionActionBar';
import { PrescriptionLineCard } from './components/PrescriptionLineCard';
import type {
    MedicationOption,
    PrescriptionLineValues,
} from './components/PrescriptionLineCard';

interface Props {
    patients: Pick<
        Patient,
        'id' | 'first_name' | 'last_name' | 'patient_uid'
    >[];
    medications: MedicationOption[];
}

export default function PrescriptionsCreate({ patients, medications }: Props) {
    const { t } = useTranslation();
    const [patientSearch] = useState('');

    const { data, setData, post, processing, errors } = useForm({
        patient_id: '',
        notes: '',
        items: [] as PrescriptionLineValues[],
    });

    const patientOptions = patients
        .filter(
            (p) =>
                p.first_name
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()) ||
                p.last_name
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()) ||
                p.patient_uid
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()),
        )
        .map((p) => ({
            value: String(p.id),
            label: `${p.last_name} ${p.first_name} — ${p.patient_uid}`,
        }));

    const addItem = () => {
        setData('items', [
            ...data.items,
            {
                medication_id: '',
                dosage: '',
                frequency: '',
                duration: '',
                quantity: '1',
                instructions: '',
            },
        ]);
    };

    const removeItem = (index: number) => {
        setData(
            'items',
            data.items.filter((_, i) => i !== index),
        );
    };

    const updateItem = (
        index: number,
        field: keyof PrescriptionLineValues,
        value: string,
    ) => {
        const updated = [...data.items];
        updated[index] = { ...updated[index], [field]: value };
        setData('items', updated);
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('prescriptions.store'), {});
    };

    const getItemErr = (index: number, field: string) =>
        errors[`items.${index}.${field}` as keyof typeof errors];

    return (
        <AppLayout
            headerTitle={t('New Prescription')}
            headerSubtitle={t('Create a medical prescription for a patient')}
        >
            <Head title={t('New Prescription')} />

            <div className="mx-auto max-w-5xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={DocumentTextIcon}
                    eyebrow={t('prescriptions')}
                    title={t('New Prescription')}
                    description={t(
                        'Create a medical prescription for a patient',
                    )}
                    breadcrumbs={[
                        { label: t('clinical') },
                        {
                            label: t('prescriptions'),
                            href: route('prescriptions.index'),
                        },
                        { label: t('new') },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-5">
                    <SectionCard title={t('patient')} icon={UserIcon}>
                        <Field
                            label={t('selectPatient')}
                            htmlFor="prescription-patient"
                            required
                            error={errors.patient_id}
                            hint={t('searchPatientByNameOrUid')}
                        >
                            <Select
                                id="prescription-patient"
                                value={data.patient_id}
                                invalid={Boolean(errors.patient_id)}
                                onChange={(e) =>
                                    setData('patient_id', e.target.value)
                                }
                            >
                                <option value="">
                                    {t('searchPatientByNameOrUid')}
                                </option>
                                {patientOptions.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>
                    </SectionCard>

                    <SectionCard
                        title={t('medications')}
                        icon={BeakerIcon}
                        description={`${data.items.length} ${t('items')}`}
                        actions={
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                icon={PlusIcon}
                                onClick={addItem}
                            >
                                {t('addMedication')}
                            </Button>
                        }
                    >
                        {data.items.length === 0 ? (
                            <EmptyState
                                panel={false}
                                icon={BeakerIcon}
                                title={t('noMedicationsAddedYet')}
                                description={errors.items}
                                className="py-8"
                            />
                        ) : (
                            <div className="space-y-4">
                                {data.items.map((item, index) => (
                                    <PrescriptionLineCard
                                        key={index}
                                        index={index}
                                        values={item}
                                        medications={medications}
                                        onChange={(field, value) =>
                                            updateItem(index, field, value)
                                        }
                                        onRemove={() => removeItem(index)}
                                        errorFor={(field) =>
                                            getItemErr(index, field)
                                        }
                                        hasErrors={Object.keys(errors).some(
                                            (k) =>
                                                k.startsWith(`items.${index}`),
                                        )}
                                    />
                                ))}

                                <Button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    icon={PlusIcon}
                                    onClick={addItem}
                                >
                                    {t('addAnotherMedication')}
                                </Button>
                            </div>
                        )}
                    </SectionCard>

                    <SectionCard title={t('notes')} icon={DocumentTextIcon}>
                        <Field
                            label={t('prescriptionNotes')}
                            htmlFor="prescription-notes"
                            optional
                            error={errors.notes}
                        >
                            <Textarea
                                id="prescription-notes"
                                value={data.notes}
                                invalid={Boolean(errors.notes)}
                                onChange={(e) =>
                                    setData('notes', e.target.value)
                                }
                                placeholder={t('prescriptionNotes')}
                                rows={3}
                            />
                        </Field>
                    </SectionCard>

                    <PrescriptionActionBar
                        processing={processing}
                        cancelHref={route('prescriptions.index')}
                    />
                </form>
            </div>
        </AppLayout>
    );
}
