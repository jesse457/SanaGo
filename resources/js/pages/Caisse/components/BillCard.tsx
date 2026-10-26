import {
    CheckCircleIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import {
    Badge,
    Button,
    Card,
    Checkbox,
    EmptyState,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';
import {
    ClaimStatusBadge,
    InvoiceStatusBadge,
} from '@/pages/Finance/components/FinanceStatusBadges';
import { formatDate, formatMoney } from '@/pages/Finance/components/format';
import type { Bill, BillLine } from './types';

const MODULE_TONES: Record<string, BadgeTone> = {
    laboratory: 'accent',
    pharmacy: 'positive',
    radiology: 'warning',
    consultation: 'neutral',
    admission: 'muted',
    surgery: 'critical',
    emergency: 'critical',
    procedure: 'neutral',
    nursing: 'positive',
    other: 'muted',
};

interface BillCardProps {
    bill: Bill;
    /** Invoice line ids the cashier ticked to pay now. */
    selected: number[];
    highlighted?: boolean;
    busy?: boolean;
    onToggleLine: (line: BillLine, checked: boolean) => void;
    onEditLine: (line: BillLine) => void;
    onVoidLine: (line: BillLine) => void;
}

/**
 * One bill, whole: every charge on it, what was paid, what is left.
 *
 * This replaces the separate invoice screen. Lines the counter wrote by hand
 * are editable and removable until they are paid; lines that came from another
 * module are read-only here and say so.
 */
export function BillCard({
    bill,
    selected,
    highlighted = false,
    busy = false,
    onToggleLine,
    onEditLine,
    onVoidLine,
}: BillCardProps) {
    const { t } = useTranslation();

    const selectable = (line: BillLine) => bill.is_open && !line.settled;

    return (
        <Card
            className={cn(
                'overflow-hidden',
                highlighted && 'border-accent/40 ring-1 ring-accent/20',
            )}
        >
            <div className="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-b border-line px-4 py-3">
                <div className="flex min-w-0 flex-wrap items-center gap-2">
                    <span className="font-mono text-[12.5px] font-semibold tracking-tight text-ink">
                        {bill.invoice_number}
                    </span>
                    <InvoiceStatusBadge
                        status={bill.status}
                        label={t(
                            `finance.invoices.index.status_${bill.status}`,
                        )}
                    />
                    {bill.claim && (
                        <ClaimStatusBadge
                            status={bill.claim.status}
                            label={
                                bill.claim.payer
                                    ? `${bill.claim.payer} · ${formatMoney(bill.claim.claim_amount)}`
                                    : t(
                                          `page.caisse.claim_${bill.claim.status}`,
                                          bill.claim.status,
                                      )
                            }
                        />
                    )}
                    <span className="text-[11.5px] text-ink-subtle">
                        {formatDate(bill.invoice_date)}
                    </span>
                </div>

                <div className="flex items-center gap-4 text-[12px] text-ink-muted tabular-nums">
                    <span>
                        {t('page.caisse.bill_total', 'Billed')}{' '}
                        <span className="font-semibold text-ink">
                            {formatMoney(bill.total_amount)}
                        </span>
                    </span>
                    <span>
                        {t('page.caisse.bill_paid', 'Paid')}{' '}
                        <span className="font-semibold text-positive-ink">
                            {formatMoney(bill.total_paid)}
                        </span>
                    </span>
                    <span>
                        {t('page.caisse.bill_due', 'Left to pay')}{' '}
                        <span className="font-semibold text-ink">
                            {formatMoney(bill.remaining_balance)}
                        </span>
                    </span>
                </div>
            </div>

            {bill.items.length === 0 ? (
                <EmptyState
                    panel={false}
                    icon={CheckCircleIcon}
                    title={t('page.caisse.empty_bill', 'Nothing billed yet')}
                    description={t(
                        'page.caisse.empty_bill_desc',
                        'Add what the patient consumed below, or pick a service from the price list.',
                    )}
                    className="py-8"
                />
            ) : (
                <Table bare>
                    <TableHeader>
                        <TableRow>
                            <TableHeaderCell className="w-10">
                                <span className="sr-only">
                                    {t('page.caisse.col_select', 'Select')}
                                </span>
                            </TableHeaderCell>
                            <TableHeaderCell>
                                {t('page.caisse.col_charge', 'Charge')}
                            </TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('page.caisse.col_qty', 'Qty')}
                            </TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('page.caisse.col_unit', 'Unit price')}
                            </TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('page.caisse.col_total', 'Total')}
                            </TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('page.caisse.col_actions', 'Actions')}
                            </TableHeaderCell>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {bill.items.map((line) => (
                            <TableRow key={line.id}>
                                <TableCell>
                                    {selectable(line) ? (
                                        <Checkbox
                                            aria-label={line.description}
                                            checked={selected.includes(line.id)}
                                            disabled={busy}
                                            onChange={(event) =>
                                                onToggleLine(
                                                    line,
                                                    event.target.checked,
                                                )
                                            }
                                        />
                                    ) : (
                                        <span
                                            aria-hidden="true"
                                            className="block h-4 w-4"
                                        />
                                    )}
                                </TableCell>
                                <TableCell>
                                    <span className="flex min-w-0 flex-wrap items-center gap-2">
                                        <span className="truncate text-[12.5px] font-medium text-ink">
                                            {line.description}
                                        </span>
                                        <Badge
                                            tone={
                                                MODULE_TONES[line.module] ??
                                                'muted'
                                            }
                                            size="sm"
                                        >
                                            {line.module}
                                        </Badge>
                                        {line.manual && (
                                            <Badge tone="muted" size="sm">
                                                {t(
                                                    'page.caisse.manual_charge',
                                                    'Added at desk',
                                                )}
                                            </Badge>
                                        )}
                                        {line.settled && (
                                            <Badge
                                                tone="positive"
                                                size="sm"
                                                dot
                                            >
                                                {t(
                                                    'page.caisse.settled',
                                                    'Paid',
                                                )}
                                            </Badge>
                                        )}
                                    </span>
                                </TableCell>
                                <TableCell numeric muted>
                                    {line.quantity}
                                </TableCell>
                                <TableCell numeric muted>
                                    {formatMoney(line.unit_price)}
                                </TableCell>
                                <TableCell numeric>
                                    <span className="font-semibold text-ink">
                                        {formatMoney(line.total_price)}
                                    </span>
                                </TableCell>
                                <TableCell numeric>
                                    <div className="flex items-center justify-end gap-1">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            icon={PencilSquareIcon}
                                            disabled={!line.adjustable || busy}
                                            aria-label={t(
                                                'page.caisse.edit_charge',
                                                'Edit charge',
                                            )}
                                            onClick={() => onEditLine(line)}
                                        />
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            icon={TrashIcon}
                                            disabled={!line.adjustable || busy}
                                            aria-label={t(
                                                'page.caisse.void_charge',
                                                'Remove charge',
                                            )}
                                            className="text-critical-ink hover:bg-critical-soft hover:text-critical-ink"
                                            onClick={() => onVoidLine(line)}
                                        />
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            )}

            {bill.payments.length > 0 && (
                <div className="flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-line bg-surface-muted px-4 py-2.5">
                    <span className="text-[10.5px] font-semibold tracking-[0.13em] text-ink-subtle uppercase">
                        {t('page.caisse.payments_on_bill', 'Payments')}
                    </span>
                    {bill.payments.map((payment) => (
                        <span
                            key={payment.id}
                            className="text-[11.5px] text-ink-muted tabular-nums"
                        >
                            {formatMoney(payment.amount)} ·{' '}
                            {payment.method.replace('_', ' ')}
                            {payment.paid_at
                                ? ` · ${formatDate(payment.paid_at)}`
                                : ''}
                        </span>
                    ))}
                </div>
            )}
        </Card>
    );
}
