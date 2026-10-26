import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import type { FormEvent } from 'react';
import { Button, Dialog, Field, Input } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { BillLine } from './types';

interface EditChargeDialogProps {
    line: BillLine | null;
    onClose: () => void;
}

/**
 * EditChargeDialog — correct a hand-written charge before it is paid.
 *
 * Only lines the desk wrote are reachable here; anything a module billed is
 * read-only at the counter and says so in the bill row itself.
 */
export function EditChargeDialog({ line, onClose }: EditChargeDialogProps) {
    const { t } = useTranslation();

    const { data, setData, patch, processing, errors, clearErrors, reset } =
        useForm({
            description: line?.description ?? '',
            quantity: String(line?.quantity ?? 1),
            unit_price: String(line?.unit_price ?? 0),
        });

    useEffect(() => {
        clearErrors();
        reset();
        setData({
            description: line?.description ?? '',
            quantity: String(line?.quantity ?? 1),
            unit_price: String(line?.unit_price ?? 0),
        });
        // Re-seed the form whenever a different line is opened.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [line?.id]);

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (line === null) {
            return;
        }

        patch(route('caisse.charges.update', line.id), {
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    };

    const quantity = Number(data.quantity || 0);
    const unitPrice = Number(data.unit_price || 0);

    return (
        <Dialog
            open={line !== null}
            onClose={onClose}
            title={t('page.caisse.edit_charge', 'Edit charge')}
            size="md"
        >
            <form onSubmit={submit} className="space-y-4">
                <Field
                    label={t('page.caisse.charge_description', 'What was used')}
                    htmlFor="edit-charge-description"
                    required
                    error={errors.description}
                >
                    <Input
                        id="edit-charge-description"
                        value={data.description}
                        invalid={Boolean(errors.description)}
                        onChange={(event) =>
                            setData('description', event.target.value)
                        }
                    />
                </Field>

                <div className="grid grid-cols-2 gap-3">
                    <Field
                        label={t('page.caisse.charge_quantity', 'Quantity')}
                        htmlFor="edit-charge-quantity"
                        required
                        error={errors.quantity}
                    >
                        <Input
                            id="edit-charge-quantity"
                            type="number"
                            min={1}
                            step={1}
                            value={data.quantity}
                            invalid={Boolean(errors.quantity)}
                            onChange={(event) =>
                                setData('quantity', event.target.value)
                            }
                            className="tabular-nums"
                        />
                    </Field>

                    <Field
                        label={t('page.caisse.charge_price', 'Unit price')}
                        htmlFor="edit-charge-price"
                        required
                        error={errors.unit_price}
                    >
                        <Input
                            id="edit-charge-price"
                            type="number"
                            min={0}
                            step={1}
                            value={data.unit_price}
                            invalid={Boolean(errors.unit_price)}
                            onChange={(event) =>
                                setData('unit_price', event.target.value)
                            }
                            className="tabular-nums"
                        />
                    </Field>
                </div>

                <p className="text-[12px] text-ink-muted tabular-nums">
                    {t('page.caisse.charge_line_total', 'Line total')}{' '}
                    <span className="font-semibold text-ink">
                        {Math.max(0, quantity * unitPrice).toLocaleString()}{' '}
                        FCFA
                    </span>
                </p>

                <div className="flex justify-end gap-2 border-t border-line pt-4">
                    <Button type="button" variant="secondary" onClick={onClose}>
                        {t('page.caisse.cancel', 'Cancel')}
                    </Button>
                    <Button type="submit" loading={processing}>
                        {t('page.caisse.save_charge', 'Save charge')}
                    </Button>
                </div>
            </form>
        </Dialog>
    );
}
