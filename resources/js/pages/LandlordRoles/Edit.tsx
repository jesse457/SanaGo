import {
    ShieldCheckIcon,
    KeyIcon,
    CheckIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import FormInput from '@/Components/FormInput';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

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

    const togglePerm = (p: string) =>
        setForm((f) => ({
            ...f,
            permissions: f.permissions.includes(p)
                ? f.permissions.filter((x) => x !== p)
                : [...f.permissions, p],
        }));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        router.put(`/landlord-roles/${role.id}`, form, {
            onError: (err) => {
                setErrors(err as any);
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
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Roles', href: '/landlord-roles' },
                        { label: role.name },
                    ]}
                />

                <form onSubmit={handleSubmit}>
                    <PageSection title="Role Details" icon={ShieldCheckIcon}>
                        <div className="space-y-6 p-6">
                            <FormInput
                                name="name"
                                label="Role Name"
                                required
                                value={form.name}
                                onChange={(v) => setForm({ ...form, name: v })}
                                errors={errors}
                            />
                        </div>
                    </PageSection>

                    <div className="mt-6">
                        <PageSection
                            title="Permissions"
                            description="Select the permissions for this role"
                            icon={KeyIcon}
                        >
                            <div className="p-6">
                                <div className="grid grid-cols-2 gap-3">
                                    {permissions.map((p) => (
                                        <label
                                            key={p}
                                            className={`flex cursor-pointer items-start rounded-lg border p-3 transition-all ${form.permissions.includes(p) ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700'}`}
                                        >
                                            <input
                                                type="checkbox"
                                                checked={form.permissions.includes(
                                                    p,
                                                )}
                                                onChange={() => togglePerm(p)}
                                                className="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600"
                                            />
                                            <span className="ml-3 text-sm font-medium text-gray-900 dark:text-neutral-100">
                                                {p}
                                            </span>
                                        </label>
                                    ))}
                                </div>
                            </div>
                        </PageSection>
                    </div>

                    <div className="mt-6 flex items-center justify-end gap-3">
                        <Link
                            href="/landlord-roles"
                            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.cancel')}
                        </Link>
                        <button
                            type="submit"
                            disabled={submitting}
                            className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                        >
                            {submitting ? (
                                <>
                                    <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <CheckIcon className="h-4 w-4" /> Update
                                    Role
                                </>
                            )}
                        </button>
                    </div>
                </form>
            </div>
        </SuperAdminLayout>
    );
}
