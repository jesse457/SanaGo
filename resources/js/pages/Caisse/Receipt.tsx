import { ArrowLeftIcon, PrinterIcon } from '@heroicons/react/24/outline';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { Button, PageHeader, SegmentedControl } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { CashPaymentRecord, TenantIdentity } from '@/types';

interface Props {
    /** `CaisseController@receipt` loads cashShift.user, patient and invoice. */
    payment: CashPaymentRecord;
}

type PaperWidth = 'a4' | 'thermal';

const METHOD_KEYS: Record<string, string> = {
    cash: 'page.caisse_receipt.method_cash',
    mobile_money: 'page.caisse_receipt.method_mobile_money',
    bank_transfer: 'page.caisse_receipt.method_bank_transfer',
};

function formatMoney(value: string | number | null | undefined): string {
    const amount = typeof value === 'string' ? Number(value) : (value ?? 0);

    if (Number.isNaN(amount)) {
        return String(value ?? '—');
    }

    return new Intl.NumberFormat(undefined, {
        maximumFractionDigits: 0,
    }).format(amount);
}

function formatDateTime(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return `${parsed.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })} · ${parsed.toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
    })}`;
}

function ReceiptRow({
    label,
    value,
    strong = false,
}: {
    label: string;
    value: React.ReactNode;
    strong?: boolean;
}) {
    return (
        <div className="flex items-baseline justify-between gap-4 border-b border-dashed border-black/20 py-1.5 last:border-0">
            <span className="text-[11px] tracking-[0.06em] text-black/60">
                {label}
            </span>
            <span
                className={
                    strong
                        ? 'text-right text-[13px] font-bold text-black tabular-nums'
                        : 'text-right text-[12px] font-medium text-black tabular-nums'
                }
            >
                {value}
            </span>
        </div>
    );
}

/**
 * Caisse/Receipt — printable proof of payment. Two paper widths: A4 for the
 * office printer and 80 mm for thermal receipt printers. Both are pure black
 * on white and drop the app chrome when printed.
 */
export default function CaisseReceipt({ payment }: Props) {
    const { t } = useTranslation();
    const { props } = usePage();
    const tenant = (props as { tenant?: TenantIdentity | null }).tenant;
    const [paper, setPaper] = useState<PaperWidth>('a4');

    const patientName = payment.patient
        ? `${payment.patient.first_name} ${payment.patient.last_name}`.trim()
        : t('page.caisse_receipt.walk_in');

    const cashier = payment.cashShift?.user?.name ?? '—';
    const receiptNumber = `RC-${String(payment.id).padStart(6, '0')}`;
    const thermal = paper === 'thermal';

    return (
        <AppLayout headerTitle={t('page.caisse_receipt.head_title')}>
            <Head title={t('page.caisse_receipt.head_title')} />

            <div className="mx-auto flex max-w-3xl flex-col gap-6 print:block print:max-w-none">
                <div className="print:hidden">
                    <PageHeader
                        title={t('page.caisse_receipt.title')}
                        description={`${receiptNumber} · ${formatMoney(payment.amount_paid)} FCFA`}
                        breadcrumbs={[
                            { label: t('common.home'), href: '/dashboard' },
                            {
                                label: t('caisse', 'Caisse'),
                                href: route('caisse.index'),
                            },
                            { label: t('page.caisse_receipt.title') },
                        ]}
                        actions={
                            <>
                                <SegmentedControl
                                    size="sm"
                                    value={paper}
                                    onChange={setPaper}
                                    options={[
                                        { value: 'a4', label: 'A4' },
                                        {
                                            value: 'thermal',
                                            label: '80 mm',
                                        },
                                    ]}
                                />
                                <Button
                                    variant="secondary"
                                    icon={ArrowLeftIcon}
                                    href={route('caisse.index')}
                                >
                                    {t('page.caisse_receipt.back')}
                                </Button>
                                <Button
                                    icon={PrinterIcon}
                                    onClick={() => window.print()}
                                >
                                    {t('page.caisse_receipt.print')}
                                </Button>
                            </>
                        }
                    />
                </div>

                <div className="flex justify-center print:block">
                    <article
                        className={
                            thermal
                                ? 'w-[80mm] rounded-lg border border-line bg-white px-4 py-5 text-black shadow-surface print:w-auto print:rounded-none print:border-0 print:px-2 print:py-0 print:shadow-none'
                                : 'w-full rounded-xl border border-line bg-white px-8 py-9 text-black shadow-surface print:rounded-none print:border-0 print:px-0 print:py-0 print:shadow-none'
                        }
                    >
                        <header
                            className={
                                thermal
                                    ? 'border-b border-black/20 pb-3 text-center'
                                    : 'border-b-2 border-black pb-5 text-center'
                            }
                        >
                            <p
                                className={
                                    thermal
                                        ? 'text-[13px] leading-tight font-bold tracking-tight text-black uppercase'
                                        : 'text-[17px] leading-tight font-bold tracking-tight text-black uppercase'
                                }
                            >
                                {tenant?.name ??
                                    t(
                                        'page.radiology_print.hospital_placeholder',
                                    )}
                            </p>
                            <p className="mt-1 text-[9.5px] tracking-[0.16em] text-black/55 uppercase">
                                {t('page.caisse_receipt.document_title')}
                            </p>
                            {!thermal && (
                                <p className="mt-2 font-mono text-[11px] text-black/60 tabular-nums">
                                    {receiptNumber}
                                </p>
                            )}
                        </header>

                        {thermal && (
                            <p className="mt-2 text-center font-mono text-[10px] text-black/60 tabular-nums">
                                {receiptNumber}
                            </p>
                        )}

                        <section className={thermal ? 'mt-3' : 'mt-5'}>
                            <p className="mb-1 text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                                {t('page.caisse_receipt.amount_paid')}
                            </p>
                            <p
                                className={
                                    thermal
                                        ? 'text-[24px] leading-none font-bold text-black tabular-nums'
                                        : 'text-[30px] leading-none font-bold tracking-tight text-black tabular-nums'
                                }
                            >
                                {formatMoney(payment.amount_paid)}
                                <span className="ml-1.5 text-[12px] font-semibold tracking-[0.1em] text-black/55 uppercase">
                                    FCFA
                                </span>
                            </p>
                        </section>

                        <section className={thermal ? 'mt-3' : 'mt-5'}>
                            <p className="mb-1.5 text-[9.5px] font-semibold tracking-[0.14em] text-black/55 uppercase">
                                {t('page.caisse_receipt.details')}
                            </p>
                            <ReceiptRow
                                label={t('page.caisse_receipt.patient')}
                                value={patientName}
                            />
                            {payment.patient?.patient_uid && (
                                <ReceiptRow
                                    label={t(
                                        'page.patient_profile.patient_uid',
                                    )}
                                    value={payment.patient.patient_uid}
                                />
                            )}
                            <ReceiptRow
                                label={t('page.caisse_receipt.invoice_number')}
                                value={
                                    payment.invoice?.invoice_number ??
                                    t('page.caisse_receipt.no_invoice')
                                }
                            />
                            <ReceiptRow
                                label={t('page.caisse_receipt.payment_method')}
                                value={t(
                                    METHOD_KEYS[payment.payment_method] ??
                                        'page.caisse_receipt.method_cash',
                                )}
                            />
                            <ReceiptRow
                                label={t('page.caisse_receipt.cashier')}
                                value={cashier}
                            />
                            <ReceiptRow
                                label={t('page.caisse_receipt.payment_point')}
                                value={payment.cashShift?.payment_point ?? '—'}
                            />
                            <ReceiptRow
                                label={t('page.caisse_receipt.date')}
                                value={formatDateTime(payment.created_at)}
                            />
                        </section>

                        <footer
                            className={
                                thermal
                                    ? 'mt-3 border-t border-black/20 pt-2.5 text-center'
                                    : 'mt-6 border-t border-black/20 pt-4 text-center'
                            }
                        >
                            <p className="text-[12px] font-semibold text-black">
                                {t('page.caisse_receipt.thank_you')}
                            </p>
                            <p className="mt-0.5 text-[11.5px] text-black/70">
                                {t('page.caisse_receipt.thank_you_secondary')}
                            </p>
                            <p className="mt-2 text-[9.5px] leading-relaxed text-black/50">
                                {t('page.caisse_receipt.footer_note')}
                            </p>
                        </footer>
                    </article>
                </div>
            </div>
        </AppLayout>
    );
}
