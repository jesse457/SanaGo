import { BanknotesIcon } from '@heroicons/react/24/outline';
import { Field, FieldSet, Input, Select, Textarea } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface AdmissionBed {
    id: number;
    code: string;
    ward?: { name: string };
}

export interface AdmissionDoctor {
    id: number;
    name: string;
}

export interface AdmissionFormFields {
    bed_id: string;
    attending_doctor_id: string;
    admission_date: string;
    reason_for_admission: string;
    observation_fee: number;
}

interface AdmissionFormFieldsProps {
    data: AdmissionFormFields;
    errors: Partial<Record<keyof AdmissionFormFields, string>>;
    availableBeds: AdmissionBed[];
    doctors: AdmissionDoctor[];
    setData: <K extends keyof AdmissionFormFields>(
        key: K,
        value: AdmissionFormFields[K],
    ) => void;
}

/**
 * AdmissionFormFields — the placement, physician, deposit and reason inputs
 * shared by the admission intake screen.
 */
export function AdmissionFormFields({
    data,
    errors,
    availableBeds,
    doctors,
    setData,
}: AdmissionFormFieldsProps) {
    const { t } = useTranslation();

    return (
        <div className="space-y-8">
            <FieldSet
                title={t('page.admission.ward_bed_assignment')}
                description={t('page.admission.placement_description')}
                columns={2}
            >
                <Field
                    label={t('page.admission.ward_bed_assignment')}
                    htmlFor="bed_id"
                    error={errors.bed_id}
                >
                    <Select
                        id="bed_id"
                        name="bed_id"
                        value={data.bed_id}
                        onChange={(event) =>
                            setData('bed_id', event.target.value)
                        }
                        invalid={Boolean(errors.bed_id)}
                    >
                        <option value="">
                            {t('page.admission.select_bed')}
                        </option>
                        {availableBeds.map((bed) => (
                            <option key={bed.id} value={bed.id}>
                                {bed.ward?.name} - Bed {bed.code}
                            </option>
                        ))}
                    </Select>
                </Field>

                <Field
                    label={t('page.admission.attending_physician')}
                    htmlFor="attending_doctor_id"
                    error={errors.attending_doctor_id}
                >
                    <Select
                        id="attending_doctor_id"
                        name="attending_doctor_id"
                        value={data.attending_doctor_id}
                        onChange={(event) =>
                            setData('attending_doctor_id', event.target.value)
                        }
                        invalid={Boolean(errors.attending_doctor_id)}
                    >
                        <option value="">
                            {t('page.admission.select_doctor')}
                        </option>
                        {doctors.map((doctor) => (
                            <option key={doctor.id} value={doctor.id}>
                                Dr. {doctor.name}
                            </option>
                        ))}
                    </Select>
                </Field>
            </FieldSet>

            <FieldSet
                title={t('page.admission.admission_date')}
                description={t('page.admission.billing_description')}
                columns={2}
            >
                <Field
                    label={t('page.admission.admission_date')}
                    htmlFor="admission_date"
                    error={errors.admission_date}
                >
                    <Input
                        id="admission_date"
                        name="admission_date"
                        type="date"
                        value={data.admission_date}
                        onChange={(event) =>
                            setData('admission_date', event.target.value)
                        }
                        invalid={Boolean(errors.admission_date)}
                    />
                </Field>

                <Field
                    label={t('page.admission.observation_deposit')}
                    htmlFor="observation_fee"
                    error={errors.observation_fee}
                >
                    <Input
                        id="observation_fee"
                        name="observation_fee"
                        icon={BanknotesIcon}
                        type="number"
                        value={data.observation_fee}
                        onChange={(event) =>
                            setData(
                                'observation_fee',
                                parseInt(event.target.value) || 0,
                            )
                        }
                        placeholder="5000"
                        invalid={Boolean(errors.observation_fee)}
                    />
                </Field>
            </FieldSet>

            <FieldSet
                title={t('page.admission.reason_for_admission')}
                columns={1}
            >
                <Field
                    label={t('page.admission.reason_for_admission')}
                    htmlFor="reason_for_admission"
                    error={errors.reason_for_admission}
                >
                    <Textarea
                        id="reason_for_admission"
                        name="reason_for_admission"
                        value={data.reason_for_admission}
                        onChange={(event) =>
                            setData('reason_for_admission', event.target.value)
                        }
                        placeholder={t('page.admission.reason_placeholder')}
                        rows={3}
                        invalid={Boolean(errors.reason_for_admission)}
                    />
                </Field>
            </FieldSet>
        </div>
    );
}
