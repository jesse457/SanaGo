import { ArrowRightIcon, CheckIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import type { AppScreenKey } from '@/Components/marketing/AppWindow';
import { AppWindow } from '@/Components/marketing/AppWindow';
import Button from '@/Components/marketing/Button';
import { CtaBanner } from '@/Components/marketing/CtaBanner';
import { EditorialPhoto } from '@/Components/marketing/EditorialPhoto';
import { PageHero } from '@/Components/marketing/PageHero';
import { RoleConsole } from '@/Components/marketing/RoleConsole';
import {
    DisplayTitle,
    Lede,
    MicroLabel,
    SectionLedger,
} from '@/Components/marketing/SectionLedger';
import { Section } from '@/Components/marketing/Shell';
import { WorkflowRail } from '@/Components/marketing/WorkflowRail';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';

interface CapabilityGroup {
    key: string;
    title: string;
    intro: string;
    keys: string[];
    /** The workspace screen that group's work actually happens on. */
    screen: AppScreenKey;
}

/**
 * The module catalogue.
 *
 * Every row is one capability key the platform gates (`App\Enums\PlanFeature`)
 * plus the published plans that unlock it. The copy explains the work; the
 * console decides who gets it — so this page cannot promise a module that does
 * not exist or forget one that does.
 */
export default function Features() {
    const { t } = useTranslation();
    const { capabilities, plans, stats } = useMarketing();
    const [activeRole, setActiveRole] = useState('doctor');

    const copy: Record<string, { title: string; body: string }> = {
        patients: {
            title: t('mk.cap.patients.title', 'Patient records'),
            body: t(
                'mk.cap.patients.body',
                'One file per patient, opened at the front desk and never re-typed: photo, guardian, blood group, allergies, insurer and the full visit history behind a single search box.',
            ),
        },
        appointments: {
            title: t('mk.cap.appointments.title', 'Appointments & queue'),
            body: t(
                'mk.cap.appointments.body',
                'Book by desk, doctor or station. The hall list shows who is waiting, who is in consultation and who has not arrived.',
            ),
        },
        consultations: {
            title: t('mk.cap.consultations.title', 'Consultations'),
            body: t(
                'mk.cap.consultations.body',
                'Structured clinical notes — presenting complaint, examination by system, diagnosis, treatment plan and follow-up — attached to the visit.',
            ),
        },
        admissions: {
            title: t('mk.cap.admissions.title', 'Admissions & wards'),
            body: t(
                'mk.cap.admissions.body',
                'Admit, transfer a bed, discharge with a summary. Occupancy is live, so the ward knows what is free before the patient arrives.',
            ),
        },
        vitals: {
            title: t('mk.cap.vitals.title', 'Vitals & nursing'),
            body: t(
                'mk.cap.vitals.body',
                'Temperature, blood pressure, pulse, oxygen saturation, respiration and weight, with abnormal readings flagged at the moment of entry.',
            ),
        },
        laboratory: {
            title: t('mk.cap.laboratory.title', 'Laboratory'),
            body: t(
                'mk.cap.laboratory.body',
                'Requests, specimens and results entered against the test definition, then validated and released to the doctor who ordered them.',
            ),
        },
        radiology: {
            title: t('mk.cap.radiology.title', 'Radiology & imaging'),
            body: t(
                'mk.cap.radiology.body',
                'Orders by modality — X-ray, ultrasound, CT, MRI — with findings, a conclusion and a signed report archive.',
            ),
        },
        pharmacy: {
            title: t('mk.cap.pharmacy.title', 'Pharmacy & dispensing'),
            body: t(
                'mk.cap.pharmacy.body',
                'Dispense against the prescription, batch by batch, with expiry and low-stock warnings before the shelf is empty.',
            ),
        },
        inventory: {
            title: t('mk.cap.inventory.title', 'Inventory & supplies'),
            body: t(
                'mk.cap.inventory.body',
                'Non-drug stock — consumables, PPE, IV fluids, wound care — with reorder thresholds, usage and current inventory value.',
            ),
        },
        surgery: {
            title: t('mk.cap.surgery.title', 'Surgery'),
            body: t(
                'mk.cap.surgery.body',
                'Theatre schedule with surgeon, anaesthetist, procedure, fee and complications recorded against the patient.',
            ),
        },
        emergency: {
            title: t('mk.cap.emergency.title', 'Emergency'),
            body: t(
                'mk.cap.emergency.body',
                'Register an arrival with triage level, arrival mode, assessment and disposition — before a full file exists.',
            ),
        },
        billing: {
            title: t('mk.cap.billing.title', 'Billing & invoicing'),
            body: t(
                'mk.cap.billing.body',
                'Invoices with tariff lines, per-doctor pricing, partial payments and the balance carried on the episode.',
            ),
        },
        cash_register: {
            title: t('mk.cap.cash_register.title', 'Cashier (Caisse)'),
            body: t(
                'mk.cap.cash_register.body',
                'Open a shift, take payments, close and reconcile against a counted drawer. Each shift keeps its own ledger.',
            ),
        },
        insurance_claims: {
            title: t('mk.cap.claims.title', 'Insurance & tiers payant'),
            body: t(
                'mk.cap.claims.body',
                'Patient policies, beneficiaries and insurer claims with the supporting lines the payer will ask for.',
            ),
        },
        certificates: {
            title: t('mk.cap.certificates.title', 'Medical certificates'),
            body: t(
                'mk.cap.certificates.body',
                'Sick leave, fitness and death certificates issued from the record, numbered and printable.',
            ),
        },
        reports: {
            title: t('mk.cap.reports.title', 'Reports & analytics'),
            body: t(
                'mk.cap.reports.body',
                'Revenue by period and service, activity across desks, and the figures the board asks for — without a spreadsheet export.',
            ),
        },
        audit_logs: {
            title: t('mk.cap.audit.title', 'Audit log'),
            body: t(
                'mk.cap.audit.body',
                'Who opened, changed or exported a record, and when. Visible to the administrator, not to everyone.',
            ),
        },
        sms_notifications: {
            title: t('mk.cap.sms.title', 'SMS notifications'),
            body: t(
                'mk.cap.sms.body',
                'Appointment reminders and result notices over MTN and Orange numbers, with email as the fallback channel.',
            ),
        },
        api_access: {
            title: t('mk.cap.api.title', 'API access'),
            body: t(
                'mk.cap.api.body',
                'Machine-to-machine access for a billing system, an analyser or a national reporting feed.',
            ),
        },
        custom_branding: {
            title: t('mk.cap.branding.title', 'Custom branding'),
            body: t(
                'mk.cap.branding.body',
                'The hospital’s own name, mark and subdomain, instead of a generic tenant address.',
            ),
        },
    };

    const groups: CapabilityGroup[] = [
        {
            key: 'clinical',
            title: t('mk.features.g1', 'The clinical core'),
            intro: t(
                'mk.features.g1_intro',
                'The five capabilities every hospital touches on its first day.',
            ),
            keys: [
                'patients',
                'appointments',
                'consultations',
                'vitals',
                'admissions',
            ],
            screen: 'patients',
        },
        {
            key: 'diagnostics',
            title: t('mk.features.g2', 'Diagnostics'),
            intro: t(
                'mk.features.g2_intro',
                'Requests leave the consultation with the patient already attached.',
            ),
            keys: ['laboratory', 'radiology'],
            screen: 'laboratory',
        },
        {
            key: 'stock',
            title: t('mk.features.g3', 'Pharmacy and stock'),
            intro: t(
                'mk.features.g3_intro',
                'Every box that leaves the shelf leaves a trace.',
            ),
            keys: ['pharmacy', 'inventory'],
            screen: 'pharmacy',
        },
        {
            key: 'money',
            title: t('mk.features.g4', 'Money'),
            intro: t(
                'mk.features.g4_intro',
                'The end of the episode: invoice, drawer, insurer.',
            ),
            keys: [
                'billing',
                'cash_register',
                'insurance_claims',
                'certificates',
            ],
            screen: 'caisse',
        },
        {
            key: 'operations',
            title: t('mk.features.g5', 'Operations and platform'),
            intro: t(
                'mk.features.g5_intro',
                'Theatre, emergencies, reporting and the machinery underneath.',
            ),
            keys: [
                'surgery',
                'emergency',
                'reports',
                'audit_logs',
                'sms_notifications',
                'api_access',
                'custom_branding',
            ],
            screen: 'wards',
        },
    ];

    const plansFor = (key: string): string[] =>
        plans
            .filter((plan) => plan.capabilities[key] === true)
            .map((plan) => plan.name);

    const resolve = (key: string) =>
        capabilities.find((capability) => capability.key === key) ?? {
            key,
            label: key.replace(/_/g, ' '),
            description: '',
        };

    return (
        <PublicLayout>
            <PageHero
                index="01"
                eyebrow={t('mk.features.eyebrow', 'Module catalogue')}
                title={
                    <>
                        {t('mk.features.title_a', 'Every module')}{' '}
                        <em className="text-accent-ink not-italic">
                            {t('mk.features.title_b', 'the platform gates')}
                        </em>
                        .
                    </>
                }
                lede={t(
                    'mk.features.lede',
                    'This page is generated from the platform’s own capability list and the plans an operator published — so it cannot promise a module that does not exist, or forget one that does.',
                )}
                actions={
                    <>
                        <Button
                            href="/contact"
                            size="lg"
                            trailingIcon={ArrowRightIcon}
                        >
                            {t('mk.features.primary', 'See it on your wards')}
                        </Button>
                        <Button href="/pricing" variant="secondary" size="lg">
                            {t('mk.features.secondary', 'Which plan has what')}
                        </Button>
                    </>
                }
                footnotes={[
                    {
                        label: t('mk.features.f_modules', 'Capabilities'),
                        value: String(stats.capabilities),
                    },
                    {
                        label: t('mk.features.f_plans', 'Published plans'),
                        value: String(stats.plans),
                    },
                    {
                        label: t('mk.features.f_desks', 'Role consoles'),
                        value: '07',
                    },
                    {
                        label: t('mk.features.f_trial', 'Trial'),
                        value: `${stats.trial_days} d`,
                    },
                ]}
            />

            <Section tone="muted" bordered>
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-10">
                    <nav
                        aria-label={t('mk.features.index', 'Catalogue index')}
                        className="lg:col-span-3"
                    >
                        <div className="lg:sticky lg:top-24">
                            <MicroLabel>
                                {t('mk.features.index', 'Catalogue index')}
                            </MicroLabel>
                            <ol className="mt-4">
                                {groups.map((group, index) => (
                                    <li key={group.key}>
                                        <a
                                            href={`#${group.key}`}
                                            className="flex items-baseline gap-3 border-t border-line py-2.5 text-[12.5px] text-ink-muted transition-colors duration-150 hover:text-ink"
                                        >
                                            <span
                                                data-numeric
                                                className="font-mono text-[9.5px] tracking-[0.14em] text-ink-subtle tabular-nums"
                                            >
                                                {String(index + 1).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </span>
                                            {group.title}
                                        </a>
                                    </li>
                                ))}
                            </ol>
                        </div>
                    </nav>

                    <div className="flex flex-col gap-16 lg:col-span-9">
                        {groups.map((group, index) => (
                            <div
                                key={group.key}
                                id={group.key}
                                className="scroll-mt-28"
                            >
                                <SectionLedger
                                    index={String(index + 1).padStart(2, '0')}
                                    label={group.title}
                                    note={group.intro}
                                    className="border-t-0 pt-0"
                                />
                                <div className="mt-6 grid items-start gap-8 lg:grid-cols-12">
                                    <ul className="lg:col-span-7">
                                        {group.keys.map((key) => {
                                            const capability = resolve(key);
                                            const entry = copy[key];
                                            const included = plansFor(key);

                                            return (
                                                <li
                                                    key={key}
                                                    className="border-b border-line py-4 first:border-t"
                                                >
                                                    <div className="flex flex-wrap items-baseline justify-between gap-2">
                                                        <h3 className="text-[14px] font-semibold text-ink">
                                                            {entry?.title ??
                                                                capability.label}
                                                        </h3>
                                                        <p className="font-mono text-[9px] tracking-[0.12em] text-ink-subtle uppercase">
                                                            {included.length > 0
                                                                ? included.join(
                                                                      ' · ',
                                                                  )
                                                                : t(
                                                                      'mk.features.not_in_plan',
                                                                      'Not in a published plan',
                                                                  )}
                                                        </p>
                                                    </div>
                                                    <p className="mt-1.5 max-w-[60ch] text-[12.5px] leading-relaxed text-ink-muted">
                                                        {entry?.body ?? ''}
                                                    </p>
                                                </li>
                                            );
                                        })}
                                    </ul>

                                    <div className="lg:sticky lg:top-24 lg:col-span-5">
                                        <AppWindow
                                            screen={group.screen}
                                            density="compact"
                                        />
                                        <p className="mt-3 flex items-center gap-2 font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                                            <CheckIcon className="h-3 w-3 text-accent" />
                                            {t(
                                                'mk.features.gated_note',
                                                'Gated by plan capability · drawn from the working UI kit',
                                            )}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </Section>

            {/* Patient journey anchor, linked from the home page */}
            <Section id="workflow" bordered>
                <SectionLedger
                    index="W"
                    label={t('mk.home.workflow.label', 'Patient journey')}
                    note={t(
                        'mk.features.workflow_note',
                        'What the capabilities above look like end to end.',
                    )}
                    className="border-t-0 pt-0"
                />
                <DisplayTitle className="mt-6 max-w-[24ch]">
                    {t('mk.features.workflow_title', 'Six rooms, one chart')}
                </DisplayTitle>
                <WorkflowRail className="mt-10" />
            </Section>

            {/* Role consoles anchor, linked from the home page */}
            <Section id="roles" tone="muted" bordered>
                <SectionLedger
                    index="R"
                    label={t('mk.home.roles.label', 'Role consoles')}
                    note={t(
                        'mk.features.roles_note',
                        'The same capabilities, arranged by the desk that uses them.',
                    )}
                    className="border-t-0 pt-0"
                />
                <DisplayTitle className="mt-6 max-w-[24ch]">
                    {t(
                        'mk.features.roles_title',
                        'A console per desk, not a password per person',
                    )}
                </DisplayTitle>
                <RoleConsole
                    activeRole={activeRole}
                    onRoleChange={setActiveRole}
                    className="mt-10"
                />
            </Section>

            {/* Photography: the rooms these modules describe */}
            <Section bordered>
                <div className="grid gap-8 lg:grid-cols-12 lg:items-end">
                    <div className="lg:col-span-6">
                        <SectionLedger
                            index="P"
                            label={t('mk.features.rooms', 'Rooms')}
                            className="border-t-0 pt-0"
                        />
                        <DisplayTitle className="mt-6">
                            {t(
                                'mk.features.rooms_title',
                                'Where these screens get used',
                            )}
                        </DisplayTitle>
                        <Lede className="mt-4">
                            {t(
                                'mk.features.rooms_body',
                                'A theatre list, a ward round and a dispensary shelf: three places a hospital loses hours every week, and three places SanaGo is designed to give them back.',
                            )}
                        </Lede>
                    </div>
                    <div className="grid grid-cols-2 gap-4 lg:col-span-6">
                        <EditorialPhoto
                            src="/images/marketing/theatre.jpg"
                            alt="An operating theatre prepared for surgery"
                            caption={t('mk.features.room_theatre', 'Theatre')}
                            ratio="tall"
                        />
                        <EditorialPhoto
                            src="/images/marketing/ward-beds.jpg"
                            alt="A hospital ward with prepared beds"
                            caption={t('mk.features.room_ward', 'Ward')}
                            ratio="tall"
                            className="lg:mt-10"
                        />
                    </div>
                </div>
            </Section>

            <CtaBanner className="pb-20" />
        </PublicLayout>
    );
}
