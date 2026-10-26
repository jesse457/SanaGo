import { CheckIcon, UserGroupIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { PageHeader } from '@/Components/ui';
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

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/landlord-users', {
            onSuccess: () => {
                setData('name', '');
                setData('email', '');
                setData('password', '');
                setData('role', roles[0] || '');
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
