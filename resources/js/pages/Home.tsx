import {
    ArrowRightIcon,
    BanknotesIcon,
    BeakerIcon,
    CalendarDaysIcon,
    CheckIcon,
    CubeIcon,
    HeartIcon,
    HomeModernIcon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import { useState } from 'react';
import type { ReactNode } from 'react';
import { AppWindow } from '@/Components/marketing/AppWindow';
import Button from '@/Components/marketing/Button';
import { CtaBanner } from '@/Components/marketing/CtaBanner';
import { EditorialPhoto } from '@/Components/marketing/EditorialPhoto';
import { FaqLedger, usePricingFaq } from '@/Components/marketing/FaqLedger';
import FeatureCard from '@/Components/marketing/FeatureCard';
import { ProductShot } from '@/Components/marketing/ProductShot';
import { RoleConsole } from '@/Components/marketing/RoleConsole';
import {
    DisplayTitle,
    Lede,
    MicroLabel,
    SectionLedger,
} from '@/Components/marketing/SectionLedger';
import { Section, Shell } from '@/Components/marketing/Shell';
import { WorkflowRail } from '@/Components/marketing/WorkflowRail';
import { WorkspaceTour } from '@/Components/marketing/WorkspaceTour';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';
import { formatCurrency } from '@/lib/format';
import { cn } from '@/lib/utils';

interface ModuleEntry {
    key: string;
    icon: ReactNode;
    tint: 'blue' | 'sage' | 'clay' | 'lavender' | 'sand';
    title: string;
    description: string;
    media?: ReactNode;
    span: string;
}

/**
 * The public home page.
 *
 * The argument runs in one line: one record → every desk → the money. Each
 * band is a section of the same document (numbered ledger headings, hairlines,
 * no floating cards), and every number, price and contact on it is read from
 * the platform console rather than typed into React.
 */
export default function Home() {
    const { t } = useTranslation();
    const { stats, plans, platform } = useMarketing();
    const [activeRole, setActiveRole] = useState('doctor');
    const faqs = usePricingFaq();

    const modules: ModuleEntry[] = [
        {
            key: 'records',
            icon: <UsersIcon className="h-[18px] w-[18px]" />,
            tint: 'blue',
            title: t('mk.home.modules.records.title', 'Patient records'),
            description: t(
                'mk.home.modules.records.body',
                'A file opened once and never re-typed: demographics, guardian, blood group, insurer, allergies, and every visit that follows. Searchable by name, phone or hospital UID.',
            ),
            media: (
                <div className="relative">
                    <EditorialPhoto
                        src="/images/marketing/reception.jpg"
                        alt="A hospital records counter with a staffed reception window"
                        caption={t(
                            'mk.home.modules.records.caption',
                            'Records desk',
                        )}
                        ratio="cinema"
                    />
                </div>
            ),
            span: 'lg:col-span-7',
        },
        {
            key: 'diagnostics',
            icon: <BeakerIcon className="h-[18px] w-[18px]" />,
            tint: 'lavender',
            title: t(
                'mk.home.modules.diagnostics.title',
                'Laboratory & imaging',
            ),
            description: t(
                'mk.home.modules.diagnostics.body',
                'Requests leave the consultation already attached to the patient. Technicians work a list, enter results against the test definition, and release them signed.',
            ),
            media: <ProductShot variant="report" />,
            span: 'lg:col-span-5',
        },
        {
            key: 'consult',
            icon: <CalendarDaysIcon className="h-[18px] w-[18px]" />,
            tint: 'sage',
            title: t(
                'mk.home.modules.consult.title',
                'Consultations & prescriptions',
            ),
            description: t(
                'mk.home.modules.consult.body',
                'Notes, diagnosis, prescription and follow-up date in one sitting — with allergy warnings against the record before the prescription is signed.',
            ),
            span: 'lg:col-span-4',
        },
        {
            key: 'pharmacy',
            icon: <CubeIcon className="h-[18px] w-[18px]" />,
            tint: 'clay',
            title: t('mk.home.modules.pharmacy.title', 'Pharmacy & stock'),
            description: t(
                'mk.home.modules.pharmacy.body',
                'Dispense from the prescription, batch by batch. Stock, expiries and low-level alerts follow the same transaction.',
            ),
            span: 'lg:col-span-4',
        },
        {
            key: 'wards',
            icon: <HomeModernIcon className="h-[18px] w-[18px]" />,
            tint: 'sand',
            title: t('mk.home.modules.wards.title', 'Wards & admissions'),
            description: t(
                'mk.home.modules.wards.body',
                'A real bed per patient, a deposit on the account, and the shift handover written down instead of remembered.',
            ),
            span: 'lg:col-span-4',
        },
        {
            key: 'money',
            icon: <BanknotesIcon className="h-[18px] w-[18px]" />,
            tint: 'blue',
            title: t(
                'mk.home.modules.money.title',
                'Billing, cashier & claims',
            ),
            description: t(
                'mk.home.modules.money.body',
                'One invoice per episode. The cashier takes the payment, the shift reconciles to a counted drawer, and the insurer claim keeps its supporting lines.',
            ),
            media: <ProductShot variant="revenue" />,
            span: 'lg:col-span-7',
        },
        {
            key: 'theatre',
            icon: <HeartIcon className="h-[18px] w-[18px]" />,
            tint: 'clay',
            title: t('mk.home.modules.theatre.title', 'Surgery & emergency'),
            description: t(
                'mk.home.modules.emergency.body',
                'Schedule a theatre, record the procedure and its fee, and register an emergency arrival with triage before the file is complete.',
            ),
            media: <ProductShot variant="ward" />,
            span: 'lg:col-span-5',
        },
    ];

    const heroFacts = [
        {
            value: String(stats.capabilities),
            label: t('mk.home.facts.modules', 'Modules gated by plan'),
        },
        {
            value: '07',
            label: t('mk.home.facts.desks', 'Role consoles'),
        },
        {
            value: String(stats.trial_days),
            label: t('mk.home.facts.trial', 'Day trial, no card'),
        },
        {
            value: platform.supported_locales
                .map((locale) => locale.toUpperCase())
                .join(' / '),
            label: t('mk.home.facts.locales', 'Working languages'),
        },
    ];

    const marketFacts = [
        {
            title: t('mk.home.market.currency', 'FCFA, natively'),
            body: t(
                'mk.home.market.currency_body',
                'Tariffs, invoices, cashier shifts and insurer claims are all held in FCFA. No exchange-rate workaround at the desk.',
            ),
        },
        {
            title: t('mk.home.market.language', 'French and English'),
            body: t(
                'mk.home.market.language_body',
                'Every desk can work in either language — the interface a nurse sees does not depend on the language the administrator chose.',
            ),
        },
        {
            title: t('mk.home.market.sms', 'MTN & Orange SMS'),
            body: t(
                'mk.home.market.sms_body',
                'Appointment reminders and result notices go out as SMS on Cameroonian networks, with email as the fallback.',
            ),
        },
        {
            title: t('mk.home.market.claims', 'Tiers payant'),
            body: t(
                'mk.home.market.claims_body',
                'Patient insurance, policy numbers and claim lines are part of the record, not a spreadsheet beside it.',
            ),
        },
    ];

    const featuredPlans = plans.slice(0, 4);

    return (
        <PublicLayout>
            {/* ── Hero ───────────────────────────────────────────────── */}
            <section className="relative overflow-hidden pt-10 pb-14 sm:pt-14 lg:pt-16 lg:pb-20">
                <Shell>
                    <div className="grid items-start gap-12 lg:grid-cols-12 lg:gap-10">
                        <div className="lg:col-span-7">
                            <div className="flex flex-wrap items-center gap-2.5">
                                <span className="inline-flex items-center gap-2 rounded-md border border-line bg-surface px-2.5 py-1 font-mono text-[9.5px] font-medium tracking-[0.16em] text-ink-muted uppercase">
                                    <span className="h-1.5 w-1.5 rounded-full bg-positive" />
                                    {platform.name}
                                    {platform.currency_symbol &&
                                        ` · ${platform.currency_symbol}`}
                                </span>
                                <span className="font-mono text-[9.5px] tracking-[0.16em] text-ink-subtle uppercase">
                                    {t(
                                        'mk.home.hero.eyebrow',
                                        'Hospital management system',
                                    )}
                                </span>
                            </div>

                            <DisplayTitle as="h1" size="hero" className="mt-7">
                                {t('mk.home.hero.title_a', 'One record,')}
                                <br />
                                {t(
                                    'mk.home.hero.title_b',
                                    'from the door',
                                )}{' '}
                                <em className="text-accent-ink not-italic">
                                    {t(
                                        'mk.home.hero.title_c',
                                        'to the receipt',
                                    )}
                                </em>
                                .
                            </DisplayTitle>

                            <Lede className="mt-7">
                                {t(
                                    'mk.home.hero.lede',
                                    'SanaGo runs the front desk, the consultation room, the laboratory, the pharmacy, the ward and the cashier on the same patient file — so the chart a doctor reads is the chart the nurse wrote, and the invoice is the one the cashier can prove.',
                                )}
                            </Lede>

                            <div className="mt-9 flex flex-wrap items-center gap-3">
                                <Button
                                    href="/contact"
                                    size="lg"
                                    trailingIcon={ArrowRightIcon}
                                >
                                    {t(
                                        'mk.home.hero.primary',
                                        'Book a working session',
                                    )}
                                </Button>
                                <Button
                                    href="/pricing"
                                    variant="secondary"
                                    size="lg"
                                >
                                    {t(
                                        'mk.home.hero.secondary',
                                        'See what it costs',
                                    )}
                                </Button>
                            </div>

                            <dl className="mt-12 grid grid-cols-2 gap-x-6 gap-y-6 sm:grid-cols-4">
                                {heroFacts.map((fact) => (
                                    <div
                                        key={fact.label}
                                        className="border-t border-line pt-3.5"
                                    >
                                        <dt className="sr-only">
                                            {fact.label}
                                        </dt>
                                        <dd>
                                            <span
                                                data-numeric
                                                className="block text-[19px] leading-none font-semibold tracking-[-0.025em] text-ink tabular-nums"
                                            >
                                                {fact.value}
                                            </span>
                                            <span className="mt-2 block font-mono text-[9px] leading-tight tracking-[0.13em] text-ink-subtle uppercase">
                                                {fact.label}
                                            </span>
                                        </dd>
                                    </div>
                                ))}
                            </dl>
                        </div>

                        {/* Evidence: the room */}
                        <div className="relative lg:col-span-5 lg:pl-6">
                            <EditorialPhoto
                                src="/images/marketing/clinicians-screen.jpg"
                                alt="A doctor reviewing patient scans at a diagnostic workstation while a colleague looks on"
                                ratio="portrait"
                                priority
                                className="shadow-surface"
                            />
                        </div>
                    </div>

                    {/* The screen itself, at full measure: the workspace the
                        copy above has just described. */}
                    <div className="mt-14 lg:mt-16">
                        <AppWindow screen="dashboard" />
                        <p className="mt-3 font-mono text-[9.5px] tracking-[0.14em] text-ink-subtle uppercase">
                            {t(
                                'mk.home.hero.caption',
                                'The administrator dashboard of a live hospital, drawn from the product UI kit',
                            )}
                        </p>
                    </div>
                </Shell>
            </section>

            {/* ── 01 The workspace ───────────────────────────────────── */}
            <Section id="workspace" tone="muted" bordered>
                <SectionLedger
                    index="01"
                    label={t('mk.tour.label', 'The workspace')}
                    note={t(
                        'mk.tour.note',
                        'Seven stops on one patient file. Pick a desk to see the screen it opens on.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <DisplayTitle className="max-w-[24ch]">
                        {t(
                            'mk.tour.title',
                            'Look at the screens before you look at the price',
                        )}
                    </DisplayTitle>
                    <Lede className="lg:max-w-[38ch]">
                        {t(
                            'mk.tour.body',
                            'Nothing on this page is a mock-up made for a brochure. It is the same interface a receptionist, a nurse and a cashier work in on the day you go live.',
                        )}
                    </Lede>
                </div>
                <WorkspaceTour className="mt-12" />
            </Section>

            {/* ── 01 Modules ─────────────────────────────────────────── */}
            <Section id="modules" tone="muted" bordered>
                <SectionLedger
                    index="02"
                    label={t('mk.home.modules.label', 'Modules')}
                    note={t(
                        'mk.home.modules.note',
                        'Eight desks, one file. Each module is a capability the platform can grant per plan.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <DisplayTitle className="max-w-[22ch]">
                        {t(
                            'mk.home.modules.title',
                            'Everything the ward needs, in the order it happens',
                        )}
                    </DisplayTitle>
                    <Lede className="lg:max-w-[40ch]">
                        {t(
                            'mk.home.modules.body',
                            'Nothing here is a module you visit once. Each screen writes into the record the next one reads.',
                        )}
                    </Lede>
                </div>

                <div className="mt-12 grid gap-5 lg:grid-cols-12">
                    {modules.map((module, index) => (
                        <FeatureCard
                            key={module.key}
                            icon={module.icon}
                            tint={module.tint}
                            title={module.title}
                            description={module.description}
                            index={String(index + 1).padStart(2, '0')}
                            className={cn('bg-surface', module.span)}
                        >
                            {module.media}
                        </FeatureCard>
                    ))}
                </div>

                <div className="mt-8 flex flex-wrap items-center gap-4">
                    <Button
                        href="/features"
                        variant="secondary"
                        trailingIcon={ArrowRightIcon}
                    >
                        {t('mk.home.modules.cta', 'The full module catalogue')}
                    </Button>
                    <p className="font-mono text-[9.5px] tracking-[0.14em] text-ink-subtle uppercase">
                        {t(
                            'mk.home.modules.cta_note',
                            'Read from the platform console · changes when a plan changes',
                        )}
                    </p>
                </div>
            </Section>

            {/* ── 02 Patient journey ─────────────────────────────────── */}
            <Section id="workflow" bordered>
                <SectionLedger
                    index="03"
                    label={t('mk.home.workflow.label', 'Patient journey')}
                    note={t(
                        'mk.home.workflow.note',
                        'Six hand-offs. The record is not re-created at any of them.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 grid gap-6 lg:grid-cols-12">
                    <DisplayTitle className="lg:col-span-7">
                        {t(
                            'mk.home.workflow.title',
                            'The same chart, six rooms later',
                        )}
                    </DisplayTitle>
                    <Lede className="lg:col-span-5">
                        {t(
                            'mk.home.workflow.body',
                            'Most hospitals lose the thread between the queue and the ward. SanaGo is built so that hand-off is a state change on one record, not a new form.',
                        )}
                    </Lede>
                </div>
                <WorkflowRail className="mt-12" />
            </Section>

            {/* ── 03 Role consoles ───────────────────────────────────── */}
            <Section id="roles" tone="muted" bordered>
                <SectionLedger
                    index="04"
                    label={t('mk.home.roles.label', 'Role consoles')}
                    note={t(
                        'mk.home.roles.note',
                        'Permissions decide what a desk can open — not a shared password.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <DisplayTitle className="max-w-[24ch]">
                        {t(
                            'mk.home.roles.title',
                            'Each desk opens on the work it does',
                        )}
                    </DisplayTitle>
                    <Lede className="lg:max-w-[38ch]">
                        {t(
                            'mk.home.roles.body',
                            'A nurse should not have to read the revenue report to record a temperature. Pick a desk to see its screen.',
                        )}
                    </Lede>
                </div>
                <RoleConsole
                    activeRole={activeRole}
                    onRoleChange={setActiveRole}
                    className="mt-12"
                />
            </Section>

            {/* ── 04 Inside a working week ───────────────────────────── */}
            <Section id="inside" bordered>
                <SectionLedger
                    index="05"
                    label={t('mk.home.inside.label', 'Inside the hospital')}
                    note={t(
                        'mk.home.inside.note',
                        'Three rooms this software was shaped around.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 grid gap-10 lg:grid-cols-12 lg:items-end">
                    <DisplayTitle className="max-w-[20ch] lg:col-span-7">
                        {t(
                            'mk.home.inside.title',
                            'Written with the people who work the shift',
                        )}
                    </DisplayTitle>
                    <Lede className="lg:col-span-5">
                        {t(
                            'mk.home.inside.body',
                            'The messy parts are the point: a patient who arrives without a file, a prescription half dispensed, a drawer that does not balance. Those are the moments the screens are designed for.',
                        )}
                    </Lede>
                </div>

                <div className="mt-10 grid gap-5 sm:grid-cols-3">
                    <figure>
                        <EditorialPhoto
                            src="/images/marketing/reception.jpg"
                            alt="Hospital reception and records counter"
                            caption={t('mk.home.inside.c1', 'Front desk')}
                            ratio="landscape"
                        />
                        <figcaption className="mt-3 text-[12.5px] leading-relaxed text-ink-muted">
                            {t(
                                'mk.home.inside.c1_body',
                                'Registration takes a minute, not a re-interview.',
                            )}
                        </figcaption>
                    </figure>
                    <figure>
                        <EditorialPhoto
                            src="/images/marketing/vitals-check.jpg"
                            alt="A nurse taking a patient's blood pressure during a consultation"
                            caption={t('mk.home.inside.c2', 'Consultation')}
                            ratio="landscape"
                        />
                        <figcaption className="mt-3 text-[12.5px] leading-relaxed text-ink-muted">
                            {t(
                                'mk.home.inside.c2_body',
                                'Vitals, notes and the next appointment in one sitting.',
                            )}
                        </figcaption>
                    </figure>
                    <figure>
                        <EditorialPhoto
                            src="/images/marketing/pharmacy-shelves.jpg"
                            alt="A pharmacist checking medication stock on the dispensary shelves"
                            caption={t('mk.home.inside.c3', 'Dispensary')}
                            ratio="landscape"
                        />
                        <figcaption className="mt-3 text-[12.5px] leading-relaxed text-ink-muted">
                            {t(
                                'mk.home.inside.c3_body',
                                'Every box dispensed leaves a trace on the batch.',
                            )}
                        </figcaption>
                    </figure>
                </div>

                {/* Local detail — the parts that only make sense here */}
                <div className="mt-16 grid gap-10 border-t border-line pt-10 lg:grid-cols-12 lg:gap-12">
                    <div className="lg:col-span-4">
                        <MicroLabel>
                            {t('mk.home.market.label', 'Local detail')}
                        </MicroLabel>
                        <h3 className="mt-4 font-editorial text-[clamp(1.4rem,2.2vw,1.8rem)] leading-[1.15] text-ink">
                            {t(
                                'mk.home.market.title',
                                'Built for a Cameroonian hospital, not translated for one',
                            )}
                        </h3>
                    </div>
                    <ul className="grid gap-x-8 gap-y-7 sm:grid-cols-2 lg:col-span-8">
                        {marketFacts.map((fact) => (
                            <li
                                key={fact.title}
                                className="flex gap-3 border-t border-line pt-4"
                            >
                                <CheckIcon className="mt-0.5 h-3.5 w-3.5 shrink-0 text-accent" />
                                <div>
                                    <p className="text-[13.5px] font-semibold text-ink">
                                        {fact.title}
                                    </p>
                                    <p className="mt-1.5 text-[12.5px] leading-relaxed text-ink-muted">
                                        {fact.body}
                                    </p>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            </Section>

            {/* ── 05 Plans ───────────────────────────────────────────── */}
            <Section id="plans" tone="muted" bordered>
                <SectionLedger
                    index="06"
                    label={t('mk.home.plans.label', 'Plans')}
                    note={t(
                        'mk.home.plans.note',
                        'Read from the plans published in the platform console.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 grid gap-6 lg:grid-cols-12 lg:items-end">
                    <DisplayTitle className="lg:col-span-7">
                        {t(
                            'mk.home.plans.title',
                            'Priced per hospital, in FCFA',
                        )}
                    </DisplayTitle>
                    <div className="lg:col-span-5 lg:text-right">
                        <Button
                            href="/pricing"
                            variant="secondary"
                            trailingIcon={ArrowRightIcon}
                        >
                            {t('mk.home.plans.cta', 'Compare every module')}
                        </Button>
                    </div>
                </div>

                {featuredPlans.length > 0 ? (
                    <ul className="mt-10 border-t border-line">
                        {featuredPlans.map((plan) => (
                            <li
                                key={plan.id}
                                className={cn(
                                    'grid items-baseline gap-3 border-b border-line py-5 sm:grid-cols-12 sm:gap-6',
                                )}
                            >
                                <div className="sm:col-span-3">
                                    <p className="text-[14.5px] font-semibold text-ink">
                                        {plan.name}
                                    </p>
                                    {plan.is_featured && (
                                        <p className="mt-1 font-mono text-[9px] tracking-[0.14em] text-accent-ink uppercase">
                                            {t(
                                                'mk.pricing.recommended',
                                                'Recommended',
                                            )}
                                        </p>
                                    )}
                                </div>
                                <p className="text-[12.5px] leading-relaxed text-ink-muted sm:col-span-5">
                                    {plan.description ?? ''}
                                </p>
                                <div className="flex flex-wrap gap-x-5 gap-y-1 sm:col-span-2">
                                    <span className="font-mono text-[10px] tracking-[0.1em] text-ink-subtle uppercase">
                                        {plan.limits.users ?? '∞'}{' '}
                                        {t('mk.pricing.limit_users', 'users')}
                                    </span>
                                </div>
                                <p
                                    data-numeric
                                    className="text-[14px] font-semibold text-ink tabular-nums sm:col-span-2 sm:text-right"
                                >
                                    {formatCurrency(
                                        plan.monthly_price,
                                        plan.currency,
                                        platform.currency_symbol,
                                    )}
                                    <span className="ml-1 text-[10.5px] font-normal text-ink-subtle">
                                        {t('mk.pricing.per_month', '/ month')}
                                    </span>
                                </p>
                            </li>
                        ))}
                    </ul>
                ) : (
                    <p className="mt-10 text-[13px] text-ink-muted">
                        {t(
                            'mk.pricing.empty',
                            'Plans are being configured. Talk to us for current pricing.',
                        )}
                    </p>
                )}
            </Section>

            {/* ── 06 Questions ───────────────────────────────────────── */}
            <Section id="faq" bordered>
                <SectionLedger
                    index="07"
                    label={t('mk.home.faq.label', 'Questions')}
                    note={t(
                        'mk.home.faq.note',
                        'Straight answers, including the uncomfortable ones.',
                    )}
                    className="border-t-0 pt-0"
                />
                <div className="mt-7 grid gap-8 lg:grid-cols-12">
                    <DisplayTitle className="lg:col-span-5">
                        {t(
                            'mk.home.faq.title',
                            'The things hospital directors actually ask',
                        )}
                    </DisplayTitle>
                    <div className="lg:col-span-7">
                        <FaqLedger items={faqs.slice(0, 5)} />
                    </div>
                </div>
            </Section>

            <CtaBanner className="pb-20" />
        </PublicLayout>
    );
}
