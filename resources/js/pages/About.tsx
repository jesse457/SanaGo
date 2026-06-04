import { HeartPulse, ShieldCheck, Users, Globe } from 'lucide-react';
import Button from '@/Components/marketing/Button';
import SectionHeading from '@/Components/marketing/SectionHeading';
import StatDisplay from '@/Components/marketing/StatDisplay';
import PublicLayout from '@/Layouts/PublicLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
      <section className="pt-32 lg:pt-44 pb-20">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.about.hero_title')}
            subtitle={t('page.about.hero_subtitle')}
          />
        </div>
      </section>

      {/* Story */}
      <section className="py-24 bg-slate-50 dark:bg-neutral-800">
        <div className="max-w-4xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <p className="text-xs font-bold tracking-[0.2em] text-blue-600 uppercase mb-4">
              {t('marketing.services.support.title')}
            </p>
            <h2 className="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-neutral-100 mb-6">
              {t('page.about.story_title')}
            </h2>
            <p className="text-slate-500 dark:text-neutral-400 leading-relaxed mb-4">
              {t('page.about.story_p1')}
            </p>
            <p className="text-slate-500 dark:text-neutral-400 leading-relaxed">
              {t('page.about.story_p2')}
            </p>
          </div>

          <div className="grid grid-cols-2 gap-6">
            <StatDisplay value="6+" label={t('page.about.stats_modules')} />
            <StatDisplay value="5" label={t('page.about.stats_roles')} />
            <StatDisplay value="24/7" label={t('marketing.stats.patient_centered')} />
            <StatDisplay value="HIPAA" label={t('page.about.stats_access')} />
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="py-24">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.about.values_title')}
            subtitle={t('page.about.values_subtitle')}
          />

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {VALUES.map((value) => (
              <div
                key={value.titleKey}
                className="bg-white dark:bg-neutral-900 p-8 rounded-3xl border border-slate-100 dark:border-neutral-700 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group text-center"
              >
                <div className={`w-14 h-14 ${value.color} rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition duration-300`}>
                  {value.icon}
                </div>
                <h3 className="font-bold text-lg text-slate-900 dark:text-neutral-100 mb-3">
                  {t(value.titleKey)}
                </h3>
                <p className="text-sm text-slate-500 dark:text-neutral-400 leading-relaxed">
                  {t(value.descriptionKey)}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 px-6">
        <div className="max-w-5xl mx-auto bg-gradient-to-br from-slate-900 to-slate-800 dark:from-neutral-800 dark:to-neutral-900 rounded-[50px] p-12 lg:p-24 text-center relative overflow-hidden shadow-2xl">
          <div className="relative z-10">
            <h2 className="text-4xl lg:text-5xl font-bold text-white mb-6">
              {t('page.about.cta_title')}
            </h2>
            <p className="text-slate-300 mb-10 max-w-sm mx-auto">
              {t('page.about.cta_description')}
            </p>
            <Button href="/contact" size="xl">
              {t('page.about.cta_button')}
            </Button>
          </div>
          <div className="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32" />
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-32 -mb-32" />
        </div>
      </section>
    </PublicLayout>
  );
}
