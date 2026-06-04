import {
    EnvelopeIcon,
    PhoneIcon,
    BuildingOfficeIcon,
    ShieldCheckIcon,
    CheckCircleIcon,
    XCircleIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, usePage } from '@inertiajs/react';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface User {
    id: number;
    name: string;
    email: string;
    phone_number?: string;
    is_active: boolean;
    department?: { name: string } | null;
    roles?: { name: string }[];
    created_at: string;
}

export default function ProfileShow() {
    const { t } = useTranslation();
    const user = (usePage().props.auth as any)?.user as User;

    const initials = (user?.name || '??')
        .split(' ')
        .map((p: string) => p.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);

    const role =
        user?.roles?.[0]?.name?.replace('_', ' ') ||
        t('page.profile_show.staff');

    return (
        <AppLayout
            headerTitle={t('page.profile_show.title')}
            headerSubtitle={t('page.profile_show.subtitle')}
        >
            <Head title={t('page.profile_show.head_title')} />

            <div className="mx-auto max-w-3xl space-y-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.profile_show.title') },
                    ]}
                />

                <PageSection
                    title={t('page.profile_show.title')}
                    icon={UserCircleIcon}
                >
                    <div className="p-6 pb-0">
                        <div className="flex items-end gap-4">
                            <div className="flex h-20 w-20 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-2xl font-black text-white shadow-lg">
                                {initials}
                            </div>
                            <div className="flex-1 pb-1">
                                <h2 className="text-xl font-black text-gray-900 dark:text-neutral-100">
                                    {user?.name}
                                </h2>
                                <StatusBadge label={role} color="blue" />
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 p-6 pt-4 sm:grid-cols-2">
                        <DetailItem
                            label={t('page.profile_show.email')}
                            value={user?.email}
                            icon={EnvelopeIcon}
                        />
                        <DetailItem
                            label={t('page.profile_show.phone')}
                            value={
                                user?.phone_number ||
                                t('page.profile_show.not_set')
                            }
                            icon={PhoneIcon}
                        />
                        <DetailItem
                            label={t('page.profile_show.role')}
                            value={role}
                            icon={ShieldCheckIcon}
                        />
                        <DetailItem
                            label={t('page.profile_show.department')}
                            value={
                                user?.department?.name ||
                                t('page.profile_show.general')
                            }
                            icon={BuildingOfficeIcon}
                        />
                        <DetailItem
                            label={t('page.profile_show.status')}
                            value={
                                user?.is_active
                                    ? t('page.profile_show.active')
                                    : t('page.profile_show.disabled')
                            }
                            icon={
                                user?.is_active ? CheckCircleIcon : XCircleIcon
                            }
                        />
                    </div>
                </PageSection>
            </div>
        </AppLayout>
    );
}
