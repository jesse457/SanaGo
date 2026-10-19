import { XCircleIcon } from '@heroicons/react/24/outline';
import type { FormEvent } from 'react';
import {
    Alert,
    Badge,
    Button,
    Dialog,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import type { DispenseCartItem, DispenseViewMode } from './types';

interface DispenseConfirmDialogProps {
    open: boolean;
    onClose: () => void;
    onSubmit: (event: FormEvent<HTMLFormElement>) => void;
    processing: boolean;
    items: DispenseCartItem[];
    errors: Record<string, string | string[] | undefined>;
    viewMode: DispenseViewMode;
    cartTotal: number;
}

/**
 * DispenseConfirmDialog — the last checkpoint before stock moves. Lists what is
 * about to leave the shelf, the post-dispense balance per line, the unit count
 * and (walk-in only) the money.
 */
export function DispenseConfirmDialog({
    open,
    onClose,
    onSubmit,
    processing,
    items,
    errors,
    viewMode,
    cartTotal,
}: DispenseConfirmDialogProps) {
    const { t } = useTranslation();
    const errorEntries = Object.entries(errors);
    const totalUnits = items.reduce((acc, item) => acc + item.quantity, 0);

    return (
        <Dialog
            open={open}
            onClose={onClose}
            size="lg"
            title={t('confirmDispensation')}
            description={t('pleaseReviewQuantities')}
            footer={
                <form onSubmit={onSubmit} className="flex items-center gap-2">
                    <Button type="button" variant="secondary" onClick={onClose}>
                        {t('cancel')}
                    </Button>
                    <Button
                        type="submit"
                        variant={viewMode === 'walkin' ? 'accent' : 'primary'}
                        loading={processing}
                    >
                        {t('confirmAndProcess')}
                    </Button>
                </form>
            }
        >
            <div className="space-y-4">
                {errorEntries.length > 0 && (
                    <Alert tone="critical" title={t('error')}>
                        <ul className="space-y-1">
                            {errorEntries.map(([key, messages]) => (
                                <li
                                    key={key}
                                    className="flex items-start gap-2 text-critical-ink"
                                >
                                    <XCircleIcon className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                                    <span>
                                        {Array.isArray(messages)
                                            ? messages[0]
                                            : String(messages)}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </Alert>
                )}

                <Table bare>
                    <TableHeader>
                        <TableRow>
                            <TableHeaderCell>{t('medication')}</TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('quantity')}
                            </TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('totalCost')}
                            </TableHeaderCell>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {items.map((item) => (
                            <TableRow key={item.medication_id}>
                                <TableCell>
                                    <span className="block font-medium">
                                        {item.name}
                                    </span>
                                    <span className="mt-0.5 block text-[11.5px] text-ink-subtle tabular-nums">
                                        {t('inventoryPostDispense')}:{' '}
                                        {item.stock - item.quantity}
                                    </span>
                                </TableCell>
                                <TableCell numeric>
                                    <Badge tone="neutral">
                                        x{item.quantity}
                                    </Badge>
                                </TableCell>
                                <TableCell numeric>
                                    {item.price
                                        ? `${t('currencySymbol')}${(
                                              item.price * item.quantity
                                          ).toFixed(2)}`
                                        : '—'}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>

                <div className="flex items-end justify-between gap-4 border-t border-line pt-3">
                    <div>
                        <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('totalItems')}
                        </p>
                        <p className="mt-1 text-[15px] font-semibold text-ink tabular-nums">
                            {totalUnits} {t('units')}
                        </p>
                    </div>
                    {viewMode === 'walkin' && (
                        <div className="text-right">
                            <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                {t('totalCost')}
                            </p>
                            <p className="mt-1 text-[20px] font-semibold text-ink tabular-nums">
                                {t('currencySymbol')}
                                {cartTotal.toFixed(2)}
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </Dialog>
    );
}
