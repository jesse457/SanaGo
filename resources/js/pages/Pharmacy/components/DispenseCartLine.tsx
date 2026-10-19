import { MinusIcon, PlusIcon, TrashIcon } from '@heroicons/react/24/outline';
import { Badge, Card } from '@/Components/ui';
import { cn } from '@/lib/utils';
import type { DispenseCartItem } from './types';

interface DispenseCartLineProps {
    item: DispenseCartItem;
    currencySymbol: string;
    perUnitLabel: string;
    onQuantityChange: (quantity: number) => void;
    onRemove: () => void;
}

const stepperButton =
    'flex h-7 w-7 items-center justify-center rounded-md text-ink-muted transition-colors duration-150 ease-premium ' +
    'hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none ' +
    'disabled:pointer-events-none disabled:opacity-40';

/**
 * DispenseCartLine — one cart row: price per unit, a bounded quantity stepper
 * and the running line total. The stepper clamps to [1, available stock] in the
 * page; this component only renders and reports intent.
 */
export function DispenseCartLine({
    item,
    currencySymbol,
    perUnitLabel,
    onQuantityChange,
    onRemove,
}: DispenseCartLineProps) {
    const atFloor = item.quantity <= 1;
    const atCeiling = item.quantity >= item.max;

    return (
        <Card className="flex flex-col justify-between gap-3 p-3.5 sm:flex-row sm:items-center">
            <div className="min-w-0 flex-1">
                <p className="truncate text-[13.5px] font-semibold text-ink">
                    {item.name}
                </p>
                <p className="mt-0.5 text-[11.5px] text-ink-subtle tabular-nums">
                    {currencySymbol}
                    {parseFloat(String(item.price)).toFixed(2)} {perUnitLabel}
                </p>
            </div>

            <div className="flex items-center justify-between gap-4 sm:justify-end">
                <div className="flex items-center gap-0.5 rounded-md border border-line bg-surface-muted p-0.5">
                    <button
                        type="button"
                        onClick={() => onQuantityChange(item.quantity - 1)}
                        disabled={atFloor}
                        aria-label={`- ${item.name}`}
                        className={stepperButton}
                    >
                        <MinusIcon className="h-3.5 w-3.5" />
                    </button>
                    <input
                        type="number"
                        value={item.quantity}
                        onChange={(event) =>
                            onQuantityChange(parseInt(event.target.value))
                        }
                        aria-label={item.name}
                        className="w-11 border-none bg-transparent p-0 text-center text-[13px] font-semibold text-ink tabular-nums focus:ring-0 focus:outline-none"
                    />
                    <button
                        type="button"
                        onClick={() => onQuantityChange(item.quantity + 1)}
                        disabled={atCeiling}
                        aria-label={`+ ${item.name}`}
                        className={stepperButton}
                    >
                        <PlusIcon className="h-3.5 w-3.5" />
                    </button>
                </div>

                <p className="w-16 text-right text-[13px] font-semibold text-ink tabular-nums">
                    {currencySymbol}
                    {(item.price * item.quantity).toFixed(2)}
                </p>

                <button
                    type="button"
                    onClick={onRemove}
                    aria-label={item.name}
                    className={cn(
                        'flex h-8 w-8 items-center justify-center rounded-md text-ink-subtle transition-colors duration-150 ease-premium',
                        'hover:bg-critical-soft hover:text-critical-ink focus-visible:ring-2 focus-visible:ring-critical/40 focus-visible:outline-none',
                    )}
                >
                    <TrashIcon className="h-4 w-4" />
                </button>
            </div>
        </Card>
    );
}

/**
 * CartCountBadge — the item counter shown next to the cart heading.
 */
export function CartCountBadge({
    count,
    label,
}: {
    count: number;
    label: string;
}) {
    return (
        <Badge tone="positive" dot>
            {count} {label}
        </Badge>
    );
}
