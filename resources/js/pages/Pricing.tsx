import { Check } from 'lucide-react';
import PricingCard from '@/Components/marketing/PricingCard';
import SectionHeading from '@/Components/marketing/SectionHeading';
import PublicLayout from '@/Layouts/PublicLayout';

const PLANS = [
  {
    name: 'Starter',
    price: 'Free',
    period: '/month',
    description: 'For small clinics getting started with digital management.',
    highlighted: false,
    ctaLabel: 'Get Started',
    ctaHref: '/contact',
    features: [
      { included: true, text: 'Up to 50 patients' },
      { included: true, text: 'Patient management' },
      { included: true, text: 'Appointment scheduling' },
      { included: true, text: 'Basic billing' },
      { included: true, text: 'Single user' },
      { included: false, text: 'Lab & diagnostics' },
      { included: false, text: 'Pharmacy management' },
      { included: false, text: 'Multi-user roles' },
      { included: false, text: 'Priority support' },
    ],
  },
  {
    name: 'Professional',
    price: '$49',
    period: '/month',
    description: 'For growing practices that need full clinic management.',
    highlighted: true,
    ctaLabel: 'Start Free Trial',
    ctaHref: '/contact',
    features: [
      { included: true, text: 'Unlimited patients' },
      { included: true, text: 'Patient management' },
      { included: true, text: 'Appointment scheduling' },
      { included: true, text: 'Billing & revenue' },
      { included: true, text: 'Lab & diagnostics' },
      { included: true, text: 'Pharmacy management' },
      { included: true, text: 'Up to 10 users' },
      { included: true, text: 'Role-based access' },
      { included: false, text: 'Multi-tenant support' },
      { included: false, text: 'Dedicated support' },
    ],
  },
  {
    name: 'Enterprise',
    price: 'Custom',
    period: '',
    description: 'For hospital networks needing multi-tenant deployment.',
    highlighted: false,
    ctaLabel: 'Contact Sales',
    ctaHref: '/contact',
    features: [
      { included: true, text: 'Everything in Professional' },
      { included: true, text: 'Unlimited users' },
      { included: true, text: 'Multi-tenant isolation' },
      { included: true, text: 'Subscription management' },
      { included: true, text: 'Central super-admin panel' },
      { included: true, text: 'Custom integrations' },
      { included: true, text: 'Dedicated support' },
      { included: true, text: 'SLA guarantee' },
      { included: true, text: 'On-premise deployment' },
    ],
  },
];

const FAQS = [
  {
    question: 'Can I switch plans later?',
    answer: 'Absolutely. You can upgrade or downgrade your plan at any time. Changes take effect at the start of your next billing cycle.',
  },
  {
    question: 'Is there a free trial?',
    answer: 'The Professional plan includes a 30-day free trial. No credit card required — just sign up and start using SanaGo.',
  },
  {
    question: 'What kind of support do you offer?',
    answer: 'Starter includes community support. Professional includes email support with a 24-hour response time. Enterprise gets a dedicated account manager and SLA-backed priority support.',
  },
  {
    question: 'Can I host SanaGo on my own servers?',
    answer: 'Yes, Enterprise plans support on-premise deployment. We also offer cloud-hosted options for all plans.',
  },
];

export default function Pricing() {
  return (
    <PublicLayout>
      {/* Hero */}
      <section className="pt-32 lg:pt-44 pb-20">
        <div className="max-w-7xl mx-auto px-6">
          <SectionHeading
            title="Simple, Transparent Pricing"
            subtitle="Choose the plan that fits your clinic or hospital. All plans include core hospital management features."
          />
        </div>
      </section>

      {/* Pricing Cards */}
      <section className="pb-24">
        <div className="max-w-5xl mx-auto px-6 grid md:grid-cols-3 gap-8 items-start">
          {PLANS.map((plan) => (
            <PricingCard
              key={plan.name}
              name={plan.name}
              price={plan.price}
              period={plan.period}
              description={plan.description}
              features={plan.features}
              highlighted={plan.highlighted}
              ctaLabel={plan.ctaLabel}
              ctaHref={plan.ctaHref}
            />
          ))}
        </div>
      </section>

      {/* FAQ */}
      <section className="py-24 bg-slate-50 dark:bg-neutral-800">
        <div className="max-w-3xl mx-auto px-6">
          <SectionHeading
            title="Frequently Asked Questions"
            subtitle="Can't find what you're looking for? Reach out to our team."
          />

          <div className="space-y-4">
            {FAQS.map((faq) => (
              <details
                key={faq.question}
                className="bg-white dark:bg-neutral-900 rounded-2xl border border-slate-100 dark:border-neutral-700 group"
              >
                <summary className="px-6 py-5 font-semibold text-slate-900 dark:text-neutral-100 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center justify-between">
                  {faq.question}
                  <Check size={16} className="text-slate-300 dark:text-neutral-600 group-open:hidden" />
                </summary>
                <p className="px-6 pb-5 text-sm text-slate-500 dark:text-neutral-400 leading-relaxed">
                  {faq.answer}
                </p>
              </details>
            ))}
          </div>
        </div>
      </section>
    </PublicLayout>
  );
}
