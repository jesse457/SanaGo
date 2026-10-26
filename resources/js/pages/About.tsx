import { ArrowRightIcon, ArrowUpRightIcon } from '@heroicons/react/24/outline';
import { AppWindow } from '@/Components/marketing/AppWindow';
import Button from '@/Components/marketing/Button';
import { CtaBanner } from '@/Components/marketing/CtaBanner';
import { EditorialPhoto } from '@/Components/marketing/EditorialPhoto';
import { PageHero } from '@/Components/marketing/PageHero';
import {
    DisplayTitle,
    Lede,
    MicroLabel,
    SectionLedger,
} from '@/Components/marketing/SectionLedger';
import { Section, Shell } from '@/Components/marketing/Shell';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

/**
 * About — why this software exists and how the company behaves.
 *
 * Written as a short document rather than a brochure: the problem, the rule the
 * product follows, the promises a hospital can hold us to, and the people to
 * call. The numbers are the platform's own.
 */
export default function About() {
    const { t } = useTranslation();
    const { platform, stats } = useMarketing();

    const principles = [
        {
            title: t('mk.about.p1.title', 'The record is the product'),
            body: t(
                'mk.about.p1.body',
                'Every screen either writes to the patient file or reads from it. When a feature cannot justify itself that way, it does not ship — that rule is why the ward and the cashier agree.',
            ),
        },
        {
            title: t('mk.about.p2.title', 'Permission, not trust'),
            body: t(
                'mk.about.p2.body',
                'Access is granted per role and per action, and every privileged change is logged with the person, the address and the payload. A hospital should be able to prove who saw what.',
            ),
        },
        {
            title: t('mk.about.p3.title', 'One hospital, one database'),
            body: t(
                'mk.about.p3.body',
                'Each hospital runs on its own database on its own subdomain. A login from one hospital cannot reach another’s records, however the URL is typed.',
            ),
        },
        {
            title: t('mk.about.p4.title', 'Built for the shift, not the demo'),
            body: t(
                'mk.about.p4.body',
                'Slow connections, shared terminals, a nurse with gloves on. The interfaces are designed to be operated at speed, in French or English, with the numbers we actually bill in.',
            ),
        },
    ];

    const promises = [
        {
            title: t('mk.about.promise1.title', 'A named person on support'),
            body:
                platform.support_email || platform.support_phone
                    ? t(
                          'mk.about.promise1.body',
                          'Write or call the same team that sets the hospital up — not a queue that reroutes to a reseller.',
                      )
                    : t(
                          'mk.about.promise1.body_plain',
                          'The team that sets the hospital up is the team that answers when something breaks.',
                      ),
            contact: platform.support_email || platform.support_phone,
        },
        {
            title: t('mk.about.promise2.title', 'Migration, not a re-typing'),
            body: t(
                'mk.about.promise2.body',
                'Patient lists, tariffs, drug catalogues, staff accounts and wards are loaded before go-live, from the exports your current system already produces.',
            ),
        },
        {
            title: t('mk.about.promise3.title', 'You keep the data'),
            body: t(
                'mk.about.promise3.body',
                'A full export of the hospital’s records is available at any time, including after a subscription ends. Leaving is a process, not a hostage negotiation.',
            ),
        },
    ];

    const figures = [
        {
            value: String(stats.hospitals),
            label: t('mk.about.fig_hospitals', 'Hospitals live'),
        },
        {
            value: String(stats.capabilities),
            label: t('mk.about.fig_modules', 'Modules in the product'),
        },
        {
            value: platform.supported_locales
                .map((locale) => locale.toUpperCase())
                .join(' / '),
            label: t('mk.about.fig_locales', 'Languages'),
        },
        {
            value: platform.currency_symbol || platform.currency,
            label: t('mk.about.fig_currency', 'Billing currency'),
        },
    ];

    return (
        <PublicLayout>
            <PageHero
                index="03"
                eyebrow={t('mk.about.eyebrow', 'About')}
                title={
                    <>
                        {t('mk.about.title_a', 'Hospital software should be')}{' '}
                        <em className="text-accent-ink not-italic">
                            {t('mk.about.title_b', 'the quiet part')}
                        </em>
                        .
                    </>
                }
                lede={t(
                    'mk.about.lede',
                    'SanaGo exists because small and mid-sized hospitals are offered two bad options: spreadsheets held together by one person, or an enterprise suite priced for a ministry. We build the third one.',
                )}
                actions={
                    <>
                        <Button
                            href="/contact"
                            size="lg"
                            trailingIcon={ArrowRightIcon}
                        >
                            {t('mk.about.primary', 'Talk to the team')}
                        </Button>
                        <Button href="/features" variant="secondary" size="lg">
                            {t('mk.about.secondary', 'See what we built')}
                        </Button>
                    </>
                }
                aside={
                    <div className="relative">
                        <EditorialPhoto
                            src="/images/marketing/care-team.jpg"
                            alt="A clinical team walking through a hospital corridor"
                            caption={t('mk.about.photo_caption', 'Ward round')}
                            ratio="landscape"
                            priority
                        />
                        <p className="mt-4 max-w-[34ch] text-[12px] leading-relaxed text-ink-subtle">
                            {t(
                                'mk.about.photo_note',
                                'Photography from hospital settings. Unsplash licence.',
                            )}
                        </p>
                    </div>
                }
            />

            <Section bordered>
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-10">
                    <div className="lg:col-span-4">
                        <SectionLedger
                            index="A"
                            label={t('mk.about.story_label', 'The problem')}
                            className="border-t-0 pt-0"
                        />
                        <DisplayTitle className="mt-6">
                            {t(
                                'mk.about.story_title',
                                'The gap is not software. It is software priced for this hospital.',
                            )}
                        </DisplayTitle>
                    </div>
                    <div className="space-y-5 lg:col-span-8 lg:pt-2">
                        <Lede>
                            {t(
                                'mk.about.story_p1',
                                'Most hospital software is sold per bed, per module, per year, in a currency the hospital does not earn in. So the work migrates: the patient register becomes a notebook, the pharmacy ledger becomes a spreadsheet, and the cashier reconciles three sources by hand at midnight.',
                            )}
                        </Lede>
                        <Lede>
                            {t(
                                'mk.about.story_p2',
                                'SanaGo is built the other way around. One hospital, one database, one price in FCFA, and a workflow that assumes the reality of a busy Cameroonian hospital — several stations, shared terminals, patients who arrive without a file, and insurers who pay late.',
                            )}
                        </Lede>
                        <Lede>
                            {t(
                                'mk.about.story_p3',
                                'The product is deliberately narrow. It does clinical records, diagnostics, stock, wards and money, and it does nothing else until those are excellent.',
                            )}
                        </Lede>
                    </div>
                </div>
            </Section>

            {/* What the thing actually looks like: two screens, one spread. */}
            <Section tone="muted" bordered>
                <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <SectionLedger
                            index="B"
                            label={t('mk.about.proof_label', 'On screen')}
                            className="border-t-0 pt-0"
                        />
                        <DisplayTitle className="mt-6 max-w-[26ch]">
                            {t(
                                'mk.about.proof_title',
                                'This is the product, not a highlights reel',
                            )}
                        </DisplayTitle>
                    </div>
                    <Lede className="lg:max-w-[38ch]">
                        {t(
                            'mk.about.proof_body',
                            'Two screens a hospital opens on its first Monday: the patient directory and the cashier shift. Both are drawn from the same interface the hospital actually operates.',
                        )}
                    </Lede>
                </div>

                <div className="mt-10 grid items-start gap-5 lg:grid-cols-12">
                    <div className="lg:col-span-7">
                        <AppWindow screen="patients" />
                        <p className="mt-3 font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                            {t(
                                'mk.about.proof_c1',
                                'Patient directory · search by name, phone or UID',
                            )}
                        </p>
                    </div>
                    <div className="lg:col-span-5">
                        <AppWindow screen="caisse" density="compact" />
                        <p className="mt-3 font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                            {t(
                                'mk.about.proof_c2',
                                'Caisse · one shift, one counted drawer',
                            )}
                        </p>
                    </div>
                </div>
            </Section>

            <Section tone="muted" bordered>
                <SectionLedger
                    index="C"
                    label={t('mk.about.principles_label', 'Principles')}
                    note={t(
                        'mk.about.principles_note',
                        'Four rules the product is held to.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-8 grid gap-x-10 gap-y-8 sm:grid-cols-2">
                    {principles.map((principle, index) => (
                        <div
                            key={principle.title}
                            className="border-t border-line pt-5"
                        >
                            <p
                                data-numeric
                                className="font-mono text-[10px] tracking-[0.16em] text-accent-ink tabular-nums"
                            >
                                {String(index + 1).padStart(2, '0')}
                            </p>
                            <h3 className="mt-3 text-[15px] font-semibold tracking-[-0.01em] text-ink">
                                {principle.title}
                            </h3>
                            <p className="mt-2 max-w-[46ch] text-[13px] leading-relaxed text-ink-muted">
                                {principle.body}
                            </p>
                        </div>
                    ))}
                </div>
            </Section>

            <Section bordered>
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-10">
                    <div className="lg:col-span-5">
                        <EditorialPhoto
                            src="/images/marketing/ward-beds.jpg"
                            alt="A hospital ward prepared for patients"
                            caption={t('mk.about.photo2_caption', 'The ward')}
                            ratio="landscape"
                        />
                        <dl className="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-line bg-line">
                            {figures.map((figure) => (
                                <div
                                    key={figure.label}
                                    className="bg-surface px-4 py-4"
                                >
                                    <dt className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                                        {figure.label}
                                    </dt>
                                    <dd
                                        data-numeric
                                        className="mt-1.5 text-[18px] font-semibold tracking-[-0.02em] text-ink tabular-nums"
                                    >
                                        {figure.value}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </div>

                    <div className="lg:col-span-7">
                        <SectionLedger
                            index="D"
                            label={t(
                                'mk.about.promises_label',
                                'What we owe you',
                            )}
                            className="border-t-0 pt-0"
                        />
                        <ul className="mt-6">
                            {promises.map((promise) => (
                                <li
                                    key={promise.title}
                                    className="border-t border-line py-5 last:border-b"
                                >
                                    <h3 className="text-[14.5px] font-semibold text-ink">
                                        {promise.title}
                                    </h3>
                                    <p className="mt-2 max-w-[54ch] text-[13px] leading-relaxed text-ink-muted">
                                        {promise.body}
                                    </p>
                                    {promise.contact && (
                                        <a
                                            href={
                                                promise.contact.includes('@')
                                                    ? `mailto:${promise.contact}`
                                                    : `tel:${promise.contact.replace(/\s+/g, '')}`
                                            }
                                            className="group mt-3 inline-flex items-center gap-1.5 text-[12.5px] font-medium text-accent-ink"
                                        >
                                            <span data-numeric>
                                                {promise.contact}
                                            </span>
                                            <ArrowUpRightIcon className="h-3 w-3 transition-transform duration-200 ease-premium group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                        </a>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </Section>

            <Section tone="muted" bordered>
                <Shell className="!px-0">
                    <SectionLedger
                        index="E"
                        label={t('mk.about.how_label', 'Working together')}
                        note={t(
                            'mk.about.how_note',
                            'From signature to the first ward round on the system.',
                        )}
                        className="border-t-0 pt-0"
                    />
                    <DisplayTitle className="mt-6 max-w-[26ch]">
                        {t(
                            'mk.about.how_title',
                            'A fortnight to live, not a quarter',
                        )}
                    </DisplayTitle>
                    <div className="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        {[
                            {
                                label: t('mk.about.how1', 'Onboarding'),
                                body: t(
                                    'mk.about.how1_body',
                                    'Two weeks from signed agreement to a live subdomain, with the hospital’s own tariffs loaded.',
                                ),
                            },
                            {
                                label: t('mk.about.how2', 'Training'),
                                body: t(
                                    'mk.about.how2_body',
                                    'Per-desk sessions — front office, nursing, laboratory, pharmacy, cashier — in French or English.',
                                ),
                            },
                            {
                                label: t('mk.about.how3', 'Support'),
                                body: t(
                                    'mk.about.how3_body',
                                    'Weekday support by phone and email, with an audit trail of every ticket.',
                                ),
                            },
                            {
                                label: t('mk.about.how4', 'Reviews'),
                                body: t(
                                    'mk.about.how4_body',
                                    'A quarterly look at what the wards actually use, and what we should stop building.',
                                ),
                            },
                        ].map((item) => (
                            <div
                                key={item.label}
                                className="border-t border-line pt-4"
                            >
                                <MicroLabel>{item.label}</MicroLabel>
                                <p className="mt-3 text-[13px] leading-relaxed text-ink-muted">
                                    {item.body}
                                </p>
                            </div>
                        ))}
                    </div>
                </Shell>
            </Section>

            <CtaBanner
                className="pb-20"
                title={t(
                    'mk.about.cta_title',
                    'Come and interrogate the product.',
                )}
                description={t(
                    'mk.about.cta_body',
                    'Bring your hardest workflow — a transfer between wards, a half-paid invoice, a lab result that changes the treatment. We will walk it on a real screen.',
                )}
            />
        </PublicLayout>
    );
}
