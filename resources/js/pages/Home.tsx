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
import Button from '@/Components/marketing/Button';
import FeatureCard from '@/Components/marketing/FeatureCard';
import SectionHeading from '@/Components/marketing/SectionHeading';
import StatDisplay from '@/Components/marketing/StatDisplay';
import PublicLayout from '@/Layouts/PublicLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';

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
  { roleKey: 'marketing.roles.admin.description', icon: <Users size={24} />, color: 'text-blue-600', label: 'Admin' },
  { roleKey: 'marketing.roles.doctor.description', icon: <Stethoscope size={24} />, color: 'text-emerald-600', label: 'Doctor' },
  { roleKey: 'marketing.roles.nurse.description', icon: <Heart size={24} />, color: 'text-rose-600', label: 'Nurse' },
  { roleKey: 'marketing.roles.lab_tech.description', icon: <Microscope size={24} />, color: 'text-purple-600', label: 'Lab Tech' },
  { roleKey: 'marketing.roles.pharmacist.description', icon: <Pill size={24} />, color: 'text-orange-600', label: 'Pharmacist' },
];

const SERVICES = [
  {
    icon: <ShieldCheck className="w-5 h-5 text-blue-600" />,
    color: 'bg-blue-100 dark:bg-blue-900/30',
    titleKey: 'marketing.services.security.title',
    descriptionKey: 'marketing.services.security.description',
  },
  {
    icon: <Heart className="w-5 h-5 text-emerald-600" />,
    color: 'bg-emerald-100 dark:bg-emerald-900/30',
    titleKey: 'marketing.services.consultation_surgery.title',
    descriptionKey: 'marketing.services.consultation_surgery.description',
  },
  {
    icon: <HeadphonesIcon className="w-5 h-5 text-purple-600" />,
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
      <section className="relative pt-32 lg:pt-44 pb-20 overflow-hidden scroll-mt-20">
        <div className="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl -mr-48 -mt-48" />
        <div className="absolute bottom-0 left-0 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl -ml-48 -mb-48" />
        <div className="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
          <div className="relative z-10">
            <p className="text-xs font-bold tracking-[0.2em] text-blue-600 uppercase mb-4 animate-pulse">
              {t('page.home.hero_badge')}
            </p>
            <h1 className="text-5xl lg:text-7xl font-extrabold leading-[1.1] text-slate-900 dark:text-neutral-100">
              {t('page.home.hero_title_line1')}{' '}
              <span className="text-blue-600 relative inline-block">
                {t('page.home.hero_title_highlight')}
                <svg
                  className="absolute -bottom-2 left-0 w-full h-3 text-blue-200 dark:text-blue-900/50 -z-10"
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
            <p className="mt-8 text-lg text-slate-500 dark:text-neutral-400 leading-relaxed max-w-lg">
              {t('page.home.hero_description')}
            </p>

            <div className="mt-10 flex flex-wrap gap-4 items-center">
              <Button href="/login" size="lg">
                {t('page.home.hero_get_started')}
              </Button>
              <Button href="/features" variant="secondary" size="lg">
                {t('page.home.hero_explore')}{' '}
                <ChevronRight size={18} className="group-hover:translate-x-1 transition" />
              </Button>
              <LocaleSwitcher variant="full" />
            </div>

            <div className="mt-12 grid grid-cols-3 gap-6">
              <StatDisplay value="6+" label={t('marketing.stats.core_modules')} />
              <StatDisplay value="5" label={t('marketing.stats.user_roles')} />
              <StatDisplay value="24/7" label={t('marketing.stats.patient_centered')} />
            </div>
          </div>

          <div className="relative hidden lg:block">
            <div className="rounded-[60px] overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-700">
              <img
                src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80"
                alt="Hospital dashboard on screen"
                className="w-full h-[550px] object-cover"
                loading="lazy"
              />
            </div>
            <div className="absolute -bottom-6 -left-10 bg-white dark:bg-neutral-800 p-5 rounded-3xl shadow-2xl flex items-center gap-4 border border-slate-50 dark:border-neutral-700 hover:scale-105 transition duration-300">
              <div className="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white">
                <ShieldCheck size={24} />
              </div>
              <div>
                <p className="font-bold text-slate-900 dark:text-neutral-100 leading-none">{t('marketing.services.security.title')}</p>
                <p className="text-xs text-slate-400 dark:text-neutral-500 mt-1">{t('marketing.stats.patient_centered')}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Modules */}
      <section className="py-24 bg-slate-50 dark:bg-neutral-800 scroll-mt-20">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.home.modules_title')}
            subtitle={t('page.home.modules_subtitle')}
          />

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
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
      <section className="py-24 scroll-mt-20">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.home.roles_title')}
            subtitle={t('page.home.roles_subtitle')}
          />

          <div className="grid md:grid-cols-5 gap-6 text-center">
            {ROLES.map((item) => (
              <div
                key={item.label}
                className="bg-white dark:bg-neutral-900 p-8 rounded-xl border border-slate-100 dark:border-neutral-700 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group"
              >
                <div className={`w-14 h-14 bg-slate-100 dark:bg-neutral-800 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300 ${item.color}`}>
                  {item.icon}
                </div>
                <h3 className="font-bold text-slate-900 dark:text-neutral-100 mb-2">{item.label}</h3>
                <p className="text-xs text-slate-400 dark:text-neutral-500 leading-relaxed">{t(item.roleKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Why Choose SanaGo */}
      <section className="py-24 bg-slate-50 dark:bg-neutral-800 scroll-mt-20">
        <div className="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <h2 className="text-4xl font-bold text-slate-900 dark:text-neutral-100 mb-6">
              {t('page.home.services_title')}
            </h2>
            <div className="space-y-6">
              {SERVICES.map((service) => (
                <div key={service.titleKey} className="flex gap-4 group">
                  <div className={`w-12 h-12 ${service.color} rounded-lg flex items-center justify-center shrink-0 group-hover:scale-110 transition`}>
                    {service.icon}
                  </div>
                  <div>
                    <h3 className="font-bold text-slate-900 dark:text-neutral-100">{t(service.titleKey)}</h3>
                    <p className="text-sm text-slate-500 dark:text-neutral-400 mt-1">
                      {t(service.descriptionKey)}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-neutral-800 dark:to-neutral-900 rounded-[20px] p-10 lg:p-14 text-white relative overflow-hidden shadow-2xl">
            <div className="relative z-10 space-y-6">
              <p className="text-blue-400 font-bold text-xs uppercase tracking-[0.2em]">{t('page.home.services_title')}</p>
              <h3 className="text-3xl font-bold">{t('page.home.modules_title')}</h3>
              <p className="text-slate-300 leading-relaxed">
                {t('page.home.services_subtitle')}
              </p>
              <div className="space-y-3 pt-4">
                {[
                  t('marketing.services.security.title'),
                  t('marketing.services.consultation_surgery.title'),
                  t('marketing.services.emergency.title'),
                ].map((item) => (
                  <div key={item} className="flex items-center gap-3 text-sm text-slate-300">
                    <div className="w-1.5 h-1.5 bg-blue-500 rounded-full shrink-0" />
                    {item}
                  </div>
                ))}
              </div>
            </div>
            <div className="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32" />
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 px-6">
        <div className="max-w-5xl mx-auto bg-gradient-to-br from-slate-900 to-slate-800 dark:from-neutral-800 dark:to-neutral-900 rounded-[50px] p-12 lg:p-24 text-center relative overflow-hidden shadow-2xl">
          <div className="relative z-10">
            <h2 className="text-4xl lg:text-5xl font-bold text-white mb-6">
              {t('page.home.cta_title')}
            </h2>
            <p className="text-slate-300 mb-10 max-w-sm mx-auto">
              {t('page.home.cta_description')}
            </p>
            <Button href="/login" size="xl">
              {t('page.home.cta_button')}
            </Button>
          </div>
          <div className="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32" />
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-32 -mb-32" />
        </div>
      </section>
    </PublicLayout>
  );
}
