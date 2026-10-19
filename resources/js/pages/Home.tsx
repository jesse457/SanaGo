import {
    BeakerIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    ChartBarIcon,
    ChevronRightIcon,
    HeartIcon,
    UserIcon,
    LifebuoyIcon,
    ShieldCheckIcon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import Button from '@/Components/marketing/Button';
import FeatureCard from '@/Components/marketing/FeatureCard';
import SectionHeading from '@/Components/marketing/SectionHeading';
import StatDisplay from '@/Components/marketing/StatDisplay';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

const MODULES = [
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

const ROLES = [
    {
        roleKey: 'marketing.roles.admin.description',
        icon: <UsersIcon className="h-6 w-6" />,
        color: 'text-accent-ink',
        label: 'Admin',
    },
    {
        roleKey: 'marketing.roles.doctor.description',
        icon: <UserIcon className="h-6 w-6" />,
        color: 'text-positive-ink',
        label: 'Doctor',
    },
    {
        roleKey: 'marketing.roles.nurse.description',
        icon: <HeartIcon className="h-6 w-6" />,
        color: 'text-critical-ink',
        label: 'Nurse',
    },
    {
        roleKey: 'marketing.roles.lab_tech.description',
        icon: <BeakerIcon className="h-6 w-6" />,
        color: 'text-accent-ink',
        label: 'Lab Tech',
    },
    {
        roleKey: 'marketing.roles.pharmacist.description',
        icon: <BeakerIcon className="h-6 w-6" />,
        color: 'text-warning-ink',
        label: 'Pharmacist',
    },
];

const SERVICES = [
    {
        icon: <ShieldCheckIcon className="h-5 w-5 text-accent-ink" />,
        color: 'bg-accent-soft ',
        titleKey: 'marketing.services.security.title',
        descriptionKey: 'marketing.services.security.description',
    },
    {
        icon: <HeartIcon className="h-5 w-5 text-positive-ink" />,
        color: 'bg-positive-soft ',
        titleKey: 'marketing.services.consultation_surgery.title',
        descriptionKey: 'marketing.services.consultation_surgery.description',
    },
    {
        icon: <LifebuoyIcon className="h-5 w-5 text-accent-ink" />,
        color: 'bg-accent-soft ',
        titleKey: 'marketing.services.support.title',
        descriptionKey: 'marketing.services.support.description',
    },
];

export default function Home() {
    const { t } = useTranslation();

    return (
        <PublicLayout>
            {/* Hero */}
            <section className="relative scroll-mt-20 overflow-hidden pt-32 pb-20 lg:pt-44">
                <div className="bg-accent-soft0/10 absolute top-0 right-0 -mt-48 -mr-48 h-96 w-96 rounded-full blur-3xl" />
                <div className="absolute bottom-0 left-0 -mb-48 -ml-48 h-96 w-96 rounded-full bg-accent/10 blur-3xl" />
                <div className="relative z-10 mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2">
                    <div className="relative z-10">
                        <p className="mb-4 animate-pulse text-xs font-bold tracking-[0.2em] text-accent-ink uppercase">
                            {t('page.home.hero_badge')}
                        </p>
                        <h1 className="text-5xl leading-[1.1] font-extrabold text-ink lg:text-7xl">
                            {t('page.home.hero_title_line1')}{' '}
                            <span className="relative inline-block text-accent-ink">
                                {t('page.home.hero_title_highlight')}
                                <svg
                                    className="absolute -bottom-2 left-0 -z-10 h-3 w-full text-accent/20"
                                    viewBox="0 0 200 10"
                                    preserveAspectRatio="none"
                                >
                                    <path
                                        d="M0 5 Q 50 10 100 5 T 200 5"
                                        stroke="currentColor"
                                        strokeWidth="4"
                                        fill="none"
                                    />
                                </svg>
                            </span>{' '}
                            <br />
                            {t('page.home.hero_title_line2')}
                        </h1>
                        <p className="mt-8 max-w-lg text-lg leading-relaxed text-ink-muted">
                            {t('page.home.hero_description')}
                        </p>

                        <div className="mt-10 flex flex-wrap items-center gap-4">
                            <Button href="/login" size="lg">
                                {t('page.home.hero_get_started')}
                            </Button>
                            <Button
                                href="/features"
                                variant="secondary"
                                size="lg"
                            >
                                {t('page.home.hero_explore')}{' '}
                                <ChevronRightIcon className="h-[18px] w-[18px] transition group-hover:translate-x-1" />
                            </Button>
                            <LocaleSwitcher variant="full" />
                        </div>

                        <div className="mt-12 grid grid-cols-3 gap-6">
                            <StatDisplay
                                value="6+"
                                label={t('marketing.stats.core_modules')}
                            />
                            <StatDisplay
                                value="5"
                                label={t('marketing.stats.user_roles')}
                            />
                            <StatDisplay
                                value="24/7"
                                label={t('marketing.stats.patient_centered')}
                            />
                        </div>
                    </div>

                    <div className="relative hidden lg:block">
                        <div className="rotate-2 overflow-hidden rounded-[60px] shadow-float transition-transform duration-700 hover:rotate-0">
                            <img
                                src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80"
                                alt="Hospital dashboard on screen"
                                className="h-[550px] w-full object-cover"
                                loading="lazy"
                            />
                        </div>
                        <div className="absolute -bottom-6 -left-10 flex items-center gap-4 rounded-3xl border border-line bg-surface p-5 shadow-float transition duration-300 hover:scale-105">
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-positive text-ink-inverse">
                                <ShieldCheckIcon className="h-6 w-6" />
                            </div>
                            <div>
                                <p className="leading-none font-bold text-ink">
                                    {t('marketing.services.security.title')}
                                </p>
                                <p className="mt-1 text-xs text-ink-subtle">
                                    {t('marketing.stats.patient_centered')}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Modules */}
            <section className="scroll-mt-20 bg-surface-muted py-24">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.home.modules_title')}
                        subtitle={t('page.home.modules_subtitle')}
                    />

                    <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        {MODULES.map((mod) => (
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

            {/* Role Dashboards */}
            <section className="scroll-mt-20 py-24">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.home.roles_title')}
                        subtitle={t('page.home.roles_subtitle')}
                    />

                    <div className="grid gap-6 text-center md:grid-cols-5">
                        {ROLES.map((item) => (
                            <div
                                key={item.label}
                                className="group rounded-xl border border-line bg-surface p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-float"
                            >
                                <div
                                    className={`mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-surface transition duration-300 group-hover:scale-110 ${item.color}`}
                                >
                                    {item.icon}
                                </div>
                                <h3 className="mb-2 font-bold text-ink">
                                    {item.label}
                                </h3>
                                <p className="text-xs leading-relaxed text-ink-subtle">
                                    {t(item.roleKey)}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Why Choose SanaGo */}
            <section className="scroll-mt-20 bg-surface-muted py-24">
                <div className="mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2">
                    <div>
                        <h2 className="mb-6 text-4xl font-bold text-ink">
                            {t('page.home.services_title')}
                        </h2>
                        <div className="space-y-6">
                            {SERVICES.map((service) => (
                                <div
                                    key={service.titleKey}
                                    className="group flex gap-4"
                                >
                                    <div
                                        className={`h-12 w-12 ${service.color} flex shrink-0 items-center justify-center rounded-lg transition group-hover:scale-110`}
                                    >
                                        {service.icon}
                                    </div>
                                    <div>
                                        <h3 className="font-bold text-ink">
                                            {t(service.titleKey)}
                                        </h3>
                                        <p className="mt-1 text-sm text-ink-muted">
                                            {t(service.descriptionKey)}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="relative overflow-hidden rounded-[20px] bg-ink p-10 text-ink-inverse shadow-float lg:p-14">
                        <div className="relative z-10 space-y-6">
                            <p className="text-xs font-bold tracking-[0.2em] text-accent uppercase">
                                {t('page.home.services_title')}
                            </p>
                            <h3 className="text-3xl font-bold">
                                {t('page.home.modules_title')}
                            </h3>
                            <p className="leading-relaxed text-ink-subtle">
                                {t('page.home.services_subtitle')}
                            </p>
                            <div className="space-y-3 pt-4">
                                {[
                                    t('marketing.services.security.title'),
                                    t(
                                        'marketing.services.consultation_surgery.title',
                                    ),
                                    t('marketing.services.emergency.title'),
                                ].map((item) => (
                                    <div
                                        key={item}
                                        className="flex items-center gap-3 text-sm text-ink-subtle"
                                    >
                                        <div className="bg-accent-soft0 h-1.5 w-1.5 shrink-0 rounded-full" />
                                        {item}
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="bg-accent-soft0/20 absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full blur-3xl" />
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="px-6 py-20">
                <div className="relative mx-auto max-w-5xl overflow-hidden rounded-2xl bg-ink p-12 text-center shadow-float lg:p-24">
                    <div className="relative z-10">
                        <h2 className="mb-6 text-4xl font-bold text-ink-inverse lg:text-5xl">
                            {t('page.home.cta_title')}
                        </h2>
                        <p className="mx-auto mb-10 max-w-sm text-ink-subtle">
                            {t('page.home.cta_description')}
                        </p>
                        <Button href="/login" size="xl">
                            {t('page.home.cta_button')}
                        </Button>
                    </div>
                    <div className="bg-accent-soft0/20 absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-accent/20 blur-3xl" />
                </div>
            </section>
        </PublicLayout>
    );
}
