import { CheckIcon } from '@heroicons/react/24/outline';
import { Badge, Button } from '@/Components/ui';
import type { Translate } from './types';

interface FormActionBarProps {
    processing: boolean;
    conflictCount: number;
    missingComplaint: boolean;
    missingDiagnosis: boolean;
    t: Translate;
}

/**
 * FormActionBar — the sticky summary strip that closes the consultation form.
 * Keeps the save affordance reachable from every tab while surfacing what still
 * blocks a submission.
 */
export function FormActionBar({
    processing,
    conflictCount,
    missingComplaint,
    missingDiagnosis,
    t,
}: FormActionBarProps) {
    const blockers = [
        missingComplaint ? t('chiefComplaint') : null,
        missingDiagnosis ? t('diagnosis') : null,
    ].filter(Boolean) as string[];

    return (
        <div className="chrome-blur sticky bottom-0 z-20 -mx-4 mt-4 border-t border-line px-4 py-3 sm:-mx-6 sm:px-6">
            <div className="flex flex-wrap items-center gap-2">
                {conflictCount > 0 && (
                    <Badge tone="critical" dot>
                        {conflictCount} {t('Drug-Allergy Interaction Warning')}
                    </Badge>
                )}

                {blockers.length > 0 && (
                    <span className="text-[12px] text-ink-subtle">
                        {t('requiredBeforeSaving')}:{' '}
                        <span className="font-medium text-ink-muted">
                            {blockers.join(' · ')}
                        </span>
                    </span>
                )}

                <div className="ml-auto flex items-center gap-2">
                    <Button
                        type="submit"
                        variant="primary"
                        icon={CheckIcon}
                        loading={processing}
                    >
                        {processing ? t('processing') : t('saveRecord')}
                    </Button>
                </div>
            </div>
        </div>
    );
}
