import { ShieldCheckIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { Alert, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { RoleForm } from './components/RoleForm';

export default function LandlordRolesCreate({
    permissions,
}: {
    permissions: string[];
}) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        permissions: [] as string[],
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        setErrorMessage(null);
        post('/landlord-roles', {
            onSuccess: () => {
                setSuccessMessage(t('roleCreatedSuccessfully'));
                setData('name', '');
                setData('permissions', []);
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={t('createRole')}
            headerSubtitle={t('defineNewLandlordRole')}
        >
            <Head title={t('createRole')} />

            <div className="mx-auto max-w-5xl space-y-5">
                <PageHeader
                    eyebrow="Access control"
                    title={t('createRole')}
                    description={t('defineNewLandlordRole')}
                    icon={ShieldCheckIcon}
                    breadcrumbs={[
                        { label: 'Roles', href: '/landlord-roles' },
                        { label: t('createRole') },
                    ]}
                />

                {successMessage && (
                    <Alert
                        tone="positive"
                        title={t('success')}
                        action={
                            <button
                                type="button"
                                onClick={() => setSuccessMessage(null)}
                                className="rounded-md px-1.5 py-0.5 text-[11px] font-semibold text-positive-ink transition-colors duration-150 hover:bg-positive-soft focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:outline-none"
                            >
                                {t('common.close')}
                            </button>
                        }
                    >
                        {successMessage}
                    </Alert>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <Alert
                        tone="critical"
                        title={t('error')}
                        action={
                            <button
                                type="button"
                                onClick={() => setErrorMessage(null)}
                                className="rounded-md px-1.5 py-0.5 text-[11px] font-semibold text-critical-ink transition-colors duration-150 hover:bg-critical-soft focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:outline-none"
                            >
                                {t('common.close')}
                            </button>
                        }
                    >
                        {errorMessage || t('someFieldsRequireAttention')}
                    </Alert>
                )}

                <RoleForm
                    data={data}
                    errors={errors}
                    permissions={permissions}
                    processing={processing}
                    setData={setData}
                    onSubmit={submit}
                    cancelHref="/landlord-roles"
                    cancelLabel={t('common.cancel')}
                    submitLabel={t('createRole')}
                />
            </div>
        </SuperAdminLayout>
    );
}
