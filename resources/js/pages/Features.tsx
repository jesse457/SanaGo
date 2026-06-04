import {
    CalendarCheck,
    FlaskConical,
    Pill,
    ChartNoAxesColumn,
    Users,
    Building2,
    ShieldCheck,
    Stethoscope,
    Heart,
    Microscope,
    BedDouble,
    HeadphonesIcon,
} from 'lucide-react';
import Button from '@/Components/marketing/Button';
import FeatureCard from '@/Components/marketing/FeatureCard';
import SectionHeading from '@/Components/marketing/SectionHeading';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

const CORE_MODULES = [
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

const SERVICES = [
    {
        icon: <ShieldCheck />,
        titleKey: 'marketing.services.security.title',
        descriptionKey: 'marketing.services.security.description',
        color: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600',
    },
    {
        icon: <BedDouble />,
        titleKey: 'marketing.services.bed_ward.title',
        descriptionKey: 'marketing.services.bed_ward.description',
        color: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600',
    },
    {
        icon: <Stethoscope />,
        titleKey: 'marketing.services.consultation_surgery.title',
        descriptionKey: 'marketing.services.consultation_surgery.description',
        color: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600',
    },
    {
        icon: <Heart />,
        titleKey: 'marketing.services.emergency.title',
        descriptionKey: 'marketing.services.emergency.description',
        color: 'bg-orange-50 dark:bg-orange-900/20 text-orange-600',
    },
    {
        icon: <HeadphonesIcon />,
        titleKey: 'marketing.services.support.title',
        descriptionKey: 'marketing.services.support.description',
        color: 'bg-rose-50 dark:bg-rose-900/20 text-rose-600',
    },
];

const ROLE_DETAILS = [
    {
        label: 'Admin',
        roleKey: 'admin',
        icon: <Users size={20} />,
        color: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600',
    },
    {
        label: 'Doctor',
        roleKey: 'doctor',
        icon: <Stethoscope size={20} />,
        color: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600',
    },
    {
        label: 'Nurse',
        roleKey: 'nurse',
        icon: <Heart size={20} />,
        color: 'bg-rose-100 dark:bg-rose-900/30 text-rose-600',
    },
    {
        label: 'Lab Tech',
        roleKey: 'lab_tech',
        icon: <Microscope size={20} />,
        color: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600',
    },
    {
        label: 'Pharmacist',
        roleKey: 'pharmacist',
        icon: <Pill size={20} />,
        color: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600',
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
            <section className="bg-slate-50 py-24 dark:bg-neutral-800">
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
            <section className="bg-slate-50 py-24 dark:bg-neutral-800">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.features.roles_title')}
                        subtitle={t('page.features.roles_subtitle')}
                    />
                    <div className="grid gap-6 md:grid-cols-5">
                        {ROLE_DETAILS.map((role) => (
                            <div
                                key={role.label}
                                className="group rounded-3xl border border-slate-100 bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div
                                    className={`h-12 w-12 ${role.color} mb-5 flex items-center justify-center rounded-2xl transition duration-300 group-hover:scale-110`}
                                >
                                    {role.icon}
                                </div>
                                <h3 className="mb-4 text-lg font-bold text-slate-900 dark:text-neutral-100">
                                    {role.label}
                                </h3>
                                <ul className="space-y-2 text-left">
                                    {[1, 2, 3, 4].map((i) => (
                                        <li
                                            key={i}
                                            className="flex items-center gap-2 text-xs text-slate-500 dark:text-neutral-400"
                                        >
                                            <div className="h-1 w-1 shrink-0 rounded-full bg-blue-600" />
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
                <div className="relative mx-auto max-w-5xl overflow-hidden rounded-[50px] bg-gradient-to-br from-slate-900 to-slate-800 p-12 text-center shadow-2xl lg:p-24 dark:from-neutral-800 dark:to-neutral-900">
                    <div className="relative z-10">
                        <h2 className="mb-6 text-4xl font-bold text-white lg:text-5xl">
                            {t('page.features.cta_title')}
                        </h2>
                        <p className="mx-auto mb-10 max-w-sm text-slate-300">
                            {t('page.features.cta_description')}
                        </p>
                        <Button href="/contact" size="xl">
                            {t('page.features.cta_button')}
                        </Button>
                    </div>
                    <div className="absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl" />
                </div>
            </section>
        </PublicLayout>
    );
}
