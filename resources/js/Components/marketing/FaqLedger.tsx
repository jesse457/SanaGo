import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

export interface FaqItem {
    question: string;
    answer: string;
}

/**
 * FaqLedger — questions answered in place.
 *
 * Not an accordion: every answer is already open, set as a two-column ledger
 * so a reader can scan the questions on the left and stop at the one they
 * need. Hiding the answer behind a click saves a page's worth of height and
 * costs the reader the answer.
 */
export function FaqLedger({
    items,
    className,
}: {
    items: FaqItem[];
    className?: string;
}) {
    return (
        <div className={cn('border-t border-line', className)}>
            {items.map((item) => (
                <div
                    key={item.question}
                    className="grid gap-2 border-b border-line py-6 sm:grid-cols-12 sm:gap-8 sm:py-7"
                >
                    <h3 className="text-[14.5px] leading-snug font-semibold tracking-[-0.01em] text-ink sm:col-span-5">
                        {item.question}
                    </h3>
                    <p className="max-w-[62ch] text-[13.5px] leading-relaxed text-ink-muted sm:col-span-7">
                        {item.answer}
                    </p>
                </div>
            ))}
        </div>
    );
}

/**
 * The public pricing FAQ. Kept beside the component because every answer is
 * derived from how this platform actually behaves: 14-day trial from the
 * console, per-hospital isolated databases, FCFA invoicing, annual discount
 * when the operator publishes a yearly price.
 */
export function usePricingFaq(): FaqItem[] {
    const { t } = useTranslation();

    return [
        {
            question: t('mk.faq.trial.q', 'How does the trial work?'),
            answer: t(
                'mk.faq.trial.a',
                'Every new hospital starts on a full working trial — no card, no sales call. When it ends the hospital keeps its data and can move onto any plan; nothing is deleted.',
            ),
        },
        {
            question: t(
                'mk.faq.patient_cap.q',
                'What happens when we reach the patient limit?',
            ),
            answer: t(
                'mk.faq.patient_cap.a',
                'The console warns you as you approach the tier ceiling. Nothing breaks and no record is hidden, but new registrations pause until you move up a tier.',
            ),
        },
        {
            question: t('mk.faq.upgrade.q', 'Can we change plan mid-year?'),
            answer: t(
                'mk.faq.upgrade.a',
                'Yes — up or down, at any point. The change is recorded on the subscription with its own invoice, so the finance desk always has an audit trail.',
            ),
        },
        {
            question: t('mk.faq.data.q', 'Where does our patient data live?'),
            answer: t(
                'mk.faq.data.a',
                'Each hospital gets its own database. Staff on one subdomain cannot reach another hospital’s records even with a valid login, and every privileged action is written to an audit log.',
            ),
        },
        {
            question: t(
                'mk.faq.offline.q',
                'Our power and internet are not reliable. Is that a problem?',
            ),
            answer: t(
                'mk.faq.offline.a',
                'The desk keeps working through short outages and rejoins cleanly. Work that must not be lost — dispensing, payments — is committed to the record, not held in the browser.',
            ),
        },
        {
            question: t('mk.faq.payment.q', 'How do we pay?'),
            answer: t(
                'mk.faq.payment.a',
                'Invoices are raised in FCFA and settled by transfer or mobile money. An annual plan bills once and costs less than twelve monthly payments.',
            ),
        },
    ];
}
