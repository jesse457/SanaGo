import type { LegalSection } from '@/Components/marketing/LegalDocument';
import { LegalDocument } from '@/Components/marketing/LegalDocument';
import { PageHero } from '@/Components/marketing/PageHero';
import { Section } from '@/Components/marketing/Shell';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';
import { formatDate } from '@/lib/format';

/**
 * Terms of service.
 *
 * The commercial terms a hospital actually negotiates over: the trial, the
 * billing cycle in FCFA, what happens during the grace period, who owns the
 * data, and what each side is responsible for.
 */
export default function Terms({ updated_at }: { updated_at: string }) {
    const { t } = useTranslation();
    const { platform, stats } = useMarketing();

    const sections: LegalSection[] = [
        {
            id: 'agreement',
            title: t('mk.terms.agreement.title', 'The agreement'),
            paragraphs: [
                t(
                    'mk.terms.agreement.p1',
                    'These terms govern the use of the SanaGo platform by a hospital, clinic or health centre (the customer) and the people it invites into its workspace. By provisioning a hospital or using a trial, the customer accepts them.',
                ),
            ],
        },
        {
            id: 'trial',
            title: t('mk.terms.trial.title', 'Trials'),
            paragraphs: [
                t(
                    'mk.terms.trial.p1',
                    'A new hospital starts on a working trial that runs for the number of days published on the pricing page at the time of signup. No payment method is required to start. At the end of the trial the workspace keeps its data and moves to read-only until a plan is taken or the hospital asks for deletion.',
                ),
            ],
        },
        {
            id: 'plans',
            title: t('mk.terms.plans.title', 'Plans, quotas and billing'),
            bullets: [
                t(
                    'mk.terms.plans.b1',
                    'Plans are priced per hospital in the currency shown on the pricing page, billed monthly or annually.',
                ),
                t(
                    'mk.terms.plans.b2',
                    'Each plan carries a user limit, a patient-record limit, a storage allowance and a set of unlocked modules. Limits are shown before a plan is chosen.',
                ),
                t(
                    'mk.terms.plans.b3',
                    'When a limit is reached, new records in that category pause. Existing records are never hidden or deleted.',
                ),
                t(
                    'mk.terms.plans.b4',
                    'Changing plan mid-cycle raises a new invoice for the difference; the change is recorded on the subscription.',
                ),
                t(
                    'mk.terms.plans.b5',
                    'After expiry there is a grace period before the workspace becomes read-only. Data is preserved during it.',
                ),
            ],
        },
        {
            id: 'customer',
            title: t(
                'mk.terms.customer.title',
                'What the hospital is responsible for',
            ),
            bullets: [
                t(
                    'mk.terms.customer.b1',
                    'Issuing, changing and revoking its own staff accounts, and keeping their roles accurate.',
                ),
                t(
                    'mk.terms.customer.b2',
                    'Clinical decisions. SanaGo records and routes information; it does not diagnose, prescribe or replace clinical judgement.',
                ),
                t(
                    'mk.terms.customer.b3',
                    'Obtaining any patient consent required by local law for the collection and use of health data.',
                ),
                t(
                    'mk.terms.customer.b4',
                    'The accuracy of tariffs, drug catalogues and insurance details entered into the workspace.',
                ),
            ],
        },
        {
            id: 'data',
            title: t('mk.terms.data.title', 'Ownership of data'),
            paragraphs: [
                t(
                    'mk.terms.data.p1',
                    'The hospital owns its data. We store and process it to run the service and for support, and we do not sell it or use it to train models. A full export is available on request, including after the subscription ends.',
                ),
            ],
        },
        {
            id: 'service',
            title: t('mk.terms.service.title', 'Availability and support'),
            paragraphs: [
                t(
                    'mk.terms.service.p1',
                    'We aim to keep the platform available continuously and to give notice of planned maintenance. Support is provided on working days through the channels published on the contact page; response times depend on the plan.',
                ),
            ],
        },
        {
            id: 'acceptable',
            title: t('mk.terms.acceptable.title', 'Acceptable use'),
            bullets: [
                t(
                    'mk.terms.acceptable.b1',
                    'No attempts to reach another hospital’s data, to bypass role permissions, or to probe the platform for vulnerabilities without written agreement.',
                ),
                t(
                    'mk.terms.acceptable.b2',
                    'No resale, sublicensing or white-labelling of the platform outside the branding options a plan includes.',
                ),
                t(
                    'mk.terms.acceptable.b3',
                    'No use of the platform to store records the hospital is not entitled to hold.',
                ),
            ],
        },
        {
            id: 'suspension',
            title: t('mk.terms.suspension.title', 'Suspension and termination'),
            paragraphs: [
                t(
                    'mk.terms.suspension.p1',
                    'A workspace may be suspended for non-payment after the grace period, or immediately where continued operation would be unlawful or would put other hospitals at risk. Where a workspace is suspended for non-payment, data is retained and access is restored when the balance is settled.',
                ),
            ],
        },
        {
            id: 'liability',
            title: t('mk.terms.liability.title', 'Limits of liability'),
            paragraphs: [
                t(
                    'mk.terms.liability.p1',
                    'The service is provided with reasonable skill and care. To the extent permitted by law, our aggregate liability in any twelve-month period is limited to the fees paid for that hospital in the preceding twelve months. We are not liable for clinical outcomes or for losses caused by data the hospital entered incorrectly.',
                ),
            ],
        },
        {
            id: 'changes',
            title: t('mk.terms.changes.title', 'Changes to these terms'),
            paragraphs: [
                t(
                    'mk.terms.changes.p1',
                    'We will give notice in the workspace before a material change takes effect. Continuing to use the platform after that date constitutes acceptance.',
                ),
            ],
        },
        {
            id: 'contact',
            title: t('mk.terms.contact.title', 'Contact'),
            paragraphs: [
                t(
                    'mk.terms.contact.p1',
                    'Contract and billing questions go to the platform support address, quoting the hospital subdomain. For anything urgent, call the published support number.',
                ),
                platform.support_email,
                platform.support_phone,
            ].filter((paragraph): paragraph is string => Boolean(paragraph)),
        },
    ];

    return (
        <PublicLayout>
            <PageHero
                index="06"
                eyebrow={t('mk.terms.eyebrow', 'Terms')}
                title={
                    <>
                        {t('mk.terms.title_a', 'The commercial terms,')}{' '}
                        <em className="text-accent-ink not-italic">
                            {t('mk.terms.title_b', 'in plain language')}
                        </em>
                        .
                    </>
                }
                lede={t(
                    'mk.terms.lede',
                    'What a hospital signs up to: the trial, the billing cycle, the quotas, who owns the data and who is responsible for what.',
                )}
                footnotes={[
                    {
                        label: t('mk.terms.f_currency', 'Currency'),
                        value: platform.currency_symbol || platform.currency,
                    },
                    {
                        label: t('mk.terms.f_trial', 'Trial'),
                        value: `${stats.trial_days} ${t('mk.terms.days', 'days')}`,
                    },
                    {
                        label: t('mk.terms.f_grace', 'Grace period'),
                        value: `${platform.grace_days} ${t('mk.terms.days', 'days')}`,
                    },
                    {
                        label: t('mk.terms.f_plans', 'Plans'),
                        value: String(stats.plans),
                    },
                ]}
            />

            <Section bordered>
                <LegalDocument
                    sections={sections}
                    intro={t('mk.legal.contents', 'Contents')}
                    updatedLabel={`${t('mk.legal.updated', 'Updated')} ${formatDate(updated_at, { style: 'short' })}`}
                />
            </Section>
        </PublicLayout>
    );
}
