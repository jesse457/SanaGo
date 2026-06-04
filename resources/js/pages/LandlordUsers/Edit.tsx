import {
    UserGroupIcon,
    EnvelopeIcon,
    LockClosedIcon,
    ShieldCheckIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

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
    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(`/landlord-users/${user.id}`);
    };

    return (
        <SuperAdminLayout
            headerTitle={`Edit ${user.name}`}
            headerSubtitle="Update staff user"
        >
            <Head title={`Edit ${user.name}`} />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: 'Staff Users', href: '/landlord-users' },
                        { label: user.name },
                    ]}
                />

                <PageSection title="User Details" icon={UserGroupIcon}>
                    <form onSubmit={submit} className="space-y-6 p-6">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="col-span-2">
                                <FormInput
                                    name="name"
                                    label="Full Name"
                                    icon={UserGroupIcon}
                                    required
                                    errors={errors}
                                    value={data.name}
                                    onChange={(v) => setData('name', v)}
                                />
                            </div>
                            <div className="col-span-2">
                                <FormInput
                                    name="email"
                                    label="Email"
                                    type="email"
                                    icon={EnvelopeIcon}
                                    required
                                    errors={errors}
                                    value={data.email}
                                    onChange={(v) => setData('email', v)}
                                />
                            </div>
                            <div className="space-y-1">
                                <FormInput
                                    name="password"
                                    label="New Password"
                                    type="password"
                                    icon={LockClosedIcon}
                                    errors={errors}
                                    value={data.password}
                                    onChange={(v) => setData('password', v)}
                                />
                                <p className="ml-1 text-xs text-gray-400 dark:text-neutral-500">
                                    (leave blank to keep)
                                </p>
                            </div>
                            <FormSelect
                                name="role"
                                label="Role"
                                icon={ShieldCheckIcon}
                                required
                                errors={errors}
                                value={data.role}
                                onChange={(v) => setData('role', v)}
                                options={roles.map((r) => ({
                                    value: r,
                                    label: r,
                                }))}
                            />
                        </div>

                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href="/landlord-users"
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        Saving...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" /> Update
                                        User
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </PageSection>
            </div>
        </SuperAdminLayout>
    );
}
