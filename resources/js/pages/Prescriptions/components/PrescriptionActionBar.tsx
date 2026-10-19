import { Button, CardFooter } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

interface PrescriptionActionBarProps {
    processing: boolean;
    cancelHref: string;
}

/**
 * PrescriptionActionBar — the closing row of the prescription form. Submit copy
 * keeps its creating/saving states so the pending post is never ambiguous.
 */
export function PrescriptionActionBar({
    processing,
    cancelHref,
}: PrescriptionActionBarProps) {
    const { t } = useTranslation();

    return (
        <CardFooter className="rounded-xl border border-line bg-surface">
            <Button variant="secondary" href={cancelHref}>
                {t('cancel')}
            </Button>
            <Button type="submit" loading={processing}>
                {processing ? `${t('saving')}...` : t('createPrescription')}
            </Button>
        </CardFooter>
    );
}
