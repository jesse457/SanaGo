import { UserIcon } from '@heroicons/react/24/outline';
import { Field, FieldSet, Input, Select } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface PatientIdentityFields {
    first_name: string;
    last_name: string;
    date_of_birth: string;
    gender: string;
    blood_type: string;
    allergies: string;
}

interface IdentityStepProps {
    data: PatientIdentityFields;
    errors: Partial<Record<keyof PatientIdentityFields, string>>;
    setData: <K extends keyof PatientIdentityFields>(
        key: K,
        value: PatientIdentityFields[K],
    ) => void;
}

const bloodTypes = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

/**
 * IdentityStep — step one of the registration intake: the demographics that
 * become the patient's permanent chart header.
 */
export function IdentityStep({ data, errors, setData }: IdentityStepProps) {
    const { t } = useTranslation();

    return (
        <FieldSet
            title={t('page.patients_create.basic_information')}
            description={t('page.patients_create.identity_details')}
            columns={2}
        >
            <Field
                label={t('page.patients_create.first_name')}
                htmlFor="first_name"
                required
                error={errors.first_name}
            >
                <Input
                    id="first_name"
                    name="first_name"
                    icon={UserIcon}
                    type="text"
                    value={data.first_name}
                    onChange={(event) =>
                        setData('first_name', event.target.value)
                    }
                    placeholder={t(
                        'page.patients_create.first_name_placeholder',
                    )}
                    invalid={Boolean(errors.first_name)}
                    required
                />
            </Field>

            <Field
                label={t('page.patients_create.last_name')}
                htmlFor="last_name"
                required
                error={errors.last_name}
            >
                <Input
                    id="last_name"
                    name="last_name"
                    icon={UserIcon}
                    type="text"
                    value={data.last_name}
                    onChange={(event) =>
                        setData('last_name', event.target.value)
                    }
                    placeholder={t(
                        'page.patients_create.last_name_placeholder',
                    )}
                    invalid={Boolean(errors.last_name)}
                    required
                />
            </Field>

            <Field
                label={t('page.patients_create.date_of_birth')}
                htmlFor="date_of_birth"
                required
                error={errors.date_of_birth}
            >
                <Input
                    id="date_of_birth"
                    name="date_of_birth"
                    type="date"
                    value={data.date_of_birth}
                    onChange={(event) =>
                        setData('date_of_birth', event.target.value)
                    }
                    invalid={Boolean(errors.date_of_birth)}
                    required
                />
            </Field>

            <Field
                label={t('page.patients_create.gender')}
                htmlFor="gender"
                required
                error={errors.gender}
            >
                <Select
                    id="gender"
                    name="gender"
                    value={data.gender}
                    onChange={(event) => setData('gender', event.target.value)}
                    invalid={Boolean(errors.gender)}
                    required
                >
                    <option value="">
                        {t('page.patients_create.select_gender')}
                    </option>
                    <option value="male">{t('common.male')}</option>
                    <option value="female">{t('common.female')}</option>
                    <option value="other">{t('common.other')}</option>
                </Select>
            </Field>

            <Field
                label={t('page.patients_create.blood_type')}
                htmlFor="blood_type"
                optional
                error={errors.blood_type}
            >
                <Select
                    id="blood_type"
                    name="blood_type"
                    value={data.blood_type}
                    onChange={(event) =>
                        setData('blood_type', event.target.value)
                    }
                    invalid={Boolean(errors.blood_type)}
                >
                    <option value="">
                        {t('page.patients_create.unknown')}
                    </option>
                    {bloodTypes.map((group) => (
                        <option key={group} value={group}>
                            {group}
                        </option>
                    ))}
                </Select>
            </Field>

            <Field
                label={t('page.patients_create.allergies')}
                htmlFor="allergies"
                optional
                error={errors.allergies}
            >
                <Input
                    id="allergies"
                    name="allergies"
                    type="text"
                    value={data.allergies}
                    onChange={(event) =>
                        setData('allergies', event.target.value)
                    }
                    placeholder={t(
                        'page.patients_create.allergies_placeholder',
                    )}
                    invalid={Boolean(errors.allergies)}
                />
            </Field>
        </FieldSet>
    );
}
