import { CheckIcon, TagIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import {
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { FormPageCard, FormStatusAlerts } from '@/pages/Radiology/components';

const CATEGORY_OPTIONS = [
    { value: 'consultation', label: 'Consultation' },
    { value: 'procedure', label: 'Procédure' },
    { value: 'lab', label: 'Laboratoire' },
    { value: 'radiology', label: 'Radiologie' },
    { value: 'imaging', label: 'Imagerie' },
    { value: 'medication', label: 'Médicament' },
    { value: 'bed', label: 'Lit' },
    { value: 'nursing', label: 'Soins Infirmiers' },
    { value: 'emergency', label: 'Urgence' },
    { value: 'other', label: 'Autre' },
];

export default function ServicesCreate() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        code: '',
        category: 'consultation',
        description: '',
        default_price: 0,
        is_active: true,
        requires_upfront_payment: false,
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('finance.services.store'), {
            onSuccess: () => {
                setSuccessMessage(t('serviceCreatedSuccessfully'));
                reset();
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('newService')}
            headerSubtitle={t('addServiceToCatalogue')}
        >
            <Head title={t('newService')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title={t('newService')}
                    description={t('addServiceToCatalogue')}
                    breadcrumbs={[
                        { label: t('home'), href: route('dashboard') },
                        { label: t('services') },
                        { label: t('newService') },
                    ]}
                />

                <FormStatusAlerts
                    successMessage={successMessage}
                    onDismissSuccess={() => setSuccessMessage(null)}
                    errorMessage={errorMessage}
                    hasFieldErrors={Object.keys(errors).length > 0}
                    successTitle={t('success')}
                    errorTitle={t('error')}
                    errorFallback={t('someFieldsRequireAttention')}
                />

                <FormPageCard
                    title={t('serviceDetails')}
                    headingIcon={TagIcon}
                    onSubmit={handleSubmit}
                    footer={
                        <>
                            <Button
                                variant="secondary"
                                href={route('finance.services.index')}
                            >
                                {t('cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={CheckIcon}
                            >
                                {processing
                                    ? `${t('creating')}...`
                                    : t('createService')}
                            </Button>
                        </>
                    }
                >
                    <FieldSet columns={2}>
                        <Field
                            label={t('serviceName')}
                            htmlFor="name"
                            required
                            error={errors.name}
                        >
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                placeholder={t('egGeneralConsultation')}
                                invalid={Boolean(errors.name)}
                            />
                        </Field>

                        <Field
                            label={t('code')}
                            htmlFor="code"
                            required
                            error={errors.code}
                        >
                            <Input
                                id="code"
                                type="text"
                                value={data.code}
                                onChange={(e) =>
                                    setData('code', e.target.value)
                                }
                                placeholder={t('egConsGen')}
                                invalid={Boolean(errors.code)}
                            />
                        </Field>

                        <Field
                            label={t('category')}
                            htmlFor="category"
                            required
                            error={errors.category}
                        >
                            <Select
                                id="category"
                                value={data.category}
                                onChange={(e) =>
                                    setData('category', e.target.value)
                                }
                                invalid={Boolean(errors.category)}
                            >
                                {CATEGORY_OPTIONS.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label={t('defaultPriceXaf')}
                            htmlFor="default_price"
                            required
                            error={errors.default_price}
                        >
                            <Input
                                id="default_price"
                                type="number"
                                value={data.default_price}
                                onChange={(e) =>
                                    setData(
                                        'default_price',
                                        Number(e.target.value),
                                    )
                                }
                                placeholder="3000"
                                invalid={Boolean(errors.default_price)}
                            />
                        </Field>

                        <Field
                            label={t('description')}
                            htmlFor="description"
                            error={errors.description}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                id="description"
                                value={data.description}
                                onChange={(e) =>
                                    setData('description', e.target.value)
                                }
                                rows={3}
                                placeholder={t('serviceDescriptionOptional')}
                                invalid={Boolean(errors.description)}
                            />
                        </Field>
                    </FieldSet>

                    <div className="flex flex-col gap-3 border-t border-line pt-4">
                        <Checkbox
                            id="is_active"
                            checked={data.is_active}
                            onChange={(e) =>
                                setData('is_active', e.target.checked)
                            }
                            label={t('activeService')}
                        />
                        <Checkbox
                            id="requires_upfront_payment"
                            checked={data.requires_upfront_payment}
                            onChange={(e) =>
                                setData(
                                    'requires_upfront_payment',
                                    e.target.checked,
                                )
                            }
                            label={t('requiresUpfrontPayment')}
                        />
                    </div>
                </FormPageCard>
            </div>
        </AppLayout>
    );
}
