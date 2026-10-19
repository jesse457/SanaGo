import { TrashIcon } from '@heroicons/react/24/outline';
import { Button, Card, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';

/** The batch being edited is always a flat set of strings from the form. */
export interface BatchFormValues {
    batch_number: string;
    expiry_date: string;
    quantity: string;
    purchase_price: string;
    sale_price: string;
    manufactured_date: string;
    notes: string;
}

export const emptyBatch = (): BatchFormValues => ({
    batch_number: '',
    expiry_date: '',
    quantity: '',
    purchase_price: '',
    sale_price: '',
    manufactured_date: '',
    notes: '',
});

interface BatchCardProps {
    index: number;
    values: BatchFormValues;
    onChange: (field: keyof BatchFormValues, value: string) => void;
    onRemove: () => void;
    errorFor: (field: keyof BatchFormValues) => string | undefined;
    hasErrors?: boolean;
}

/**
 * BatchCard — one incoming stock lot. Used by the create form and, in its
 * compact form, by the inventory edit dialog so both read identically.
 */
export function BatchCard({
    index,
    values,
    onChange,
    onRemove,
    errorFor,
    hasErrors = false,
}: BatchCardProps) {
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
                <h4 className="text-[13px] font-semibold tracking-tight text-ink">
                    {t('general.batch')} #{index + 1}
                </h4>
                <Button
                    variant="ghost"
                    size="icon"
                    icon={TrashIcon}
                    aria-label={t('common.delete')}
                    title={t('common.delete')}
                    className="hover:text-critical-ink"
                    onClick={onRemove}
                />
            </div>

            <div className="grid grid-cols-1 gap-x-4 gap-y-3.5 px-4 py-4 sm:grid-cols-2">
                <Field
                    className="sm:col-span-2"
                    label={t('general.batchNumber')}
                    htmlFor={`batch-${index}-number`}
                    required
                    error={errorFor('batch_number')}
                >
                    <Input
                        id={`batch-${index}-number`}
                        value={values.batch_number}
                        invalid={Boolean(errorFor('batch_number'))}
                        placeholder={t('general.egBatchNumber')}
                        onChange={(e) =>
                            onChange('batch_number', e.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('general.expiryDate')}
                    htmlFor={`batch-${index}-expiry`}
                    required
                    error={errorFor('expiry_date')}
                >
                    <Input
                        id={`batch-${index}-expiry`}
                        type="date"
                        value={values.expiry_date}
                        invalid={Boolean(errorFor('expiry_date'))}
                        onChange={(e) =>
                            onChange('expiry_date', e.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('general.manufacturedDate')}
                    htmlFor={`batch-${index}-manufactured`}
                    optional
                >
                    <Input
                        id={`batch-${index}-manufactured`}
                        type="date"
                        value={values.manufactured_date}
                        onChange={(e) =>
                            onChange('manufactured_date', e.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('general.initialQuantity')}
                    htmlFor={`batch-${index}-quantity`}
                    required
                    error={errorFor('quantity')}
                >
                    <Input
                        id={`batch-${index}-quantity`}
                        type="number"
                        min={1}
                        inputMode="numeric"
                        value={values.quantity}
                        invalid={Boolean(errorFor('quantity'))}
                        placeholder={t('general.egOneHundred')}
                        className="tabular-nums"
                        onChange={(e) => onChange('quantity', e.target.value)}
                    />
                </Field>

                <Field
                    label={t('general.purchasePrice')}
                    htmlFor={`batch-${index}-purchase`}
                    required
                    error={errorFor('purchase_price')}
                >
                    <Input
                        id={`batch-${index}-purchase`}
                        type="number"
                        step="0.01"
                        inputMode="decimal"
                        value={values.purchase_price}
                        invalid={Boolean(errorFor('purchase_price'))}
                        placeholder={t('general.zeroPointZeroZero')}
                        className="tabular-nums"
                        onChange={(e) =>
                            onChange('purchase_price', e.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('general.salePrice')}
                    htmlFor={`batch-${index}-sale`}
                    optional
                >
                    <Input
                        id={`batch-${index}-sale`}
                        type="number"
                        step="0.01"
                        inputMode="decimal"
                        value={values.sale_price}
                        placeholder={t('general.zeroPointZeroZero')}
                        className="tabular-nums"
                        onChange={(e) => onChange('sale_price', e.target.value)}
                    />
                </Field>

                <Field
                    className="sm:col-span-2"
                    label={t('general.batchNotes')}
                    htmlFor={`batch-${index}-notes`}
                    optional
                >
                    <Input
                        id={`batch-${index}-notes`}
                        value={values.notes}
                        placeholder={t('general.anyNotesAboutBatch')}
                        onChange={(e) => onChange('notes', e.target.value)}
                    />
                </Field>
            </div>
        </Card>
    );
}

/** A compact single-row stock intake, used by the inventory edit dialog. */
export function BatchQuickAddForm({
    values,
    onChange,
    onSubmit,
    onCancel,
    processing,
}: {
    values: BatchFormValues;
    onChange: (field: keyof BatchFormValues, value: string) => void;
    onSubmit: () => void;
    onCancel: () => void;
    processing: boolean;
}) {
    const { t } = useTranslation();

    return (
        <div className="rounded-lg border border-line bg-surface-muted p-4">
            <div className="grid grid-cols-1 gap-x-4 gap-y-3.5 sm:grid-cols-2 lg:grid-cols-3">
                <Field label={t('general.batchNumberShort')} required>
                    <Input
                        value={values.batch_number}
                        placeholder={t('general.ref001')}
                        onChange={(e) =>
                            onChange('batch_number', e.target.value)
                        }
                    />
                </Field>
                <Field label={t('general.expiry')} required>
                    <Input
                        type="date"
                        value={values.expiry_date}
                        onChange={(e) =>
                            onChange('expiry_date', e.target.value)
                        }
                    />
                </Field>
                <Field label={t('general.manufacturedDate')} optional>
                    <Input
                        type="date"
                        value={values.manufactured_date}
                        onChange={(e) =>
                            onChange('manufactured_date', e.target.value)
                        }
                    />
                </Field>
                <Field label={t('general.qty')} required>
                    <Input
                        type="number"
                        min={1}
                        inputMode="numeric"
                        value={values.quantity}
                        placeholder="0"
                        className="tabular-nums"
                        onChange={(e) => onChange('quantity', e.target.value)}
                    />
                </Field>
                <Field label={t('general.purchasePrice')}>
                    <Input
                        type="number"
                        step="0.01"
                        inputMode="decimal"
                        value={values.purchase_price}
                        placeholder="0.00"
                        className="tabular-nums"
                        onChange={(e) =>
                            onChange('purchase_price', e.target.value)
                        }
                    />
                </Field>
                <Field label={t('general.salePrice')} optional>
                    <Input
                        type="number"
                        step="0.01"
                        inputMode="decimal"
                        value={values.sale_price}
                        placeholder="0.00"
                        className="tabular-nums"
                        onChange={(e) => onChange('sale_price', e.target.value)}
                    />
                </Field>
            </div>

            <div className="mt-3.5 flex flex-wrap items-center justify-end gap-2">
                <Button variant="secondary" size="sm" onClick={onCancel}>
                    {t('common.cancel')}
                </Button>
                <Button
                    variant="accent"
                    size="sm"
                    loading={processing}
                    onClick={onSubmit}
                >
                    {t('general.add')}
                </Button>
            </div>
        </div>
    );
}
