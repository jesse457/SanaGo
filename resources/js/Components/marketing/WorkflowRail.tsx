import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface Step {
    key: string;
    title: string;
    description: string;
}

/**
 * WorkflowRail — the patient journey, as the product actually runs it.
 *
 * Six numbered stations on one hairline rail. This is the argument the whole
 * site makes: not "modules", but one continuous record that survives every
 * hand-off between the door and the receipt.
 */
export function WorkflowRail({ className }: { className?: string }) {
    const { t } = useTranslation();

    const steps: Step[] = [
        {
            key: 'register',
            title: t('mk.workflow.register.title', 'Register'),
            description: t(
                'mk.workflow.register.body',
                'A patient file is opened once — photo, guardian, blood group, insurer — and every later visit attaches to it.',
            ),
        },
        {
            key: 'triage',
            title: t('mk.workflow.triage.title', 'Triage & queue'),
            description: t(
                'mk.workflow.triage.body',
                'Vitals at the door, a position in the queue per station, and a waiting list the front desk can see.',
            ),
        },
        {
            key: 'consult',
            title: t('mk.workflow.consult.title', 'Consult'),
            description: t(
                'mk.workflow.consult.body',
                'Structured notes, diagnosis, prescription and the next appointment, written into the same record.',
            ),
        },
        {
            key: 'diagnose',
            title: t('mk.workflow.diagnose.title', 'Diagnostics'),
            description: t(
                'mk.workflow.diagnose.body',
                'Lab and imaging requests leave the consultation already attached to the patient and the doctor who ordered them.',
            ),
        },
        {
            key: 'ward',
            title: t('mk.workflow.ward.title', 'Dispense & admit'),
            description: t(
                'mk.workflow.ward.body',
                'Pharmacy deducts the batch it dispensed; the ward assigns a real bed and the deposit lands on the account.',
            ),
        },
        {
            key: 'settle',
            title: t('mk.workflow.settle.title', 'Bill & settle'),
            description: t(
                'mk.workflow.settle.body',
                'One invoice per episode, a cash shift that reconciles, and an insurer claim with its supporting lines.',
            ),
        },
    ];

    return (
        <ol
            className={cn(
                'grid gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-6 lg:gap-x-4',
                className,
            )}
        >
            {steps.map((step, index) => (
                <li
                    key={step.key}
                    className="group relative border-t border-line pt-4 transition-colors duration-300 ease-premium hover:border-line-strong"
                >
                    <span
                        aria-hidden
                        className="absolute -top-px left-0 h-px w-8 bg-accent opacity-0 transition-opacity duration-300 ease-premium group-hover:opacity-100"
                    />
                    <p
                        data-numeric
                        className="font-mono text-[10.5px] tracking-[0.18em] text-accent-ink tabular-nums"
                    >
                        {String(index + 1).padStart(2, '0')}
                    </p>
                    <h3 className="mt-3 text-[14px] leading-tight font-semibold tracking-[-0.01em] text-ink">
                        {step.title}
                    </h3>
                    <p className="mt-2 text-[12.5px] leading-relaxed text-ink-muted">
                        {step.description}
                    </p>
                </li>
            ))}
        </ol>
    );
}
