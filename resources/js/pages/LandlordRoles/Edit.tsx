import { ShieldCheckIcon } from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { RoleForm } from './components/RoleForm';

interface EditRole {
    id: number;
    name: string;
    current_permissions: string[];
}

export default function LandlordRolesEdit({
    role,
    permissions,
}: {
    role: EditRole;
    permissions: string[];
}) {
    const { t } = useTranslation();
    const [form, setForm] = useState({
        name: role.name,
        permissions: role.current_permissions || [],
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const setField = <K extends keyof typeof form>(
        key: K,
        value: (typeof form)[K],
    ) => {
        setForm((current) => ({ ...current, [key]: value }));
    };

    const handleSubmit = (event: React.FormEvent) => {
        event.preventDefault();
        setSubmitting(true);
        router.put(`/landlord-roles/${role.id}`, form, {
            onError: (receivedErrors) => {
                setErrors(receivedErrors as Record<string, string>);
                setSubmitting(false);
            },
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${role.name}`}
            headerSubtitle="Update role permissions"
        >
            <Head title={`Edit ${role.name}`} />

            <div className="mx-auto max-w-5xl space-y-5">
                <PageHeader
                    eyebrow="Access control"
                    title={role.name}
                    description="Rename this role or adjust the permissions its members inherit."
                    icon={ShieldCheckIcon}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Roles', href: '/landlord-roles' },
                        { label: role.name },
                    ]}
                />

                <RoleForm
                    data={form}
                    errors={errors}
                    permissions={permissions}
                    processing={submitting}
                    setData={setField}
                    onSubmit={handleSubmit}
                    cancelHref="/landlord-roles"
                    cancelLabel={t('common.cancel')}
                    submitLabel="Update role"
                />
            </div>
        </SuperAdminLayout>
    );
}
