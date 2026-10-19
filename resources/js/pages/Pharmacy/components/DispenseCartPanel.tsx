import { ShoppingBagIcon } from '@heroicons/react/24/outline';
import { Button, Card, EmptyState, Textarea } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { CartCountBadge, DispenseCartLine } from './DispenseCartLine';
import type { DispenseCartItem } from './types';

interface DispenseCartPanelProps {
    items: DispenseCartItem[];
    notes: string;
    onNotesChange: (value: string) => void;
    onQuantityChange: (index: number, quantity: number) => void;
    onRemove: (index: number) => void;
    onCheckout: () => void;
    cartTotal: number;
}

/**
 * DispenseCartPanel — the walk-in stage: the cart itself, the internal note
 * field and the checkout affordance in a fixed footer. Totals stay tabular.
 */
export function DispenseCartPanel({
    items,
    notes,
    onNotesChange,
    onQuantityChange,
    onRemove,
    onCheckout,
    cartTotal,
}: DispenseCartPanelProps) {
    const { t } = useTranslation();
    const isEmpty = items.length === 0;

    return (
        <Card className="flex h-[500px] flex-col overflow-hidden lg:h-[650px]">
            <div className="flex items-center justify-between gap-3 border-b border-line bg-surface-muted px-4 py-3">
                <h2 className="text-[13px] font-semibold tracking-tight text-ink">
                    {t('walkInCart')}
                </h2>
                {items.length > 0 && (
                    <CartCountBadge count={items.length} label={t('items')} />
                )}
            </div>

            <div className="flex-1 overflow-y-auto p-2.5 sm:p-3">
                {isEmpty ? (
                    <div className="flex h-full items-center justify-center">
                        <EmptyState
                            panel={false}
                            icon={ShoppingBagIcon}
                            title={t('yourCartIsEmpty')}
                            description={t('selectMedicationsFromInventory')}
                        />
                    </div>
                ) : (
                    <div className="space-y-2.5">
                        {items.map((item, index) => (
                            <DispenseCartLine
                                key={item.medication_id}
                                item={item}
                                currencySymbol={t('currencySymbol')}
                                perUnitLabel={t('perUnit')}
                                onQuantityChange={(quantity) =>
                                    onQuantityChange(index, quantity)
                                }
                                onRemove={() => onRemove(index)}
                            />
                        ))}
                    </div>
                )}
            </div>

            <div className="space-y-3 border-t border-line bg-surface-muted px-4 py-3.5">
                {items.length > 0 && (
                    <div className="flex items-baseline justify-between gap-3">
                        <span className="text-[12px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('totalAmount')}
                        </span>
                        <span className="text-[20px] font-semibold text-ink tabular-nums">
                            {t('currencySymbol')}
                            {cartTotal.toFixed(2)}
                        </span>
                    </div>
                )}

                <Textarea
                    id="dispensation-notes"
                    name="notes"
                    rows={2}
                    value={notes}
                    onChange={(event) => onNotesChange(event.target.value)}
                    placeholder={t('addInternalNotes')}
                    aria-label={t('addInternalNotes')}
                />

                <Button
                    type="button"
                    variant="accent"
                    fullWidth
                    loading={false}
                    disabled={isEmpty}
                    onClick={onCheckout}
                >
                    {t('reviewAndCheckout')}
                </Button>
            </div>
        </Card>
    );
}
