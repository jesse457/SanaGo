import { ArrowLeftIcon } from '@heroicons/react/24/outline';
import { Button } from '@/Components/ui';
import type { Translate } from './types';

interface SurgeryFormActionsProps {
    isEdit: boolean;
    processing: boolean;
    t: Translate;
}

/**
 * SurgeryFormActions — the footer row of the surgery form. Cancel returns to
 * the index, submit carries the scheduling / updating verb.
 */
export function SurgeryFormActions({
    isEdit,
    processing,
    t,
}: SurgeryFormActionsProps) {
    return (
        <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-5">
            <Button
                href={route('surgeries.index')}
                variant="secondary"
                icon={ArrowLeftIcon}
            >
                {t('common.cancel')}
            </Button>

            <Button type="submit" variant="primary" loading={processing}>
                {processing
                    ? isEdit
                        ? t('surgeries.create.updating')
                        : t('surgeries.create.scheduling')
                    : isEdit
                      ? t('surgeries.create.update_surgery_button')
                      : t('surgeries.create.schedule_surgery_button')}
            </Button>
        </div>
    );
}
