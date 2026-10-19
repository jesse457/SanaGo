import {
    GlobeAltIcon,
    HeartIcon,
    ShieldCheckIcon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import Button from '@/Components/marketing/Button';
import SectionHeading from '@/Components/marketing/SectionHeading';
import StatDisplay from '@/Components/marketing/StatDisplay';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

const VALUES = [
    {
        icon: <HeartIcon className="h-6 w-6" />,
        color: 'bg-accent-soft text-accent-ink',
        titleKey: 'page.about.values_patient_title',
        descriptionKey: 'page.about.values_patient_description',
    },
    {
        icon: <ShieldCheckIcon className="h-6 w-6" />,
        color: 'bg-positive-soft text-positive-ink',
        titleKey: 'page.about.values_security_title',
        descriptionKey: 'page.about.values_security_description',
    },
    {
        icon: <UsersIcon className="h-6 w-6" />,
        color: 'bg-accent-soft text-accent-ink',
        titleKey: 'page.about.values_clinician_title',
        descriptionKey: 'page.about.values_clinician_description',
    },
    {
        icon: <GlobeAltIcon className="h-6 w-6" />,
        color: 'bg-warning-soft text-warning-ink',
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
            <section className="bg-surface-muted py-24">
                <div className="mx-auto grid max-w-4xl items-center gap-16 px-6 lg:grid-cols-2">
                    <div>
                        <p className="mb-4 text-xs font-bold tracking-[0.2em] text-accent-ink uppercase">
                            {t('marketing.services.support.title')}
                        </p>
                        <h2 className="mb-6 text-3xl font-bold text-ink lg:text-4xl">
                            {t('page.about.story_title')}
                        </h2>
                        <p className="mb-4 leading-relaxed text-ink-muted">
                            {t('page.about.story_p1')}
                        </p>
                        <p className="leading-relaxed text-ink-muted">
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
                                className="group rounded-3xl border border-line bg-surface p-8 text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-float"
                            >
                                <div
                                    className={`h-14 w-14 ${value.color} mx-auto mb-5 flex items-center justify-center rounded-2xl transition duration-300 group-hover:scale-110`}
                                >
                                    {value.icon}
                                </div>
                                <h3 className="mb-3 text-lg font-bold text-ink">
                                    {t(value.titleKey)}
                                </h3>
                                <p className="text-sm leading-relaxed text-ink-muted">
                                    {t(value.descriptionKey)}
                                </p>
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
                            {t('page.about.cta_title')}
                        </h2>
                        <p className="mx-auto mb-10 max-w-sm text-ink-subtle">
                            {t('page.about.cta_description')}
                        </p>
                        <Button href="/contact" size="xl">
                            {t('page.about.cta_button')}
                        </Button>
                    </div>
                    <div className="bg-accent-soft0/20 absolute top-0 right-0 -mt-32 -mr-32 h-64 w-64 rounded-full blur-3xl" />
                    <div className="absolute bottom-0 left-0 -mb-32 -ml-32 h-64 w-64 rounded-full bg-accent/20 blur-3xl" />
                </div>
            </section>
        </PublicLayout>
    );
}
