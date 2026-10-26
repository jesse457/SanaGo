import type { LegalSection } from '@/Components/marketing/LegalDocument';
import { LegalDocument } from '@/Components/marketing/LegalDocument';
import { PageHero } from '@/Components/marketing/PageHero';
import { Section } from '@/Components/marketing/Shell';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import PublicLayout from '@/Layouts/PublicLayout';
import { formatDate } from '@/lib/format';

/**
 * Privacy notice.
 *
 * Deliberately concrete: it names the isolation model, the audit trail, the
 * processors that are actually used and what a hospital can ask for. Anything
 * vaguer would be useless to a hospital director signing a data-handling
 * agreement.
 */
export default function Privacy({ updated_at }: { updated_at: string }) {
    const { t } = useTranslation();
    const { platform } = useMarketing();

    const sections: LegalSection[] = [
        {
            id: 'scope',
            title: t('mk.privacy.scope.title', 'What this notice covers'),
            paragraphs: [
                t(
                    'mk.privacy.scope.p1',
                    'This notice explains how patient and staff data moves through the SanaGo platform, who inside a hospital can see it, and what a hospital can require from us. It covers the public site you are reading and the hospital workspaces served on each hospital’s own address.',
                ),
            ],
        },
        {
            id: 'roles',
            title: t(
                'mk.privacy.roles.title',
                'The hospital decides, we operate',
            ),
            paragraphs: [
                t(
                    'mk.privacy.roles.p1',
                    'For clinical data the hospital is the data controller and SanaGo is the processor: the hospital decides what is collected and who may see it, and we run the system on its behalf. For account and billing data on the platform console, we act as controller.',
                ),
            ],
        },
        {
            id: 'isolation',
            title: t(
                'mk.privacy.isolation.title',
                'One hospital, one database',
            ),
            paragraphs: [
                t(
                    'mk.privacy.isolation.p1',
                    'Every hospital is provisioned with its own database and its own subdomain. There is no shared table holding several hospitals’ patients. A session opened on one hospital’s address cannot read another hospital’s records, even with a valid login.',
                ),
            ],
        },
        {
            id: 'access',
            title: t('mk.privacy.access.title', 'Who can see a record'),
            bullets: [
                t(
                    'mk.privacy.access.b1',
                    'Access is granted per role and per action, not per person and not by sharing a login.',
                ),
                t(
                    'mk.privacy.access.b2',
                    'A doctor sees the patients they treat and the results attached to them; a cashier sees billing lines, not clinical notes.',
                ),
                t(
                    'mk.privacy.access.b3',
                    'Sensitive actions — dispensing, discharge, refunds, exports — are recorded against the user who performed them.',
                ),
                t(
                    'mk.privacy.access.b4',
                    'Platform staff cannot browse clinical records. Support access happens through a hospital-created account or an explicit, logged impersonation by the platform administrator.',
                ),
            ],
        },
        {
            id: 'audit',
            title: t('mk.privacy.audit.title', 'The audit trail'),
            paragraphs: [
                t(
                    'mk.privacy.audit.p1',
                    'Privileged actions on the platform are written to an audit log with the actor, the hospital concerned, the origin address and the payload. A hospital administrator can read the trail for their own hospital; the platform administrator reads platform-level events.',
                ),
            ],
        },
        {
            id: 'processors',
            title: t('mk.privacy.processors.title', 'Third parties involved'),
            bullets: [
                t(
                    'mk.privacy.processors.b1',
                    'Transactional email for account, notification and support messages.',
                ),
                t(
                    'mk.privacy.processors.b2',
                    'SMS delivery over mobile networks for appointment and result notices, when the hospital enables it.',
                ),
                t(
                    'mk.privacy.processors.b3',
                    'The hosting and database infrastructure that stores the hospital’s data, with access limited to platform operators and covered by our agreements with them.',
                ),
            ],
        },
        {
            id: 'retention',
            title: t('mk.privacy.retention.title', 'Retention and export'),
            paragraphs: [
                t(
                    'mk.privacy.retention.p1',
                    'Records are retained for as long as the hospital’s subscription is active. A hospital can request a full export of its data at any time and, when it leaves, receive a final export before its database is decommissioned.',
                ),
                t(
                    'mk.privacy.retention.p2',
                    'Operational logs are kept for a shorter window, sized for support and security review rather than long-term archival.',
                ),
            ],
        },
        {
            id: 'rights',
            title: t(
                'mk.privacy.rights.title',
                'Requests from patients and staff',
            ),
            paragraphs: [
                t(
                    'mk.privacy.rights.p1',
                    'Patients and hospital staff direct access, correction and deletion requests to the hospital that holds their record — that hospital is the controller. We support the hospital in answering them through export and correction tools in the workspace.',
                ),
            ],
        },
        {
            id: 'contact',
            title: t('mk.privacy.contact.title', 'Contact'),
            paragraphs: [
                t(
                    'mk.privacy.contact.p1',
                    'Questions about this notice, a data-handling agreement or an export request go to the platform support address below. If you are a patient, contact the hospital that holds your record first.',
                ),
                platform.support_email,
            ].filter((paragraph): paragraph is string => Boolean(paragraph)),
        },
    ];

    return (
        <PublicLayout>
            <PageHero
                index="05"
                eyebrow={t('mk.privacy.eyebrow', 'Privacy')}
                title={
                    <>
                        {t('mk.privacy.title_a', 'How patient data')}{' '}
                        <em className="text-accent-ink not-italic">
                            {t('mk.privacy.title_b', 'moves through SanaGo')}
                        </em>
                        .
                    </>
                }
                lede={t(
                    'mk.privacy.lede',
                    'A short, specific notice — written for the hospital director who has to sign a data-handling agreement, and the administrator who has to answer a patient.',
                )}
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
