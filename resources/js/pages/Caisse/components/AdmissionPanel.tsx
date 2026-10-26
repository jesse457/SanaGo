import { HomeModernIcon, PlusIcon } from '@heroicons/react/24/outline';
import { Badge, Button } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { CatalogueItem, OngoingAdmission } from './types';

function money(value: number): string {
    return `${Math.round(value).toLocaleString('fr-FR')} FCFA`;
}

interface AdmissionPanelProps {
    admission: OngoingAdmission;
    picked: string[];
    disabled?: boolean;
    onPick: (item: CatalogueItem) => void;
}

/**
 * AdmissionPanel — the stay the patient is in right now.
 *
 * Detected from the open admission, never typed: the ward and bed the patient
 * occupies, how many nights the stay has run, and the bed-day price the
 * catalogue holds for that bed type. One click puts the stay on the bill.
 */
export function AdmissionPanel({
    admission,
    picked,
    disabled = false,
    onPick,
}: AdmissionPanelProps) {
    const { t } = useTranslation();

    const stay = [admission.ward, admission.bed, admission.bed_type].filter(
        (part): part is string => Boolean(part),
    );

    return (
        <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3">
            <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="flex items-center gap-2 text-[12.5px] font-semibold text-ink">
                        <HomeModernIcon className="h-4 w-4 shrink-0 text-ink-subtle" />
                        {t('page.caisse.admitted', 'Currently admitted')}
                        <Badge tone="warning" size="sm">
                            {t('page.caisse.night_count', {
                                count: admission.nights,
                            })}
                        </Badge>
                    </p>
                    <p className="mt-1 truncate font-mono text-[11px] text-ink-subtle">
                        {[...stay, admission.admitted_at, admission.doctor]
                            .filter(Boolean)
                            .join(' · ')}
                    </p>
                    {admission.reason && (
                        <p className="mt-1 line-clamp-1 text-[11.5px] text-ink-muted">
                            {admission.reason}
                        </p>
                    )}
                </div>
            </div>

            {admission.suggestions.length > 0 && (
                <ul className="mt-3 space-y-1.5">
                    {admission.suggestions.map((item) => {
                        const alreadyPicked = picked.includes(item.key);

                        return (
                            <li
                                key={item.key}
                                className="flex items-center justify-between gap-3 rounded-md bg-surface px-3 py-2"
                            >
                                <span className="min-w-0">
                                    <span className="block truncate text-[12.5px] font-medium text-ink">
                                        {item.name}
                                    </span>
                                    <span className="block text-[10.5px] text-ink-subtle tabular-nums">
                                        {item.note ??
                                            `${item.quantity} × ${money(item.price)}`}
                                    </span>
                                </span>
                                <span className="flex shrink-0 items-center gap-2">
                                    <span className="text-[12.5px] font-semibold text-ink tabular-nums">
                                        {money(item.price * item.quantity)}
                                    </span>
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant={
                                            alreadyPicked
                                                ? 'ghost'
                                                : 'secondary'
                                        }
                                        icon={PlusIcon}
                                        disabled={disabled || alreadyPicked}
                                        onClick={() => onPick(item)}
                                    >
                                        {alreadyPicked
                                            ? t('page.caisse.added', 'Added')
                                            : t('page.caisse.add', 'Add')}
                                    </Button>
                                </span>
                            </li>
                        );
                    })}
                </ul>
            )}
        </div>
    );
}
