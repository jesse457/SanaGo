import {
    CalendarCheck,
    FlaskConical,
    Pill,
    ChartNoAxesColumn,
    Users,
    Building2,
    ChevronRight,
    Stethoscope,
    Heart,
    Microscope,
    ShieldCheck,
    HeadphonesIcon,
} from 'lucide-react';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import Button from '@/Components/marketing/Button';
import FeatureCard from '@/Components/marketing/FeatureCard';
import SectionHeading from '@/Components/marketing/SectionHeading';
import StatDisplay from '@/Components/marketing/StatDisplay';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

const MODULES = [
    {
        icon: <Users />,
        titleKey: 'marketing.modules.patient_management.title',
        descriptionKey: 'marketing.modules.patient_management.description',
        color: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600',
    },
    {
        icon: <CalendarCheck />,
        titleKey: 'marketing.modules.appointments.title',
        descriptionKey: 'marketing.modules.appointments.description',
        color: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600',
    },
    {
        icon: <FlaskConical />,
        titleKey: 'marketing.modules.lab.title',
        descriptionKey: 'marketing.modules.lab.description',
        color: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600',
    },
    {
        icon: <Pill />,
        titleKey: 'marketing.modules.pharmacy.title',
        descriptionKey: 'marketing.modules.pharmacy.description',
        color: 'bg-orange-50 dark:bg-orange-900/20 text-orange-600',
    },
    {
        icon: <ChartNoAxesColumn />,
        titleKey: 'marketing.modules.billing.title',
        descriptionKey: 'marketing.modules.billing.description',
        color: 'bg-rose-50 dark:bg-rose-900/20 text-rose-600',
    },
    {
        icon: <Building2 />,
        titleKey: 'marketing.modules.infrastructure.title',
        descriptionKey: 'marketing.modules.infrastructure.description',
        color: 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600',
    },
];

const ROLES = [
    {
        roleKey: 'marketing.roles.admin.description',
        icon: <Users size={24} />,
        color: 'text-blue-600',
        label: 'Admin',
    },
    {
        roleKey: 'marketing.roles.doctor.description',
        icon: <Stethoscope size={24} />,
        color: 'text-emerald-600',
        label: 'Doctor',
    },
    {
        roleKey: 'marketing.roles.nurse.description',
        icon: <Heart size={24} />,
        color: 'text-rose-600',
        label: 'Nurse',
    },
    {
        roleKey: 'marketing.roles.lab_tech.description',
        icon: <Microscope size={24} />,
        color: 'text-purple-600',
        label: 'Lab Tech',
    },
    {
        roleKey: 'marketing.roles.pharmacist.description',
        icon: <Pill size={24} />,
        color: 'text-orange-600',
        label: 'Pharmacist',
    },
];

const SERVICES = [
    {
        icon: <ShieldCheck className="h-5 w-5 text-blue-600" />,
        color: 'bg-blue-100 dark:bg-blue-900/30',
        titleKey: 'marketing.services.security.title',
        descriptionKey: 'marketing.services.security.description',
    },
    {
        icon: <Heart className="h-5 w-5 text-emerald-600" />,
        color: 'bg-emerald-100 dark:bg-emerald-900/30',
        titleKey: 'marketing.services.consultation_surgery.title',
        descriptionKey: 'marketing.services.consultation_surgery.description',
    },
    {
        icon: <HeadphonesIcon className="h-5 w-5 text-purple-600" />,
        color: 'bg-purple-100 dark:bg-purple-900/30',
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
                <div className="absolute top-0 right-0 -mt-48 -mr-48 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl" />
                <div className="absolute bottom-0 left-0 -mb-48 -ml-48 h-96 w-96 rounded-full bg-purple-500/10 blur-3xl" />
                <div className="relative z-10 mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2">
                    <div className="relative z-10">
                        <p className="mb-4 animate-pulse text-xs font-bold tracking-[0.2em] text-blue-600 uppercase">
                            {t('page.home.hero_badge')}
                        </p>
                        <h1 className="text-5xl leading-[1.1] font-extrabold text-slate-900 lg:text-7xl dark:text-neutral-100">
                            {t('page.home.hero_title_line1')}{' '}
                            <span className="relative inline-block text-blue-600">
                                {t('page.home.hero_title_highlight')}
                                <svg
                                    className="absolute -bottom-2 left-0 -z-10 h-3 w-full text-blue-200 dark:text-blue-900/50"
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
                        <p className="mt-8 max-w-lg text-lg leading-relaxed text-slate-500 dark:text-neutral-400">
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
                                <ChevronRight
                                    size={18}
                                    className="transition group-hover:translate-x-1"
                                />
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
                        <div className="rotate-2 overflow-hidden rounded-[60px] shadow-2xl transition-transform duration-700 hover:rotate-0">
                            <img
                                src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80"
                                alt="Hospital dashboard on screen"
                                className="h-[550px] w-full object-cover"
                                loading="lazy"
                            />
                        </div>
                        <div className="absolute -bottom-6 -left-10 flex items-center gap-4 rounded-3xl border border-slate-50 bg-white p-5 shadow-2xl transition duration-300 hover:scale-105 dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500 text-white">
                                <ShieldCheck size={24} />
                            </div>
                            <div>
                                <p className="leading-none font-bold text-slate-900 dark:text-neutral-100">
                                    {t('marketing.services.security.title')}
                                </p>
                                <p className="mt-1 text-xs text-slate-400 dark:text-neutral-500">
                                    {t('marketing.stats.patient_centered')}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Modules */}
            <section className="scroll-mt-20 bg-slate-50 py-24 dark:bg-neutral-800">
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
                                className="group rounded-xl border border-slate-100 bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div
                                    className={`mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 transition duration-300 group-hover:scale-110 dark:bg-neutral-800 ${item.color}`}
                                >
                                    {item.icon}
                                </div>
                                <h3 className="mb-2 font-bold text-slate-900 dark:text-neutral-100">
                                    {item.label}
                                </h3>
                                <p className="text-xs leading-relaxed text-slate-400 dark:text-neutral-500">
                                    {t(item.roleKey)}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Why Choose SanaGo */}
            <section className="scroll-mt-20 bg-slate-50 py-24 dark:bg-neutral-800">
                <div className="mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2">
                    <div>
                        <h2 className="mb-6 text-4xl font-bold text-slate-900 dark:text-neutral-100">
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
                                        <h3 className="font-bold text-slate-900 dark:text-neutral-100">
                                            {t(service.titleKey)}
                                        </h3>
                                        <p className="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                            {t(service.descriptionKey)}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="relative overflow-hidden rounded-[20px] bg-gradient-to-br from-slate-900 to-slate-800 p-10 text-white shadow-2xl lg:p-14 dark:from-neutral-800 dark:to-neutral-900">
                        <div className="relative z-10 space-y-6">
                            <p className="text-xs font-bold tracking-[0.2em] text-blue-400 uppercase">
                                {t('page.home.services_title')}
                            </p>
                            <h3 className="text-3xl font-bold">
                                {t('page.home.modules_title')}
                            </h3>
                            <p className="leading-relaxed text-slate-300">
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
                                        className="flex items-center gap-3 text-sm text-slate-300"
                                    >
                                        <div className="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500" />
                                        {item}
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl" />
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="px-6 py-20">
                <div className="relative mx-auto max-w-5xl overflow-hidden rounded-[50px] bg-gradient-to-br from-slate-900 to-slate-800 p-12 text-center shadow-2xl lg:p-24 dark:from-neutral-800 dark:to-neutral-900">
                    <div className="relative z-10">
                        <h2 className="mb-6 text-4xl font-bold text-white lg:text-5xl">
                            {t('page.home.cta_title')}
                        </h2>
                        <p className="mx-auto mb-10 max-w-sm text-slate-300">
                            {t('page.home.cta_description')}
                        </p>
                        <Button href="/login" size="xl">
                            {t('page.home.cta_button')}
                        </Button>
                    </div>
                    <div className="absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl" />
                </div>
            </section>
        </PublicLayout>
    );
}
