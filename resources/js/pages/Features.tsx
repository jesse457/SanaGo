import {
    BeakerIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    ChartBarIcon,
    HeartIcon,
    UserIcon,
    HomeModernIcon,
    LifebuoyIcon,
    ShieldCheckIcon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import Button from '@/Components/marketing/Button';
import FeatureCard from '@/Components/marketing/FeatureCard';
import SectionHeading from '@/Components/marketing/SectionHeading';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

const CORE_MODULES = [
    {
        icon: <UsersIcon />,
        titleKey: 'marketing.modules.patient_management.title',
        descriptionKey: 'marketing.modules.patient_management.description',
        color: 'bg-accent-soft text-accent-ink',
    },
    {
        icon: <CalendarDaysIcon />,
        titleKey: 'marketing.modules.appointments.title',
        descriptionKey: 'marketing.modules.appointments.description',
        color: 'bg-positive-soft text-positive-ink',
    },
    {
        icon: <BeakerIcon />,
        titleKey: 'marketing.modules.lab.title',
        descriptionKey: 'marketing.modules.lab.description',
        color: 'bg-accent-soft text-accent-ink',
    },
    {
        icon: <BeakerIcon />,
        titleKey: 'marketing.modules.pharmacy.title',
        descriptionKey: 'marketing.modules.pharmacy.description',
        color: 'bg-warning-soft text-warning-ink',
    },
    {
        icon: <ChartBarIcon />,
        titleKey: 'marketing.modules.billing.title',
        descriptionKey: 'marketing.modules.billing.description',
        color: 'bg-critical-soft text-critical-ink',
    },
    {
        icon: <BuildingOffice2Icon />,
        titleKey: 'marketing.modules.infrastructure.title',
        descriptionKey: 'marketing.modules.infrastructure.description',
        color: 'bg-accent-soft text-accent-ink',
    },
];

const SERVICES = [
    {
        icon: <ShieldCheckIcon />,
        titleKey: 'marketing.services.security.title',
        descriptionKey: 'marketing.services.security.description',
        color: 'bg-accent-soft text-accent-ink',
    },
    {
        icon: <HomeModernIcon />,
        titleKey: 'marketing.services.bed_ward.title',
        descriptionKey: 'marketing.services.bed_ward.description',
        color: 'bg-positive-soft text-positive-ink',
    },
    {
        icon: <UserIcon />,
        titleKey: 'marketing.services.consultation_surgery.title',
        descriptionKey: 'marketing.services.consultation_surgery.description',
        color: 'bg-accent-soft text-accent-ink',
    },
    {
        icon: <HeartIcon />,
        titleKey: 'marketing.services.emergency.title',
        descriptionKey: 'marketing.services.emergency.description',
        color: 'bg-warning-soft text-warning-ink',
    },
    {
        icon: <LifebuoyIcon />,
        titleKey: 'marketing.services.support.title',
        descriptionKey: 'marketing.services.support.description',
        color: 'bg-critical-soft text-critical-ink',
    },
];

const ROLE_DETAILS = [
    {
        label: 'Admin',
        roleKey: 'admin',
        icon: <UsersIcon className="h-5 w-5" />,
        color: 'bg-accent-soft text-accent-ink',
    },
    {
        label: 'Doctor',
        roleKey: 'doctor',
        icon: <HeartIcon className="h-5 w-5" />,
        color: 'bg-positive-soft text-positive-ink',
    },
    {
        label: 'Nurse',
        roleKey: 'nurse',
        icon: <HeartIcon className="h-5 w-5" />,
        color: 'bg-critical-soft text-critical-ink',
    },
    {
        label: 'Lab Tech',
        roleKey: 'lab_tech',
        icon: <BeakerIcon className="h-5 w-5" />,
        color: 'bg-accent-soft text-accent-ink',
    },
    {
        label: 'Pharmacist',
        roleKey: 'pharmacist',
        icon: <BeakerIcon className="h-5 w-5" />,
        color: 'bg-warning-soft text-warning-ink',
    },
];

export default function Features() {
    const { t } = useTranslation();

    return (
        <PublicLayout>
            {/* Hero */}
            <section className="pt-32 pb-20 lg:pt-44">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.features.hero_title')}
                        subtitle={t('page.features.hero_subtitle')}
                    />
                </div>
            </section>

            {/* Core Modules */}
            <section className="bg-surface-muted py-24">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.features.modules_title')}
                        subtitle={t('page.features.modules_subtitle')}
                    />
                    <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        {CORE_MODULES.map((mod) => (
                            <FeatureCard
                                key={mod.titleKey}
                                icon={mod.icon}
                                title={t(mod.titleKey)}
                                description={t(mod.descriptionKey)}
                                color={mod.color}
                            />
                        ))}
                    </div>
                </div>
            </section>

            {/* Additional Services */}
            <section className="py-24">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.home.services_title')}
                        subtitle={t('page.home.services_subtitle')}
                    />
                    <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        {SERVICES.map((service) => (
                            <FeatureCard
                                key={service.titleKey}
                                icon={service.icon}
                                title={t(service.titleKey)}
                                description={t(service.descriptionKey)}
                                color={service.color}
                            />
                        ))}
                    </div>
                </div>
            </section>

            {/* Role-Based Dashboards */}
            <section className="bg-surface-muted py-24">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.features.roles_title')}
                        subtitle={t('page.features.roles_subtitle')}
                    />
                    <div className="grid gap-6 md:grid-cols-5">
                        {ROLE_DETAILS.map((role) => (
                            <div
                                key={role.label}
                                className="group rounded-3xl border border-line bg-surface p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-float"
                            >
                                <div
                                    className={`h-12 w-12 ${role.color} mb-5 flex items-center justify-center rounded-2xl transition duration-300 group-hover:scale-110`}
                                >
                                    {role.icon}
                                </div>
                                <h3 className="mb-4 text-lg font-bold text-ink">
                                    {role.label}
                                </h3>
                                <ul className="space-y-2 text-left">
                                    {[1, 2, 3, 4].map((i) => (
                                        <li
                                            key={i}
                                            className="flex items-center gap-2 text-xs text-ink-muted"
                                        >
                                            <div className="h-1 w-1 shrink-0 rounded-full bg-accent" />
                                            {t(
                                                `marketing.roles.${role.roleKey}.tasks_${i}`,
                                            )}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="px-6 py-20">
                <div className="relative mx-auto max-w-5xl overflow-hidden rounded-2xl bg-ink p-12 text-center shadow-float lg:p-24">
                    <div className="relative z-10">
                        <h2 className="mb-6 text-4xl font-bold text-ink-inverse lg:text-5xl">
                            {t('page.features.cta_title')}
                        </h2>
                        <p className="mx-auto mb-10 max-w-sm text-ink-subtle">
                            {t('page.features.cta_description')}
                        </p>
                        <Button href="/contact" size="xl">
                            {t('page.features.cta_button')}
                        </Button>
                    </div>
                    <div className="bg-accent-soft0/20 absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-accent/20 blur-3xl" />
                </div>
            </section>
        </PublicLayout>
    );
}
