import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { PageHeader } from '@/Components/ui';
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

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/landlord-roles', {
            onSuccess: () => {
                setData('name', '');
                setData('permissions', []);
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
                    breadcrumbs={[
                        { label: 'Roles', href: '/landlord-roles' },
                        { label: t('createRole') },
                    ]}
                />

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
