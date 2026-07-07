import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import type { FormEvent } from 'react';
import {
    BanknotesIcon, ClockIcon, ArrowPathIcon, XMarkIcon,
    CheckCircleIcon, DocumentTextIcon, UserIcon, PhoneIcon,
    CreditCardIcon, CalculatorIcon,
} from '@heroicons/react/24/outline';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
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

export default function CaisseIndex({ activeShift, todayPayments, shiftHistory }: Props) {
    const { t } = useTranslation();

    const { data: shiftData, setData: setShiftData, post: postShift, processing: shiftProcessing, errors: shiftErrors, reset: resetShift } = useForm({
        payment_point: 'Caisse Principale',
        opening_balance: '0',
    });

    const { data: payData, setData: setPayData, post: postPay, processing: payProcessing, errors: payErrors, reset: resetPay } = useForm({
        invoice_id: '' as string,
        patient_id: '' as string,
        amount_paid: '',
        payment_method: 'cash' as string,
    });

    const { data: closeData, setData: setCloseData, post: postClose, processing: closeProcessing, reset: resetClose } = useForm({
        closing_balance: '',
    });

    const [showOpenModal, setShowOpenModal] = useState(false);
    const [showCloseModal, setShowCloseModal] = useState(false);
    const [showPayModal, setShowPayModal] = useState(false);

    const handleOpenShift = (e: FormEvent) => {
        e.preventDefault();
        postShift(route('caisse.open'), { onSuccess: () => { setShowOpenModal(false); resetShift(); } });
    };

    const handleCloseShift = (e: FormEvent) => {
        e.preventDefault();
        postClose(route('caisse.close'), { onSuccess: () => { setShowCloseModal(false); resetClose(); } });
    };

    const handlePayment = (e: FormEvent) => {
        e.preventDefault();
        postPay(route('caisse.pay'), { onSuccess: () => { setShowPayModal(false); resetPay(); } });
    };

    const todayTotal = todayPayments.reduce((sum, p) => sum + Number(p.amount_paid), 0);
    const shiftTotal = activeShift?.payments?.reduce((sum, p) => sum + Number(p.amount_paid), 0) ?? 0;

    return (
        <AppLayout headerTitle={t('Caisse — Cash Register')} headerSubtitle={t('Manage cash shifts, payments and daily reconciliation')}>
            <Head title={t('Caisse')} />
            <PageBreadcrumb items={[{ label: t('Caisse') }]} />

            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <StatCard icon={ClockIcon} label={t('Shift Status')} value={activeShift ? t('Open') : t('Closed')} color={activeShift ? 'emerald' : 'red'} />
                <StatCard icon={BanknotesIcon} label={t('Opening Balance')} value={`${Number(activeShift?.opening_balance ?? 0).toLocaleString()} XAF`} color="blue" />
                <StatCard icon={ArrowPathIcon} label={t('Shift Payments')} value={`${shiftTotal.toLocaleString()} XAF`} color="purple" />
                <StatCard icon={CalculatorIcon} label={t('Today\'s Total')} value={`${todayTotal.toLocaleString()} XAF`} color="amber" />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 space-y-6">
                    <PageSection title={t('Shift Actions')} icon={ClockIcon}>
                        <div className="flex gap-3">
                            {!activeShift ? (
                                <button onClick={() => setShowOpenModal(true)} className="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 cursor-pointer">
                                    <CheckCircleIcon className="h-4 w-4" />
                                    {t('Open Shift')}
                                </button>
                            ) : (
                                <>
                                    <button onClick={() => setShowPayModal(true)} className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 cursor-pointer">
                                        <BanknotesIcon className="h-4 w-4" />
                                        {t('Record Payment')}
                                    </button>
                                    <button onClick={() => setShowCloseModal(true)} className="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 cursor-pointer">
                                        <XMarkIcon className="h-4 w-4" />
                                        {t('Close Shift')}
                                    </button>
                                </>
                            )}
                        </div>
                    </PageSection>

                    {activeShift && activeShift.payments && activeShift.payments.length > 0 && (
                        <PageSection title={t('Current Shift Payments')} icon={BanknotesIcon}>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="bg-gray-50 dark:bg-neutral-900">
                                        <tr>
                                            <th className="px-3 py-2 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Patient')}</th>
                                            <th className="px-3 py-2 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Invoice')}</th>
                                            <th className="px-3 py-2 text-right font-medium text-gray-600 dark:text-neutral-400">{t('Amount')}</th>
                                            <th className="px-3 py-2 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Method')}</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                        {activeShift.payments.map((p) => (
                                            <tr key={p.id}>
                                                <td className="px-3 py-2 text-gray-700 dark:text-neutral-300">{p.patient ? `${p.patient.first_name} ${p.patient.last_name}` : '—'}</td>
                                                <td className="px-3 py-2 text-gray-500 dark:text-neutral-400">{p.invoice?.invoice_number ?? '—'}</td>
                                                <td className="px-3 py-2 text-right font-medium text-gray-900 dark:text-white">{Number(p.amount_paid).toLocaleString()} XAF</td>
                                                <td className="px-3 py-2 text-center">
                                                    <StatusBadge label={p.payment_method === 'mobile_money' ? 'MoMo' : p.payment_method} color={p.payment_method === 'cash' ? 'emerald' : p.payment_method === 'mobile_money' ? 'amber' : 'blue'} />
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </PageSection>
                    )}
                </div>

                <div>
                    <PageSection title={t('Shift History')} icon={ClockIcon}>
                        {shiftHistory.length === 0 ? (
                            <p className="text-sm text-gray-500 dark:text-neutral-400">{t('No previous shifts.')}</p>
                        ) : (
                            <div className="space-y-3">
                                {shiftHistory.map((shift) => (
                                    <div key={shift.id} className="p-3 bg-gray-50 dark:bg-neutral-900 rounded-lg">
                                        <div className="flex items-center justify-between mb-1">
                                            <StatusBadge label={t(shift.status)} color={shift.status === 'open' ? 'emerald' : 'gray'} />
                                            <span className="text-xs text-gray-400">{new Date(shift.opened_at).toLocaleDateString()}</span>
                                        </div>
                                        <div className="text-xs text-gray-500 dark:text-neutral-400 space-y-0.5">
                                            <div className="flex justify-between"><span>{t('Opening')}:</span> <span className="font-medium">{Number(shift.opening_balance).toLocaleString()} XAF</span></div>
                                            {shift.closing_balance && <div className="flex justify-between"><span>{t('Closing')}:</span> <span className="font-medium">{Number(shift.closing_balance).toLocaleString()} XAF</span></div>}
                                            <div className="flex justify-between"><span>{t('Payments')}:</span> <span>{shift.payments_count ?? 0}</span></div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </PageSection>
                </div>
            </div>

            {/* Open Shift Modal */}
            <Modal isOpen={showOpenModal} onClose={() => setShowOpenModal(false)} title={t('Open Cash Shift')}>
                <form onSubmit={handleOpenShift} className="space-y-4">
                    <FormInput name="payment_point" label={t('Payment Point')} value={shiftData.payment_point} onChange={(v) => setShiftData('payment_point', v)} errors={shiftErrors} required />
                    <FormInput name="opening_balance" label={t('Opening Balance (XAF)')} type="number" value={shiftData.opening_balance} onChange={(v) => setShiftData('opening_balance', v)} errors={shiftErrors} required min="0" step="0.01" />
                    <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
                        <button type="button" onClick={() => setShowOpenModal(false)} className="px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg cursor-pointer">{t('Cancel')}</button>
                        <button type="submit" disabled={shiftProcessing} className="px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 cursor-pointer">{t('Open Shift')}</button>
                    </div>
                </form>
            </Modal>

            {/* Record Payment Modal */}
            <Modal isOpen={showPayModal} onClose={() => setShowPayModal(false)} title={t('Record Payment')}>
                <form onSubmit={handlePayment} className="space-y-4">
                    <FormInput name="invoice_id" label={t('Invoice ID (optional)')} type="number" value={payData.invoice_id} onChange={(v) => setPayData('invoice_id', v)} errors={payErrors} />
                    <FormInput name="patient_id" label={t('Patient ID (optional)')} type="number" value={payData.patient_id} onChange={(v) => setPayData('patient_id', v)} errors={payErrors} />
                    <FormInput name="amount_paid" label={t('Amount (XAF)')} type="number" value={payData.amount_paid} onChange={(v) => setPayData('amount_paid', v)} errors={payErrors} required min="0" step="0.01" />
                    <FormSelect name="payment_method" label={t('Payment Method')} value={payData.payment_method} onChange={(v) => setPayData('payment_method', v)} options={PAYMENT_METHODS} errors={payErrors} required />
                    <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
                        <button type="button" onClick={() => setShowPayModal(false)} className="px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg cursor-pointer">{t('Cancel')}</button>
                        <button type="submit" disabled={payProcessing} className="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 cursor-pointer">{t('Record Payment')}</button>
                    </div>
                </form>
            </Modal>

            {/* Close Shift Modal */}
            <Modal isOpen={showCloseModal} onClose={() => setShowCloseModal(false)} title={t('Close Cash Shift')}>
                <form onSubmit={handleCloseShift} className="space-y-4">
                    <div className="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <p className="text-sm text-amber-700 dark:text-amber-400">
                            {t('Opening Balance')}: <strong>{Number(activeShift?.opening_balance ?? 0).toLocaleString()} XAF</strong>
                        </p>
                        <p className="text-sm text-amber-700 dark:text-amber-400">
                            {t('Payments Received')}: <strong>{shiftTotal.toLocaleString()} XAF</strong>
                        </p>
                        <p className="text-sm font-medium text-amber-800 dark:text-amber-300 mt-1">
                            {t('Expected Total')}: <strong>{(Number(activeShift?.opening_balance ?? 0) + shiftTotal).toLocaleString()} XAF</strong>
                        </p>
                    </div>
                    <FormInput name="closing_balance" label={t('Actual Closing Balance (XAF)')} type="number" value={closeData.closing_balance} onChange={(v) => setCloseData('closing_balance', v)} errors={{}} required min="0" step="0.01" />
                    <div className="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-700">
                        <button type="button" onClick={() => setShowCloseModal(false)} className="px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg cursor-pointer">{t('Cancel')}</button>
                        <button type="submit" disabled={closeProcessing} className="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 cursor-pointer">{t('Close Shift')}</button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
