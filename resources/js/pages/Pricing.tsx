import { ArrowRightIcon, CheckIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import Button from '@/Components/marketing/Button';
import { CapabilityMatrix } from '@/Components/marketing/CapabilityMatrix';
import { CtaBanner } from '@/Components/marketing/CtaBanner';
import { FaqLedger, usePricingFaq } from '@/Components/marketing/FaqLedger';
import { PageHero } from '@/Components/marketing/PageHero';
import { PlanCard } from '@/Components/marketing/PlanCard';
import {
    DisplayTitle,
    Lede,
    MicroLabel,
    SectionLedger,
} from '@/Components/marketing/SectionLedger';
import { Section } from '@/Components/marketing/Shell';
import { SegmentedControl } from '@/Components/ui';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';
import type { BillingCycle } from '@/types/marketing';

/**
 * Pricing.
 *
 * The grid, the comparison matrix and the trial length are all read from the
 * plans and settings an operator publishes in the console. There is no second
 * price list in this file to fall out of date.
 */
export default function Pricing() {
    const { t } = useTranslation();
    const { plans, capabilities, platform, stats } = useMarketing();
    const [cycle, setCycle] = useState<BillingCycle>('monthly');
    const faqs = usePricingFaq();

    const priced = plans[0];
    const annualSaving =
        priced && priced.monthly_price > 0
            ? Math.round(
                  (1 - priced.yearly_price / (priced.monthly_price * 12)) * 100,
              )
            : 0;

    const explainers = [
        {
            title: t('mk.pricing.how.invoice', 'One invoice per hospital'),
            body: t(
                'mk.pricing.how.invoice_body',
                'Billed monthly or annually in FCFA, with a record of every plan change — so the finance desk always has the paper trail.',
            ),
        },
        {
            title: t('mk.pricing.how.annual', 'Annual plans cost less'),
            body: t(
                'mk.pricing.how.annual_body',
                'Paying for twelve months up front costs less than twelve monthly payments. Change the cycle at any renewal.',
            ),
        },
        {
            title: t('mk.pricing.how.trial', 'Start before you commit'),
            body: t(
                'mk.pricing.how.trial_body',
                'Every hospital begins on a trial with its own database. Move onto a plan when the wards are ready, or walk away with your data.',
            ),
        },
    ];

    return (
        <PublicLayout>
            <PageHero
                index="02"
                eyebrow={t('mk.pricing.eyebrow', 'Plans and pricing')}
                title={
                    <>
                        {t('mk.pricing.title_a', 'Priced per hospital,')}{' '}
                        <em className="text-accent-ink not-italic">
                            {t('mk.pricing.title_b', 'not per user account')}
                        </em>
                        .
                    </>
                }
                lede={t(
                    'mk.pricing.lede',
                    'Every tier runs the same platform on its own database. What changes is how much of it you unlock, how many staff can work in it, and how many patient records it holds.',
                )}
                actions={
                    <>
                        <Button
                            href="/contact"
                            size="lg"
                            trailingIcon={ArrowRightIcon}
                        >
                            {t('mk.pricing.primary', 'Start a trial')}
                        </Button>
                        <Button href="/features" variant="secondary" size="lg">
                            {t('mk.pricing.secondary', 'Read the catalogue')}
                        </Button>
                    </>
                }
                footnotes={[
                    {
                        label: t('mk.pricing.f_cycle', 'Billing'),
                        value: `${platform.currency_symbol} · ${t('mk.pricing.f_mm', 'monthly or annual')}`,
                    },
                    {
                        label: t('mk.pricing.f_trial', 'Trial'),
                        value: `${stats.trial_days} ${t('mk.pricing.days', 'days')}`,
                    },
                    {
                        label: t('mk.pricing.f_grace', 'Grace after expiry'),
                        value: `${platform.grace_days} ${t('mk.pricing.days', 'days')}`,
                    },
                    {
                        label: t('mk.pricing.f_modules', 'Modules gated'),
                        value: String(stats.capabilities),
                    },
                ]}
            />

            <Section bordered>
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <MicroLabel>
                            {t('mk.pricing.published', 'Published plans')}
                        </MicroLabel>
                        {annualSaving > 0 && (
                            <span className="rounded-sm bg-accent-soft px-1.5 py-0.5 font-mono text-[9px] font-medium tracking-[0.1em] text-accent-ink uppercase">
                                {t('mk.pricing.annual_saving', 'Annual saves')}{' '}
                                <span data-numeric>≈ {annualSaving} %</span>
                            </span>
                        )}
                    </div>
                    <SegmentedControl
                        options={[
                            {
                                value: 'monthly',
                                label: t('mk.pricing.monthly', 'Monthly'),
                            },
                            {
                                value: 'yearly',
                                label: t('mk.pricing.yearly', 'Annual'),
                            },
                        ]}
                        value={cycle}
                        onChange={(value) => setCycle(value as BillingCycle)}
                    />
                </div>

                {plans.length > 0 ? (
                    <div className="mt-8 grid items-stretch gap-5 lg:grid-cols-3">
                        {plans.map((plan) => (
                            <PlanCard
                                key={plan.id}
                                plan={plan}
                                cycle={cycle}
                                currencySymbol={platform.currency_symbol}
                            />
                        ))}
                    </div>
                ) : (
                    <div className="mt-8 rounded-xl border border-line bg-surface p-8">
                        <p className="text-[13.5px] leading-relaxed text-ink-muted">
                            {t(
                                'mk.pricing.empty',
                                'Plans are being configured in the platform console. Talk to us and we will price your hospital directly.',
                            )}
                        </p>
                        <Button href="/contact" className="mt-5">
                            {t('mk.pricing.primary', 'Start a trial')}
                        </Button>
                    </div>
                )}

                <div className="mt-12 grid gap-8 border-t border-line pt-8 sm:grid-cols-3">
                    {explainers.map((item) => (
                        <div key={item.title}>
                            <p className="flex items-center gap-2 text-[13px] font-semibold text-ink">
                                <CheckIcon className="h-3.5 w-3.5 text-accent" />
                                {item.title}
                            </p>
                            <p className="mt-2 max-w-[38ch] text-[12.5px] leading-relaxed text-ink-muted">
                                {item.body}
                            </p>
                        </div>
                    ))}
                </div>
            </Section>

            <Section tone="muted" bordered>
                <SectionLedger
                    index="S"
                    label={t('mk.matrix.label', 'Module comparison')}
                    note={t(
                        'mk.matrix.note',
                        'Straight from each plan’s capability flags — flip a module on in the console and this table follows.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <DisplayTitle className="max-w-[26ch]">
                        {t(
                            'mk.matrix.title',
                            'Which tier unlocks which module',
                        )}
                    </DisplayTitle>
                    <Lede className="lg:max-w-[34ch]">
                        {t(
                            'mk.matrix.body',
                            'Core clinical work is in every tier. Diagnostics, theatre and the money desk are where the tiers separate.',
                        )}
                    </Lede>
                </div>
                <CapabilityMatrix
                    plans={plans}
                    capabilities={capabilities}
                    className="mt-8"
                />
            </Section>

            <Section bordered>
                <div className="grid gap-10 lg:grid-cols-12">
                    <div className="lg:col-span-4">
                        <SectionLedger
                            index="Q"
                            label={t('mk.home.faq.label', 'Questions')}
                            className="border-t-0 pt-0"
                        />
                        <DisplayTitle className="mt-6">
                            {t(
                                'mk.pricing.faq_title',
                                'Before you sign anything',
                            )}
                        </DisplayTitle>
                        <p className="mt-4 text-[13px] leading-relaxed text-ink-muted">
                            {t(
                                'mk.pricing.faq_body',
                                'Still unanswered? Write to the support address in the footer — a person reads it.',
                            )}
                        </p>
                        <Button
                            href="/contact"
                            variant="secondary"
                            className="mt-6"
                            trailingIcon={ArrowRightIcon}
                        >
                            {t('mk.pricing.ask', 'Ask a question')}
                        </Button>
                    </div>
                    <div className="lg:col-span-8">
                        <FaqLedger items={faqs} />
                    </div>
                </div>
            </Section>

            <CtaBanner className="pb-20" />
        </PublicLayout>
    );
}
