import { HeartPulse, ShieldCheck, Users, Globe } from 'lucide-react';
import Button from '@/Components/marketing/Button';
import SectionHeading from '@/Components/marketing/SectionHeading';
import StatDisplay from '@/Components/marketing/StatDisplay';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

const VALUES = [
    {
        icon: <HeartPulse size={24} />,
        color: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600',
        titleKey: 'page.about.values_patient_title',
        descriptionKey: 'page.about.values_patient_description',
    },
    {
        icon: <ShieldCheck size={24} />,
        color: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600',
        titleKey: 'page.about.values_security_title',
        descriptionKey: 'page.about.values_security_description',
    },
    {
        icon: <Users size={24} />,
        color: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600',
        titleKey: 'page.about.values_clinician_title',
        descriptionKey: 'page.about.values_clinician_description',
    },
    {
        icon: <Globe size={24} />,
        color: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600',
        titleKey: 'page.about.values_accessible_title',
        descriptionKey: 'page.about.values_accessible_description',
    },
];

export default function About() {
    const { t } = useTranslation();

    return (
        <PublicLayout>
            {/* Hero */}
            <section className="pt-32 pb-20 lg:pt-44">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.about.hero_title')}
                        subtitle={t('page.about.hero_subtitle')}
                    />
                </div>
            </section>

            {/* Story */}
            <section className="bg-slate-50 py-24 dark:bg-neutral-800">
                <div className="mx-auto grid max-w-4xl items-center gap-16 px-6 lg:grid-cols-2">
                    <div>
                        <p className="mb-4 text-xs font-bold tracking-[0.2em] text-blue-600 uppercase">
                            {t('marketing.services.support.title')}
                        </p>
                        <h2 className="mb-6 text-3xl font-bold text-slate-900 lg:text-4xl dark:text-neutral-100">
                            {t('page.about.story_title')}
                        </h2>
                        <p className="mb-4 leading-relaxed text-slate-500 dark:text-neutral-400">
                            {t('page.about.story_p1')}
                        </p>
                        <p className="leading-relaxed text-slate-500 dark:text-neutral-400">
                            {t('page.about.story_p2')}
                        </p>
                    </div>

                    <div className="grid grid-cols-2 gap-6">
                        <StatDisplay
                            value="6+"
                            label={t('page.about.stats_modules')}
                        />
                        <StatDisplay
                            value="5"
                            label={t('page.about.stats_roles')}
                        />
                        <StatDisplay
                            value="24/7"
                            label={t('marketing.stats.patient_centered')}
                        />
                        <StatDisplay
                            value="HIPAA"
                            label={t('page.about.stats_access')}
                        />
                    </div>
                </div>
            </section>

            {/* Values */}
            <section className="py-24">
                <div className="mx-auto max-w-7xl px-6">
                    <SectionHeading
                        title={t('page.about.values_title')}
                        subtitle={t('page.about.values_subtitle')}
                    />

                    <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                        {VALUES.map((value) => (
                            <div
                                key={value.titleKey}
                                className="group rounded-3xl border border-slate-100 bg-white p-8 text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div
                                    className={`h-14 w-14 ${value.color} mx-auto mb-5 flex items-center justify-center rounded-2xl transition duration-300 group-hover:scale-110`}
                                >
                                    {value.icon}
                                </div>
                                <h3 className="mb-3 text-lg font-bold text-slate-900 dark:text-neutral-100">
                                    {t(value.titleKey)}
                                </h3>
                                <p className="text-sm leading-relaxed text-slate-500 dark:text-neutral-400">
                                    {t(value.descriptionKey)}
                                </p>
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
                            {t('page.about.cta_title')}
                        </h2>
                        <p className="mx-auto mb-10 max-w-sm text-slate-300">
                            {t('page.about.cta_description')}
                        </p>
                        <Button href="/contact" size="xl">
                            {t('page.about.cta_button')}
                        </Button>
                    </div>
                    <div className="absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl" />
                </div>
            </section>
        </PublicLayout>
    );
}
