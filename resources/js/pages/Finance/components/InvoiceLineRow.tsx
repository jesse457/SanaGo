import { TrashIcon } from '@heroicons/react/24/outline';
import { Button, Field, Input, Select } from '@/Components/ui';
import { formatMoney } from '@/pages/Finance/components/format';

export interface InvoiceLineDraft {
    item_type: string;
    description: string;
    quantity: number;
    unit_price: number;
}

export const INVOICE_ITEM_TYPE_OPTIONS = [
    'consultation',
    'lab_test',
    'radiology',
    'surgery',
    'medication',
    'supply',
    'admission',
    'emergency',
    'other',
].map((value) => ({ value, label: value.replace(/_/g, ' ') }));

interface InvoiceLineRowProps {
    item: InvoiceLineDraft;
    index: number;
    labels: {
        type: string;
        description: string;
        quantity: string;
        unitPrice: string;
        remove: string;
    };
    descriptionPlaceholder?: string;
    canRemove: boolean;
    onChange: (
        index: number,
        field: keyof InvoiceLineDraft,
        value: string | number,
    ) => void;
    onRemove: (index: number) => void;
}

/**
 * InvoiceLineRow — one editable invoice line: type, description, quantity, unit
 * price and the computed line total. Lives inside the invoice builder's
 * line-items table.
 */
export function InvoiceLineRow({
    item,
    index,
    labels,
    descriptionPlaceholder,
    canRemove,
    onChange,
    onRemove,
}: InvoiceLineRowProps) {
    const lineTotal =
        (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);

    return (
        <div className="grid grid-cols-1 gap-3 rounded-lg border border-line bg-surface-muted p-3 sm:grid-cols-2 md:grid-cols-12 md:items-end">
            <div className="md:col-span-2">
                <Field label={labels.type} htmlFor={`items.${index}.item_type`}>
                    <Select
                        id={`items.${index}.item_type`}
                        value={item.item_type}
                        onChange={(event) =>
                            onChange(index, 'item_type', event.target.value)
                        }
                    >
                        {INVOICE_ITEM_TYPE_OPTIONS.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </Select>
                </Field>
            </div>

            <div className="md:col-span-4">
                <Field
                    label={labels.description}
                    htmlFor={`items.${index}.description`}
                >
                    <Input
                        id={`items.${index}.description`}
                        value={item.description}
                        placeholder={descriptionPlaceholder}
                        onChange={(event) =>
                            onChange(index, 'description', event.target.value)
                        }
                    />
                </Field>
            </div>

            <div className="md:col-span-2">
                <Field
                    label={labels.quantity}
                    htmlFor={`items.${index}.quantity`}
                >
                    <Input
                        id={`items.${index}.quantity`}
                        type="number"
                        min="1"
                        className="tabular-nums"
                        value={item.quantity}
                        onChange={(event) =>
                            onChange(
                                index,
                                'quantity',
                                parseInt(event.target.value, 10) || 0,
                            )
                        }
                    />
                </Field>
            </div>

            <div className="md:col-span-2">
                <Field
                    label={labels.unitPrice}
                    htmlFor={`items.${index}.unit_price`}
                >
                    <Input
                        id={`items.${index}.unit_price`}
                        type="number"
                        min="0"
                        step="1"
                        className="tabular-nums"
                        value={item.unit_price}
                        onChange={(event) =>
                            onChange(
                                index,
                                'unit_price',
                                parseFloat(event.target.value) || 0,
                            )
                        }
                    />
                </Field>
            </div>

            <div className="flex items-center justify-between gap-2 md:col-span-2 md:justify-end">
                <span className="text-[13px] font-medium text-ink tabular-nums md:hidden">
                    {formatMoney(lineTotal)}
                </span>
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label={labels.remove}
                    disabled={!canRemove}
                    onClick={() => onRemove(index)}
                >
                    <TrashIcon className="h-4 w-4" />
                </Button>
            </div>
        </div>
    );
}
