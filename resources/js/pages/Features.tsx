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
import PublicLayout from '@/Layouts/PublicLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
  { label: 'Admin', roleKey: 'admin', icon: <Users size={20} />, color: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600' },
  { label: 'Doctor', roleKey: 'doctor', icon: <Stethoscope size={20} />, color: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' },
  { label: 'Nurse', roleKey: 'nurse', icon: <Heart size={20} />, color: 'bg-rose-100 dark:bg-rose-900/30 text-rose-600' },
  { label: 'Lab Tech', roleKey: 'lab_tech', icon: <Microscope size={20} />, color: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600' },
  { label: 'Pharmacist', roleKey: 'pharmacist', icon: <Pill size={20} />, color: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600' },
];

export default function Features() {
  const { t } = useTranslation();

  return (
    <PublicLayout>
      {/* Hero */}
      <section className="pt-32 lg:pt-44 pb-20">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.features.hero_title')}
            subtitle={t('page.features.hero_subtitle')}
          />
        </div>
      </section>

      {/* Core Modules */}
      <section className="py-24 bg-slate-50 dark:bg-neutral-800">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.features.modules_title')}
            subtitle={t('page.features.modules_subtitle')}
          />
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
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
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.home.services_title')}
            subtitle={t('page.home.services_subtitle')}
          />
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
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
      <section className="py-24 bg-slate-50 dark:bg-neutral-800">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title={t('page.features.roles_title')}
            subtitle={t('page.features.roles_subtitle')}
          />
          <div className="grid md:grid-cols-5 gap-6">
            {ROLE_DETAILS.map((role) => (
              <div
                key={role.label}
                className="bg-white dark:bg-neutral-900 p-8 rounded-3xl border border-slate-100 dark:border-neutral-700 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group"
              >
                <div className={`w-12 h-12 ${role.color} rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300`}>
                  {role.icon}
                </div>
                <h3 className="font-bold text-lg text-slate-900 dark:text-neutral-100 mb-4">
                  {role.label}
                </h3>
                <ul className="space-y-2 text-left">
                  {[1, 2, 3, 4].map((i) => (
                    <li key={i} className="text-xs text-slate-500 dark:text-neutral-400 flex items-center gap-2">
                      <div className="w-1 h-1 bg-blue-600 rounded-full shrink-0" />
                      {t(`marketing.roles.${role.roleKey}.tasks_${i}`)}
                    </li>
                  ))}
                </ul>
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
              {t('page.features.cta_title')}
            </h2>
            <p className="text-slate-300 mb-10 max-w-sm mx-auto">
              {t('page.features.cta_description')}
            </p>
            <Button href="/contact" size="xl">
              {t('page.features.cta_button')}
            </Button>
          </div>
          <div className="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -mr-32 -mt-32" />
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-32 -mb-32" />
        </div>
      </section>
    </PublicLayout>
  );
}
