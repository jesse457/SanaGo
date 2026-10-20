import { CheckIcon, UserGroupIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { Alert, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { StaffUserForm } from './components/StaffUserForm';

export default function LandlordUsersCreate({ roles }: { roles: string[] }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        role: roles[0] || '',
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        setErrorMessage(null);
        post('/landlord-users', {
            onSuccess: () => {
                setSuccessMessage(t('userCreatedSuccessfully'));
                setData('name', '');
                setData('email', '');
                setData('password', '');
                setData('role', roles[0] || '');
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={t('addStaffUser')}
            headerSubtitle={t('createNewLandlordMember')}
        >
            <Head title={t('addStaffUser')} />

            <div className="mx-auto max-w-3xl space-y-5">
                <PageHeader
                    eyebrow="Organization"
                    title={t('addStaffUser')}
                    description={t('createNewLandlordMember')}
                    icon={UserGroupIcon}
                    breadcrumbs={[
                        { label: 'Staff users', href: '/landlord-users' },
                        { label: t('addStaffUser') },
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

                <StaffUserForm
                    mode="create"
                    data={data}
                    errors={errors}
                    roles={roles}
                    processing={processing}
                    setData={setData}
                    onSubmit={submit}
                    cancelHref="/landlord-users"
                    cancelLabel={t('common.cancel')}
                    submitLabel={t('createUser')}
                    submitIcon={CheckIcon}
                />
            </div>
        </SuperAdminLayout>
    );
}
