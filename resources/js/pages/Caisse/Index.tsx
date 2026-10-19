import {
    BanknotesIcon,
    ClockIcon,
    ArrowPathIcon,
    XMarkIcon,
    CheckCircleIcon,
    CalculatorIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    Alert,
    Button,
    Dialog,
    EmptyState,
    Field,
    Input,
    PageHeader,
    Select,
    SectionCard,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { ShiftHistoryCard } from '@/pages/Caisse/components';
import type { CashShift, CashPayment } from '@/types';

interface Props {
    activeShift: CashShift | null;
    todayPayments: CashPayment[];
    shiftHistory: CashShift[];
}

const PAYMENT_METHODS = [
    { value: 'cash', label: 'Cash (Espèces)' },
    { value: 'mobile_money', label: 'Mobile Money (Orange Money / MTN MoMo)' },
    { value: 'bank_transfer', label: 'Bank Transfer (Virement)' },
];

export default function CaisseIndex({
    activeShift,
    todayPayments,
    shiftHistory,
}: Props) {
    const { t } = useTranslation();

    const {
        data: shiftData,
        setData: setShiftData,
        post: postShift,
        processing: shiftProcessing,
        errors: shiftErrors,
        reset: resetShift,
    } = useForm({
        payment_point: 'Caisse Principale',
        opening_balance: '0',
    });

    const {
        data: payData,
        setData: setPayData,
        post: postPay,
        processing: payProcessing,
        errors: payErrors,
        reset: resetPay,
    } = useForm({
        invoice_id: '' as string,
        patient_id: '' as string,
        amount_paid: '',
        payment_method: 'cash' as string,
    });

    const {
        data: closeData,
        setData: setCloseData,
        post: postClose,
        processing: closeProcessing,
        reset: resetClose,
    } = useForm({
        closing_balance: '',
    });

    const [showOpenModal, setShowOpenModal] = useState(false);
    const [showCloseModal, setShowCloseModal] = useState(false);
    const [showPayModal, setShowPayModal] = useState(false);

    const handleOpenShift = (e: FormEvent) => {
        e.preventDefault();
        postShift(route('caisse.open'), {
            onSuccess: () => {
                setShowOpenModal(false);
                resetShift();
            },
        });
    };

    const handleCloseShift = (e: FormEvent) => {
        e.preventDefault();
        postClose(route('caisse.close'), {
            onSuccess: () => {
                setShowCloseModal(false);
                resetClose();
            },
        });
    };

    const handlePayment = (e: FormEvent) => {
        e.preventDefault();
        postPay(route('caisse.pay'), {
            onSuccess: () => {
                setShowPayModal(false);
                resetPay();
            },
        });
    };

    const todayTotal = todayPayments.reduce(
        (sum, p) => sum + Number(p.amount_paid),
        0,
    );
    const shiftTotal =
        activeShift?.payments?.reduce(
            (sum, p) => sum + Number(p.amount_paid),
            0,
        ) ?? 0;

    return (
        <AppLayout
            headerTitle={t('Caisse — Cash Register')}
            headerSubtitle={t(
                'Manage cash shifts, payments and daily reconciliation',
            )}
        >
            <Head title={t('Caisse')} />

            <div className="flex flex-col gap-6">
                <PageHeader
                    title={t('Caisse — Cash Register')}
                    description={t(
                        'Manage cash shifts, payments and daily reconciliation',
                    )}
                    breadcrumbs={[{ label: t('Caisse') }]}
                />

                <StatGrid columns={4}>
                    <StatCard
                        icon={ClockIcon}
                        label={t('Shift Status')}
                        value={activeShift ? t('Open') : t('Closed')}
                        tone={activeShift ? 'positive' : 'critical'}
                    />
                    <StatCard
                        icon={BanknotesIcon}
                        label={t('Opening Balance')}
                        value={`${Number(activeShift?.opening_balance ?? 0).toLocaleString()} XAF`}
                        tone="accent"
                    />
                    <StatCard
                        icon={ArrowPathIcon}
                        label={t('Shift Payments')}
                        value={`${shiftTotal.toLocaleString()} XAF`}
                        tone="accent"
                    />
                    <StatCard
                        icon={CalculatorIcon}
                        label={t("Today's Total")}
                        value={`${todayTotal.toLocaleString()} XAF`}
                        tone="warning"
                    />
                </StatGrid>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <SectionCard
                            title={t('Shift Actions')}
                            icon={ClockIcon}
                        >
                            <div className="flex flex-wrap gap-2">
                                {!activeShift ? (
                                    <Button
                                        icon={CheckCircleIcon}
                                        onClick={() => setShowOpenModal(true)}
                                    >
                                        {t('Open Shift')}
                                    </Button>
                                ) : (
                                    <>
                                        <Button
                                            icon={BanknotesIcon}
                                            onClick={() =>
                                                setShowPayModal(true)
                                            }
                                        >
                                            {t('Record Payment')}
                                        </Button>
                                        <Button
                                            variant="danger"
                                            icon={XMarkIcon}
                                            onClick={() =>
                                                setShowCloseModal(true)
                                            }
                                        >
                                            {t('Close Shift')}
                                        </Button>
                                    </>
                                )}
                            </div>
                        </SectionCard>

                        {activeShift &&
                            activeShift.payments &&
                            activeShift.payments.length > 0 && (
                                <SectionCard
                                    title={t('Current Shift Payments')}
                                    icon={BanknotesIcon}
                                >
                                    <Table bare>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHeaderCell>
                                                    {t('Patient')}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t('Invoice')}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t('Amount')}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t('Method')}
                                                </TableHeaderCell>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {activeShift.payments.map((p) => (
                                                <TableRow key={p.id}>
                                                    <TableCell muted>
                                                        {p.patient
                                                            ? `${p.patient.first_name} ${p.patient.last_name}`
                                                            : '—'}
                                                    </TableCell>
                                                    <TableCell muted>
                                                        {p.invoice
                                                            ?.invoice_number ??
                                                            '—'}
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        {Number(
                                                            p.amount_paid,
                                                        ).toLocaleString()}{' '}
                                                        XAF
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        <span className="text-[11px] font-medium tracking-[0.1em] text-ink-muted uppercase">
                                                            {p.payment_method ===
                                                            'mobile_money'
                                                                ? 'MoMo'
                                                                : p.payment_method}
                                                        </span>
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </SectionCard>
                            )}
                    </div>

                    <SectionCard title={t('Shift History')} icon={ClockIcon}>
                        {shiftHistory.length === 0 ? (
                            <EmptyState
                                panel={false}
                                title={t('No previous shifts.')}
                                className="px-0 py-8"
                            />
                        ) : (
                            <div className="space-y-3">
                                {shiftHistory.map((shift) => (
                                    <ShiftHistoryCard
                                        key={shift.id}
                                        shift={shift}
                                        labels={{
                                            open: t('Open'),
                                            closed: t('Closed'),
                                            opening: t('Opening'),
                                            closing: t('Closing'),
                                            payments: t('Payments'),
                                        }}
                                    />
                                ))}
                            </div>
                        )}
                    </SectionCard>
                </div>
            </div>

            <Dialog
                open={showOpenModal}
                onClose={() => setShowOpenModal(false)}
                title={t('Open Cash Shift')}
                size="md"
            >
                <form onSubmit={handleOpenShift} className="space-y-4">
                    <Field
                        label={t('Payment Point')}
                        htmlFor="payment_point"
                        required
                        error={shiftErrors.payment_point}
                    >
                        <Input
                            id="payment_point"
                            type="text"
                            value={shiftData.payment_point}
                            onChange={(e) =>
                                setShiftData('payment_point', e.target.value)
                            }
                            invalid={Boolean(shiftErrors.payment_point)}
                        />
                    </Field>

                    <Field
                        label={t('Opening Balance (XAF)')}
                        htmlFor="opening_balance"
                        required
                        error={shiftErrors.opening_balance}
                    >
                        <Input
                            id="opening_balance"
                            type="number"
                            min="0"
                            step="0.01"
                            value={shiftData.opening_balance}
                            onChange={(e) =>
                                setShiftData('opening_balance', e.target.value)
                            }
                            invalid={Boolean(shiftErrors.opening_balance)}
                        />
                    </Field>

                    <div className="flex justify-end gap-2 border-t border-line pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowOpenModal(false)}
                        >
                            {t('Cancel')}
                        </Button>
                        <Button
                            type="submit"
                            loading={shiftProcessing}
                            disabled={shiftProcessing}
                        >
                            {t('Open Shift')}
                        </Button>
                    </div>
                </form>
            </Dialog>

            <Dialog
                open={showPayModal}
                onClose={() => setShowPayModal(false)}
                title={t('Record Payment')}
                size="md"
            >
                <form onSubmit={handlePayment} className="space-y-4">
                    <Field
                        label={t('Invoice ID (optional)')}
                        htmlFor="invoice_id"
                        error={payErrors.invoice_id}
                    >
                        <Input
                            id="invoice_id"
                            type="number"
                            value={payData.invoice_id}
                            onChange={(e) =>
                                setPayData('invoice_id', e.target.value)
                            }
                            invalid={Boolean(payErrors.invoice_id)}
                        />
                    </Field>

                    <Field
                        label={t('Patient ID (optional)')}
                        htmlFor="patient_id"
                        error={payErrors.patient_id}
                    >
                        <Input
                            id="patient_id"
                            type="number"
                            value={payData.patient_id}
                            onChange={(e) =>
                                setPayData('patient_id', e.target.value)
                            }
                            invalid={Boolean(payErrors.patient_id)}
                        />
                    </Field>

                    <Field
                        label={t('Amount (XAF)')}
                        htmlFor="amount_paid"
                        required
                        error={payErrors.amount_paid}
                    >
                        <Input
                            id="amount_paid"
                            type="number"
                            min="0"
                            step="0.01"
                            value={payData.amount_paid}
                            onChange={(e) =>
                                setPayData('amount_paid', e.target.value)
                            }
                            invalid={Boolean(payErrors.amount_paid)}
                        />
                    </Field>

                    <Field
                        label={t('Payment Method')}
                        htmlFor="payment_method"
                        required
                        error={payErrors.payment_method}
                    >
                        <Select
                            id="payment_method"
                            value={payData.payment_method}
                            onChange={(e) =>
                                setPayData('payment_method', e.target.value)
                            }
                            invalid={Boolean(payErrors.payment_method)}
                        >
                            {PAYMENT_METHODS.map((opt) => (
                                <option key={opt.value} value={opt.value}>
                                    {opt.label}
                                </option>
                            ))}
                        </Select>
                    </Field>

                    <div className="flex justify-end gap-2 border-t border-line pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowPayModal(false)}
                        >
                            {t('Cancel')}
                        </Button>
                        <Button
                            type="submit"
                            loading={payProcessing}
                            disabled={payProcessing}
                        >
                            {t('Record Payment')}
                        </Button>
                    </div>
                </form>
            </Dialog>

            <Dialog
                open={showCloseModal}
                onClose={() => setShowCloseModal(false)}
                title={t('Close Cash Shift')}
                size="md"
            >
                <form onSubmit={handleCloseShift} className="space-y-4">
                    <Alert tone="warning" title={t('Close Shift')}>
                        <div className="space-y-1 tabular-nums">
                            <p>
                                {t('Opening Balance')}:{' '}
                                <strong>
                                    {Number(
                                        activeShift?.opening_balance ?? 0,
                                    ).toLocaleString()}{' '}
                                    XAF
                                </strong>
                            </p>
                            <p>
                                {t('Payments Received')}:{' '}
                                <strong>
                                    {shiftTotal.toLocaleString()} XAF
                                </strong>
                            </p>
                            <p>
                                {t('Expected Total')}:{' '}
                                <strong>
                                    {(
                                        Number(
                                            activeShift?.opening_balance ?? 0,
                                        ) + shiftTotal
                                    ).toLocaleString()}{' '}
                                    XAF
                                </strong>
                            </p>
                        </div>
                    </Alert>

                    <Field
                        label={t('Actual Closing Balance (XAF)')}
                        htmlFor="closing_balance"
                        required
                    >
                        <Input
                            id="closing_balance"
                            type="number"
                            min="0"
                            step="0.01"
                            value={closeData.closing_balance}
                            onChange={(e) =>
                                setCloseData('closing_balance', e.target.value)
                            }
                        />
                    </Field>

                    <div className="flex justify-end gap-2 border-t border-line pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowCloseModal(false)}
                        >
                            {t('Cancel')}
                        </Button>
                        <Button
                            type="submit"
                            variant="danger"
                            loading={closeProcessing}
                            disabled={closeProcessing}
                        >
                            {t('Close Shift')}
                        </Button>
                    </div>
                </form>
            </Dialog>
        </AppLayout>
    );
}
