import { EnvelopeIcon, PhoneIcon, UserIcon } from '@heroicons/react/24/outline';
import { Field, FieldSet, Input, Textarea } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

export interface ContactFields {
    phone: string;
    email: string;
    emergency_contact_name: string;
    emergency_contact_phone: string;
    address: string;
}

interface ContactStepProps {
    data: ContactFields;
    errors: Partial<Record<keyof ContactFields, string>>;
    setData: <K extends keyof ContactFields>(
        key: K,
        value: ContactFields[K],
    ) => void;
}

/**
 * ContactStep — step three of the registration intake: how to reach the patient
 * and who to call in an emergency.
 */
export function ContactStep({ data, errors, setData }: ContactStepProps) {
    const { t } = useTranslation();

    return (
        <FieldSet
            title={t('page.patients_create.contact_emergency')}
            description={t('page.patients_create.reach_and_emergency')}
            columns={2}
        >
            <Field
                label={t('page.patients_create.phone_number')}
                htmlFor="phone"
                optional
                error={errors.phone}
            >
                <Input
                    id="phone"
                    name="phone"
                    icon={PhoneIcon}
                    type="text"
                    value={data.phone}
                    onChange={(event) => setData('phone', event.target.value)}
                    placeholder={t('page.patients_create.phone_placeholder')}
                    invalid={Boolean(errors.phone)}
                />
            </Field>

            <Field
                label={t('page.patients_create.email_address')}
                htmlFor="email"
                optional
                error={errors.email}
            >
                <Input
                    id="email"
                    name="email"
                    icon={EnvelopeIcon}
                    type="email"
                    value={data.email}
                    onChange={(event) => setData('email', event.target.value)}
                    placeholder={t('page.patients_create.email_placeholder')}
                    invalid={Boolean(errors.email)}
                />
            </Field>

            <Field
                label={
                    t('page.patients_create.emergency_contact_name') ||
                    'Emergency Contact Name'
                }
                htmlFor="emergency_contact_name"
                optional
                error={errors.emergency_contact_name}
            >
                <Input
                    id="emergency_contact_name"
                    name="emergency_contact_name"
                    icon={UserIcon}
                    type="text"
                    value={data.emergency_contact_name}
                    onChange={(event) =>
                        setData('emergency_contact_name', event.target.value)
                    }
                    placeholder="Emergency Contact Name"
                    invalid={Boolean(errors.emergency_contact_name)}
                />
            </Field>

            <Field
                label={
                    t('page.patients_create.emergency_contact_phone') ||
                    'Emergency Contact Phone'
                }
                htmlFor="emergency_contact_phone"
                optional
                error={errors.emergency_contact_phone}
            >
                <Input
                    id="emergency_contact_phone"
                    name="emergency_contact_phone"
                    icon={PhoneIcon}
                    type="text"
                    value={data.emergency_contact_phone}
                    onChange={(event) =>
                        setData('emergency_contact_phone', event.target.value)
                    }
                    placeholder="Emergency Contact Phone"
                    invalid={Boolean(errors.emergency_contact_phone)}
                />
            </Field>

            <Field
                label={t('page.patients_create.residential_address')}
                htmlFor="address"
                optional
                error={errors.address}
                className="sm:col-span-2"
            >
                <Textarea
                    id="address"
                    name="address"
                    value={data.address}
                    onChange={(event) => setData('address', event.target.value)}
                    placeholder={t('page.patients_create.address_placeholder')}
                    rows={3}
                    invalid={Boolean(errors.address)}
                />
            </Field>
        </FieldSet>
    );
}
