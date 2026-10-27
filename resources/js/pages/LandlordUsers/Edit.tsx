import { CheckIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { StaffUserForm } from './components/StaffUserForm';

interface EditUser {
    id: number;
    name: string;
    email: string;
    current_role: string;
    is_active: boolean;
}

export default function LandlordUsersEdit({
    user,
    roles,
}: {
    user: EditUser;
    roles: string[];
}) {
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm({
        name: user.name,
        email: user.email,
        password: '',
        role: user.current_role || roles[0],
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        put(`/landlord-users/${user.id}`);
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${user.name}`}
            headerSubtitle="Update staff user"
        >
            <Head title={`Edit ${user.name}`} />

            <div className="mx-auto max-w-3xl space-y-5">
                <PageHeader
                    eyebrow="Organization"
                    title={user.name}
                    description="Update this staff account's identity and role."
                    breadcrumbs={[
                        { label: 'Staff users', href: '/landlord-users' },
                        { label: user.name },
                    ]}
                />

                <StaffUserForm
                    mode="edit"
                    data={data}
                    errors={errors}
                    roles={roles}
                    processing={processing}
                    setData={setData}
                    onSubmit={submit}
                    cancelHref="/landlord-users"
                    cancelLabel={t('common.cancel')}
                    submitLabel="Update user"
                    submitIcon={CheckIcon}
                />
            </div>
        </SuperAdminLayout>
    );
}
