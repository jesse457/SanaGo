import {
    BuildingOfficeIcon,
    CheckIcon,
    GlobeAltIcon,
    TagIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { Button, Field, FieldSet, Input, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { FormPageCard, FormStatusAlerts } from '@/pages/Radiology/components';

export default function TenantsCreate() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        slug: '',
        domain: '',
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const submit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post('/tenants', {
            onSuccess: () => {
                setSuccessMessage(t('tenantCreatedSuccessfully'));
                reset();
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={t('newTenant')}
            headerSubtitle={t('registerNewHospitalTenant')}
        >
            <Head title={t('newTenant')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title={t('newTenant')}
                    description={t('registerNewHospitalTenant')}
                    breadcrumbs={[
                        { label: t('home') },
                        { label: t('tenants') },
                        { label: t('newTenant') },
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
                    title={t('hospitalInformation')}
                    headingIcon={BuildingOfficeIcon}
                    onSubmit={submit}
                    footer={
                        <>
                            <Button variant="secondary" href="/tenants">
                                {t('cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={CheckIcon}
                            >
                                {processing
                                    ? `${t('creating')}...`
                                    : t('createTenant')}
                            </Button>
                        </>
                    }
                >
                    <FieldSet columns={2}>
                        <Field
                            label={t('hospitalName')}
                            htmlFor="name"
                            required
                            error={errors.name}
                            className="sm:col-span-2"
                        >
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                                placeholder={t('hospitalNamePlaceholder')}
                                invalid={Boolean(errors.name)}
                            />
                        </Field>

                        <Field
                            label={t('slug')}
                            htmlFor="slug"
                            required
                            error={errors.slug}
                        >
                            <Input
                                id="slug"
                                type="text"
                                value={data.slug}
                                onChange={(e) =>
                                    setData('slug', e.target.value)
                                }
                                placeholder={t('slugPlaceholder')}
                                icon={TagIcon}
                                invalid={Boolean(errors.slug)}
                            />
                        </Field>

                        <Field
                            label={t('domain')}
                            htmlFor="domain"
                            required
                            error={errors.domain}
                        >
                            <Input
                                id="domain"
                                type="text"
                                value={data.domain}
                                onChange={(e) =>
                                    setData('domain', e.target.value)
                                }
                                placeholder={t('domainPlaceholder')}
                                icon={GlobeAltIcon}
                                invalid={Boolean(errors.domain)}
                            />
                        </Field>
                    </FieldSet>
                </FormPageCard>
            </div>
        </SuperAdminLayout>
    );
}
