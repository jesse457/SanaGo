import {
    AdjustmentsHorizontalIcon,
    ArchiveBoxArrowDownIcon,
    CalendarDaysIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Badge, Button, Card, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

/** One incoming stock lot: the flat, string-literal shape the form expects. */
export interface CreateBatchValues {
    batch_number: string;
    expiry_date: string;
    quantity: string;
    purchase_price: string;
    sale_price: string;
    manufactured_date: string;
    notes: string;
}

type BatchField = keyof CreateBatchValues;

interface CreateBatchCardProps {
    index: number;
    values: CreateBatchValues;
    onChange: (field: BatchField, value: string) => void;
    onRemove: () => void;
    errorFor: (field: BatchField) => string | undefined;
    hasErrors: boolean;
}

function PriceField({
    id,
    label,
    value,
    onChange,
    placeholder,
    required,
    optional,
    error,
}: {
    id: string;
    label: string;
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    required?: boolean;
    optional?: boolean;
    error?: string;
}) {
    return (
        <Field
            label={label}
            htmlFor={id}
            required={required}
            optional={optional}
            error={error}
        >
            <div className="relative">
                <span
                    aria-hidden="true"
                    className="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-[13px] font-medium text-ink-subtle"
                >
                    $
                </span>
                <Input
                    id={id}
                    type="number"
                    step="0.01"
                    value={value}
                    invalid={Boolean(error)}
                    onChange={(event) => onChange(event.target.value)}
                    placeholder={placeholder}
                    className="pl-7 tabular-nums"
                />
            </div>
        </Field>
    );
}

/**
 * CreateBatchCard — one editable stock lot inside the drug create form.
 * Numbers stay tabular, the error state is carried by the card hairline.
 */
export function CreateBatchCard({
    index,
    values,
    onChange,
    onRemove,
    errorFor,
    hasErrors,
}: CreateBatchCardProps) {
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
                        {t('batch')} #{index + 1}
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
                    aria-label={`${t('delete')} ${t('batch')} ${index + 1}`}
                    className="text-critical-ink hover:bg-critical-soft hover:text-critical-ink"
                />
            </div>

            <div className="grid grid-cols-1 gap-x-4 gap-y-4 px-4 py-4 sm:grid-cols-2">
                <Field
                    className="sm:col-span-2"
                    label={t('batchNumber')}
                    htmlFor={`batch-${index}-number`}
                    required
                    error={errorFor('batch_number')}
                >
                    <Input
                        id={`batch-${index}-number`}
                        value={values.batch_number}
                        invalid={Boolean(errorFor('batch_number'))}
                        onChange={(event) =>
                            onChange('batch_number', event.target.value)
                        }
                        placeholder={t('egBatchNumber')}
                    />
                </Field>

                <Field
                    label={t('expiryDate')}
                    htmlFor={`batch-${index}-expiry`}
                    required
                    error={errorFor('expiry_date')}
                >
                    <Input
                        id={`batch-${index}-expiry`}
                        type="date"
                        icon={CalendarDaysIcon}
                        value={values.expiry_date}
                        invalid={Boolean(errorFor('expiry_date'))}
                        onChange={(event) =>
                            onChange('expiry_date', event.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('initialQuantity')}
                    htmlFor={`batch-${index}-quantity`}
                    required
                    error={errorFor('quantity')}
                >
                    <Input
                        id={`batch-${index}-quantity`}
                        type="number"
                        min="1"
                        icon={ArchiveBoxArrowDownIcon}
                        value={values.quantity}
                        invalid={Boolean(errorFor('quantity'))}
                        onChange={(event) =>
                            onChange('quantity', event.target.value)
                        }
                        placeholder={t('egOneHundred')}
                        className="tabular-nums"
                    />
                </Field>

                <PriceField
                    id={`batch-${index}-purchase`}
                    label={t('purchasePrice')}
                    value={values.purchase_price}
                    onChange={(value) => onChange('purchase_price', value)}
                    placeholder={t('zeroPointZeroZero')}
                    required
                    error={errorFor('purchase_price')}
                />

                <PriceField
                    id={`batch-${index}-sale`}
                    label={t('salePrice')}
                    value={values.sale_price}
                    onChange={(value) => onChange('sale_price', value)}
                    placeholder={t('zeroPointZeroZero')}
                    optional
                />

                <Field
                    label={t('manufacturedDate')}
                    htmlFor={`batch-${index}-manufactured`}
                >
                    <Input
                        id={`batch-${index}-manufactured`}
                        type="date"
                        icon={PencilSquareIcon}
                        value={values.manufactured_date}
                        onChange={(event) =>
                            onChange('manufactured_date', event.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('batchNotes')}
                    htmlFor={`batch-${index}-notes`}
                    optional
                    className="sm:col-span-2"
                >
                    <Input
                        id={`batch-${index}-notes`}
                        icon={AdjustmentsHorizontalIcon}
                        value={values.notes}
                        onChange={(event) =>
                            onChange('notes', event.target.value)
                        }
                        placeholder={t('anyNotesAboutBatch')}
                    />
                </Field>
            </div>
        </Card>
    );
}
