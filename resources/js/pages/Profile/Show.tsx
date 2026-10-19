import {
    BuildingOfficeIcon,
    CalendarIcon,
    Cog6ToothIcon,
    CheckBadgeIcon,
    EnvelopeIcon,
    MapPinIcon,
    PhoneIcon,
    ShieldCheckIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import {
    Avatar,
    Badge,
    Button,
    Card,
    CardContent,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface User {
    id: number;
    name: string;
    email: string;
    avatar_url?: string | null;
    phone_number?: string | null;
    address?: string | null;
    date_of_birth?: string | null;
    gender?: string | null;
    hire_date?: string | null;
    is_active: boolean;
    department?: { name: string } | null;
    roles?: { name: string }[];
    created_at?: string;
}

export default function ProfileShow() {
    const { t } = useTranslation();
    const user = (usePage().props.auth as any)?.user as User;

    const role =
        user?.roles?.[0]?.name?.replace('_', ' ') ||
        t('page.profile_show.staff');

    const formatDate = (dateString?: string | null) => {
        if (!dateString) {
            return t('page.profile_show.not_set');
        }

        return new Date(dateString).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.profile_show.title')}
            headerSubtitle={user?.name}
        >
            <Head title={`${t('page.profile_show.title')} - ${user?.name}`} />

            <div className="mx-auto max-w-5xl space-y-6 pb-12">
                <PageHeader
                    title={t('page.profile_show.title')}
                    description={t('page.profile_show.subtitle')}
                    icon={UserCircleIcon}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        { label: t('page.profile_show.title') },
                    ]}
                    actions={
                        <Button
                            variant="secondary"
                            href={route('settings')}
                            icon={Cog6ToothIcon}
                        >
                            Settings
                        </Button>
                    }
                />

                <div className="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,20rem)_minmax(0,1fr)]">
                    {/* ─── Identity ─── */}
                    <Card className="h-fit">
                        <CardContent className="flex flex-col items-center gap-3 py-6 text-center">
                            <Avatar
                                src={user?.avatar_url}
                                name={user?.name}
                                size="xl"
                            />
                            <div className="min-w-0">
                                <h2 className="truncate text-[15px] font-semibold tracking-tight text-ink">
                                    {user?.name}
                                </h2>
                                <p className="mt-0.5 truncate text-[12.5px] text-ink-muted capitalize">
                                    {role} ·{' '}
                                    {user?.department?.name ||
                                        t('page.profile_show.general')}
                                </p>
                            </div>
                            <Badge
                                tone={user?.is_active ? 'positive' : 'critical'}
                                variant="soft"
                                dot
                            >
                                {user?.is_active
                                    ? t('page.profile_show.active')
                                    : t('page.profile_show.disabled')}
                            </Badge>
                        </CardContent>
                    </Card>

                    {/* ─── Detail + settings ─── */}
                    <div className="flex flex-col gap-5">
                        <SectionCard
                            title={t('page.profile_show.title')}
                            description="Your identity on record. Contact an administrator to change these details."
                            icon={UserCircleIcon}
                        >
                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label={t('page.profile_show.email')}
                                    value={user?.email}
                                    icon={EnvelopeIcon}
                                />
                                <KeyValue
                                    label={t('page.profile_show.phone')}
                                    value={
                                        user?.phone_number ||
                                        t('page.profile_show.not_set')
                                    }
                                    icon={PhoneIcon}
                                />
                                <KeyValue
                                    label="Date of birth"
                                    value={formatDate(user?.date_of_birth)}
                                    icon={CalendarIcon}
                                />
                                <KeyValue
                                    label="Gender"
                                    value={user?.gender}
                                    icon={UserCircleIcon}
                                    className="capitalize"
                                />
                                <KeyValue
                                    label={t('page.profile_show.department')}
                                    value={
                                        user?.department?.name ||
                                        t('page.profile_show.general')
                                    }
                                    icon={BuildingOfficeIcon}
                                />
                                <KeyValue
                                    label={t('page.profile_show.role')}
                                    value={role}
                                    icon={ShieldCheckIcon}
                                    className="capitalize"
                                />
                                <KeyValue
                                    label="Address"
                                    value={
                                        user?.address ||
                                        t('page.profile_show.not_set')
                                    }
                                    icon={MapPinIcon}
                                    className="sm:col-span-2"
                                />
                            </KeyValueGrid>
                        </SectionCard>

                        <SectionCard
                            title="Account"
                            description="Security and session settings for your account."
                            icon={ShieldCheckIcon}
                        >
                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label={t('page.profile_show.status')}
                                    value={
                                        user?.is_active
                                            ? t('page.profile_show.active')
                                            : t('page.profile_show.disabled')
                                    }
                                    icon={CheckBadgeIcon}
                                />
                                <KeyValue
                                    label="Hire date"
                                    value={formatDate(user?.hire_date)}
                                    icon={CalendarIcon}
                                />
                            </KeyValueGrid>
                        </SectionCard>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
