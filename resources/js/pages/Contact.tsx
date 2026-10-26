import { ArrowRightIcon, ClockIcon } from '@heroicons/react/24/outline';
import DemoRequestForm from '@/Components/marketing/DemoRequestForm';
import { EditorialPhoto } from '@/Components/marketing/EditorialPhoto';
import { PageHero } from '@/Components/marketing/PageHero';
import {
    DisplayTitle,
    Lede,
    MicroLabel,
    SectionLedger,
} from '@/Components/marketing/SectionLedger';
import { Section } from '@/Components/marketing/Shell';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

interface ContactDetail {
    label: string;
    value: string;
    href: string | null;
}

/**
 * Contact — one form, one team, no funnel.
 *
 * The demo request is the only conversion on the site. It posts to the same
 * endpoint the console reads in its request queue, and the contact details
 * beside it are the support settings the operator maintains.
 */
export default function Contact() {
    const { t } = useTranslation();
    const { platform, stats } = useMarketing();

    const details: ContactDetail[] = [
        platform.support_email
            ? {
                  label: t('mk.contact.email', 'Email'),
                  value: platform.support_email,
                  href: `mailto:${platform.support_email}`,
              }
            : null,
        platform.support_phone
            ? {
                  label: t('mk.contact.phone', 'Phone'),
                  value: platform.support_phone,
                  href: `tel:${platform.support_phone.replace(/\s+/g, '')}`,
              }
            : null,
        {
            label: t('mk.contact.languages', 'Languages'),
            value: platform.supported_locales
                .map((locale) => (locale === 'fr' ? 'Français' : 'English'))
                .join(' · '),
            href: null,
        },
        {
            label: t('mk.contact.trial', 'Trial'),
            value: `${stats.trial_days} ${t('mk.contact.days', 'days')}`,
            href: null,
        },
    ].filter((detail): detail is ContactDetail => detail !== null);

    const steps = [
        {
            title: t('mk.contact.step1.title', 'You send the form'),
            body: t(
                'mk.contact.step1.body',
                'It lands in the platform’s request queue with the hospital name and what you are trying to fix.',
            ),
        },
        {
            title: t('mk.contact.step2.title', 'We reply within a working day'),
            body: t(
                'mk.contact.step2.body',
                'A short call to understand the wards, the tariffs and who currently holds the data.',
            ),
        },
        {
            title: t(
                'mk.contact.step3.title',
                'A working session, not a slideshow',
            ),
            body: t(
                'mk.contact.step3.body',
                'We open the product and walk the workflow you described — with your units and drug names on screen.',
            ),
        },
    ];

    return (
        <PublicLayout>
            <PageHero
                index="04"
                eyebrow={t('mk.contact.eyebrow', 'Contact')}
                title={
                    <>
                        {t('mk.contact.title_a', 'Tell us what breaks')}{' '}
                        <em className="text-accent-ink not-italic">
                            {t('mk.contact.title_b', 'on a normal Tuesday')}
                        </em>
                        .
                    </>
                }
                lede={t(
                    'mk.contact.lede',
                    'The useful demo is the one where you describe the mess and we show you the screen. Send the form, or write straight to the support address — both are read by the same people.',
                )}
                footnotes={[
                    {
                        label: t('mk.contact.f_reply', 'Reply time'),
                        value: t('mk.contact.f_reply_v', '1 working day'),
                    },
                    {
                        label: t('mk.contact.f_session', 'Session'),
                        value: t('mk.contact.f_session_v', '45 minutes'),
                    },
                    {
                        label: t('mk.contact.f_trial', 'Trial'),
                        value: `${stats.trial_days} ${t('mk.contact.days', 'days')}`,
                    },
                    {
                        label: t('mk.contact.f_cost', 'Cost'),
                        value: t('mk.contact.f_cost_v', 'No card'),
                    },
                ]}
            />

            <Section bordered>
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-10">
                    <div className="lg:col-span-7">
                        <SectionLedger
                            index="F"
                            label={t('mk.contact.form_label', 'Request a demo')}
                            className="border-t-0 pt-0"
                        />
                        <div className="mt-6">
                            <DemoRequestForm />
                        </div>

                        {/* Balances the form column and saves a round of email */}
                        <div className="mt-10 border-t border-line pt-6">
                            <MicroLabel>
                                {t('mk.contact.bring', 'Bring to the session')}
                            </MicroLabel>
                            <ul className="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                                {[
                                    t(
                                        'mk.contact.bring1',
                                        'Your current tariff list, by service',
                                    ),
                                    t(
                                        'mk.contact.bring2',
                                        'The units and wards you run, and their bed counts',
                                    ),
                                    t(
                                        'mk.contact.bring3',
                                        'The drug and consumable catalogue you order from',
                                    ),
                                    t(
                                        'mk.contact.bring4',
                                        'The one report your board asks for every month',
                                    ),
                                ].map((item) => (
                                    <li
                                        key={item}
                                        className="flex gap-2.5 text-[12.5px] leading-relaxed text-ink-muted"
                                    >
                                        <span
                                            aria-hidden
                                            className="mt-[7px] h-1 w-1 shrink-0 rounded-full bg-accent"
                                        />
                                        {item}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>

                    <aside className="lg:col-span-5">
                        <SectionLedger
                            index="C"
                            label={t(
                                'mk.contact.details_label',
                                'Direct lines',
                            )}
                            className="border-t-0 pt-0"
                        />
                        <dl className="mt-6 border-t border-line">
                            {details.map((detail) => (
                                <div
                                    key={detail.label}
                                    className="border-b border-line py-3.5"
                                >
                                    <dt className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                                        {detail.label}
                                    </dt>
                                    <dd className="mt-1 text-[13.5px] font-medium text-ink">
                                        {detail.href ? (
                                            <a
                                                href={detail.href}
                                                className="transition-colors duration-150 hover:text-accent-ink"
                                            >
                                                <span data-numeric>
                                                    {detail.value}
                                                </span>
                                            </a>
                                        ) : (
                                            <span data-numeric>
                                                {detail.value}
                                            </span>
                                        )}
                                    </dd>
                                </div>
                            ))}
                        </dl>

                        <div className="mt-8 rounded-lg border border-line bg-surface-muted p-5">
                            <p className="flex items-center gap-2">
                                <ClockIcon className="h-3.5 w-3.5 text-accent" />
                                <MicroLabel>
                                    {t('mk.contact.next', 'What happens next')}
                                </MicroLabel>
                            </p>
                            <ol className="mt-4 space-y-4">
                                {steps.map((step, index) => (
                                    <li key={step.title} className="flex gap-3">
                                        <span
                                            data-numeric
                                            className="font-mono text-[9.5px] text-accent-ink tabular-nums"
                                        >
                                            {String(index + 1).padStart(2, '0')}
                                        </span>
                                        <span>
                                            <span className="block text-[12.5px] font-semibold text-ink">
                                                {step.title}
                                            </span>
                                            <span className="mt-1 block text-[12px] leading-relaxed text-ink-muted">
                                                {step.body}
                                            </span>
                                        </span>
                                    </li>
                                ))}
                            </ol>
                        </div>

                        <div className="mt-8">
                            <EditorialPhoto
                                src="/images/marketing/vitals-check.jpg"
                                alt="A nurse recording a patient's blood pressure"
                                caption={t(
                                    'mk.contact.photo_caption',
                                    'Consultation',
                                )}
                                ratio="wide"
                            />
                        </div>

                        <p className="mt-6 border-t border-line pt-4 text-[12px] leading-relaxed text-ink-muted">
                            {t(
                                'mk.contact.login_note',
                                'Already a hospital on SanaGo? Do not use this form — sign in on your hospital’s own address and raise a support ticket from the console so it carries your tenant and permissions.',
                            )}
                        </p>
                    </aside>
                </div>
            </Section>

            <Section tone="muted" bordered>
                <div className="grid gap-8 lg:grid-cols-12 lg:items-end">
                    <div className="lg:col-span-7">
                        <DisplayTitle>
                            {t(
                                'mk.contact.closing_title',
                                'If it is easier, call and ask for the SanaGo team.',
                            )}
                        </DisplayTitle>
                    </div>
                    <div className="lg:col-span-5">
                        <Lede>
                            {t(
                                'mk.contact.closing_body',
                                'We would rather answer a specific question about the cashier shift than send a brochure.',
                            )}
                        </Lede>
                        <a
                            href={
                                platform.support_email
                                    ? `mailto:${platform.support_email}`
                                    : '/contact'
                            }
                            className="group mt-5 inline-flex items-center gap-2 text-[13px] font-medium text-ink transition-colors duration-150 hover:text-accent-ink"
                        >
                            {platform.support_email ||
                                t('mk.contact.email', 'Email')}
                            <ArrowRightIcon className="h-3.5 w-3.5 transition-transform duration-200 ease-premium group-hover:translate-x-0.5" />
                        </a>
                    </div>
                </div>
            </Section>
        </PublicLayout>
    );
}
