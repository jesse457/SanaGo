import { TrashIcon } from '@heroicons/react/24/outline';
import { Badge, Button, Card, Field, Input, Select } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

/** One prescription line, kept as the flat string shape `useForm` posts. */
export interface PrescriptionLineValues {
    medication_id: string;
    dosage: string;
    frequency: string;
    duration: string;
    quantity: string;
    instructions: string;
}

type LineField = keyof PrescriptionLineValues;

export interface MedicationOption {
    id: number;
    name: string;
    strength?: string;
    dosage_units?: string;
    unit_cost: number;
}

interface PrescriptionLineCardProps {
    index: number;
    values: PrescriptionLineValues;
    medications: MedicationOption[];
    onChange: (field: LineField, value: string) => void;
    onRemove: () => void;
    errorFor: (field: LineField) => string | undefined;
    hasErrors: boolean;
}

/**
 * PrescriptionLineCard — one medication line in the prescription form. Every
 * field name matches the `items.*` payload the controller expects.
 */
export function PrescriptionLineCard({
    index,
    values,
    medications,
    onChange,
    onRemove,
    errorFor,
    hasErrors,
}: PrescriptionLineCardProps) {
    const { t } = useTranslation();

    return (
        <Card
            className={
                hasErrors
                    ? 'border-critical/40 bg-surface-muted'
                    : 'bg-surface-muted'
            }
        >
            <div className="flex items-center justify-between gap-3 border-b border-line px-4 py-2.5">
                <div className="flex items-center gap-2">
                    <h4 className="text-[13px] font-semibold tracking-tight text-ink">
                        {t('medication')} #{index + 1}
                    </h4>
                    {hasErrors && (
                        <Badge tone="critical" dot>
                            {t('error')}
                        </Badge>
                    )}
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    icon={TrashIcon}
                    onClick={onRemove}
                    aria-label={`${t('delete')} ${t('medication')} ${index + 1}`}
                    className="text-critical-ink hover:bg-critical-soft hover:text-critical-ink"
                />
            </div>

            <div className="grid grid-cols-1 gap-x-4 gap-y-4 px-4 py-4 sm:grid-cols-2">
                <Field
                    className="sm:col-span-2"
                    label={t('medication')}
                    htmlFor={`item-${index}-medication`}
                    required
                    error={errorFor('medication_id')}
                >
                    <Select
                        id={`item-${index}-medication`}
                        value={values.medication_id}
                        invalid={Boolean(errorFor('medication_id'))}
                        onChange={(event) =>
                            onChange('medication_id', event.target.value)
                        }
                    >
                        <option value="">{t('selectMedication')}</option>
                        {medications.map((m) => (
                            <option key={m.id} value={String(m.id)}>
                                {m.name} {m.strength ?? ''} (
                                {m.dosage_units ?? ''})
                            </option>
                        ))}
                    </Select>
                </Field>

                <Field
                    label={t('dosage')}
                    htmlFor={`item-${index}-dosage`}
                    required
                    error={errorFor('dosage')}
                >
                    <Input
                        id={`item-${index}-dosage`}
                        value={values.dosage}
                        invalid={Boolean(errorFor('dosage'))}
                        onChange={(event) =>
                            onChange('dosage', event.target.value)
                        }
                        placeholder={t('e.g. 500mg, 10ml')}
                    />
                </Field>

                <Field
                    label={t('frequency')}
                    htmlFor={`item-${index}-frequency`}
                    required
                    error={errorFor('frequency')}
                >
                    <Input
                        id={`item-${index}-frequency`}
                        value={values.frequency}
                        invalid={Boolean(errorFor('frequency'))}
                        onChange={(event) =>
                            onChange('frequency', event.target.value)
                        }
                        placeholder={t('e.g. 3x daily, every 8h')}
                    />
                </Field>

                <Field
                    label={t('duration')}
                    htmlFor={`item-${index}-duration`}
                    required
                    error={errorFor('duration')}
                >
                    <Input
                        id={`item-${index}-duration`}
                        value={values.duration}
                        invalid={Boolean(errorFor('duration'))}
                        onChange={(event) =>
                            onChange('duration', event.target.value)
                        }
                        placeholder={t('e.g. 7 days, 2 weeks')}
                    />
                </Field>

                <Field
                    label={t('quantity')}
                    htmlFor={`item-${index}-quantity`}
                    required
                    error={errorFor('quantity')}
                >
                    <Input
                        id={`item-${index}-quantity`}
                        type="number"
                        min="1"
                        value={values.quantity}
                        invalid={Boolean(errorFor('quantity'))}
                        onChange={(event) =>
                            onChange('quantity', event.target.value)
                        }
                        className="tabular-nums"
                    />
                </Field>

                <Field
                    className="sm:col-span-2"
                    label={t('instructions')}
                    htmlFor={`item-${index}-instructions`}
                    optional
                >
                    <Input
                        id={`item-${index}-instructions`}
                        value={values.instructions}
                        onChange={(event) =>
                            onChange('instructions', event.target.value)
                        }
                        placeholder={t('e.g. Take after meals')}
                    />
                </Field>
            </div>
        </Card>
    );
}
