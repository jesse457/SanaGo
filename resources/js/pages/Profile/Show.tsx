import { Head, usePage } from '@inertiajs/react';
import {
    EnvelopeIcon,
    PhoneIcon,
    BuildingOfficeIcon,
    ShieldCheckIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="h-24 bg-gradient-to-r from-blue-600 to-indigo-600" />
                    <div className="px-6 pb-6">
                        <div className="-mt-8 flex items-end gap-4">
                            <div className="flex h-20 w-20 items-center justify-center rounded-xl border-4 border-white bg-gradient-to-br from-blue-500 to-indigo-600 text-2xl font-black text-white shadow-lg dark:border-neutral-800">
                                {initials}
                            </div>
                            <div className="flex-1 pb-1">
                                <h2 className="text-xl font-black text-gray-900 dark:text-neutral-100">
                                    {user?.name}
                                </h2>
                                <span className="inline-block rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 uppercase dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {role}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="border-t border-gray-100 px-6 pb-6 dark:border-neutral-700">
                        <dl className="grid grid-cols-1 gap-4 pt-6 sm:grid-cols-2">
                            <div className="flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-neutral-700/50">
                                <EnvelopeIcon className="h-5 w-5 shrink-0 text-gray-400" />
                                <div>
                                    <p className="text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                        {t('page.profile_show.email')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {user?.email}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-neutral-700/50">
                                <PhoneIcon className="h-5 w-5 shrink-0 text-gray-400" />
                                <div>
                                    <p className="text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                        {t('page.profile_show.phone')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {user?.phone_number ||
                                            t('page.profile_show.not_set')}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-neutral-700/50">
                                <ShieldCheckIcon className="h-5 w-5 shrink-0 text-gray-400" />
                                <div>
                                    <p className="text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                        {t('page.profile_show.role')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 capitalize dark:text-neutral-100">
                                        {role}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-neutral-700/50">
                                <BuildingOfficeIcon className="h-5 w-5 shrink-0 text-gray-400" />
                                <div>
                                    <p className="text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                        {t('page.profile_show.department')}
                                    </p>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {user?.department?.name ||
                                            t('page.profile_show.general')}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-neutral-700/50">
                                {user?.is_active ? (
                                    <CheckCircleIcon className="h-5 w-5 shrink-0 text-emerald-500" />
                                ) : (
                                    <XCircleIcon className="h-5 w-5 shrink-0 text-red-400" />
                                )}
                                <div>
                                    <p className="text-[10px] font-bold tracking-wider text-gray-400 uppercase">
                                        {t('page.profile_show.status')}
                                    </p>
                                    <span
                                        className={`inline-block rounded-full px-2 py-0.5 text-xs font-bold uppercase ${user?.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400'}`}
                                    >
                                        {user?.is_active
                                            ? t('page.profile_show.active')
                                            : t('page.profile_show.disabled')}
                                    </span>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
