import { Head } from '@inertiajs/react';
import { PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Certificate } from '@/types';

interface Props {
    certificate: Certificate;
}

/**
 * One row on the printed sheet: quiet uppercase label, then the value. The
 * printable area stays pure `bg-white text-black` so photocopies and PDF
 * exports never inherit the app's tinted surfaces.
 */
function PrintRow({
    label,
    value,
    mutedValue,
}: {
    label: string;
    value: React.ReactNode;
    mutedValue?: React.ReactNode;
}) {
    return (
        <div>
            <span className="text-black/55">{label}:</span>
            <p className="font-medium text-black">{value}</p>
            {mutedValue && <p className="text-black/70">{mutedValue}</p>}
        </div>
    );
}

export default function CertificatesPrint({ certificate }: Props) {
    const { t } = useTranslation();

    const typeLabel =
        certificate.type === 'sick_leave'
            ? t('SICK LEAVE CERTIFICATE')
            : certificate.type === 'medical_fitness'
              ? t('MEDICAL FITNESS CERTIFICATE')
              : t('DEATH CERTIFICATE');

    return (
        <AppLayout headerTitle={t('Print Certificate')}>
            <Head title={t('Print Certificate')} />

            <div className="mx-auto max-w-2xl space-y-6">
                <PageHeader
                    title={t('Print Certificate')}
                    description={typeLabel}
                    bordered={false}
                />

                {/* The sheet itself: plain white paper on the app's canvas. */}
                <article className="rounded-xl border border-line bg-white p-8 text-black shadow-surface print:border-none print:shadow-none">
                    <header className="mb-8 text-center">
                        <h1 className="mb-2 text-xl font-bold text-black uppercase">
                            {typeLabel}
                        </h1>
                        <p className="text-sm text-black/55">
                            {t('Serial #')}: {certificate.serial_number}
                        </p>
                    </header>

                    <div className="space-y-4 text-sm">
                        <div className="grid grid-cols-2 gap-4">
                            <PrintRow
                                label={t('Patient')}
                                value={
                                    <>
                                        {certificate.patient?.last_name}{' '}
                                        {certificate.patient?.first_name}
                                    </>
                                }
                            />
                            <PrintRow
                                label={t('Issued By')}
                                value={certificate.doctor?.name ?? '—'}
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <PrintRow
                                label={t('From')}
                                value={certificate.from_date}
                            />
                            <PrintRow
                                label={t('To')}
                                value={certificate.to_date}
                            />
                        </div>

                        {certificate.duration_days && (
                            <PrintRow
                                label={t('Duration')}
                                value={
                                    <>
                                        {certificate.duration_days} {t('days')}
                                    </>
                                }
                            />
                        )}

                        {certificate.diagnosis_summary && (
                            <div>
                                <span className="text-black/55">
                                    {t('Diagnosis')}:
                                </span>
                                <p className="text-black">
                                    {certificate.diagnosis_summary}
                                </p>
                            </div>
                        )}

                        {certificate.recommendations && (
                            <div>
                                <span className="text-black/55">
                                    {t('Recommendations')}:
                                </span>
                                <p className="text-black">
                                    {certificate.recommendations}
                                </p>
                            </div>
                        )}

                        {certificate.employer_name && (
                            <PrintRow
                                label={t('Employer')}
                                value={certificate.employer_name}
                                mutedValue={certificate.employer_address}
                            />
                        )}
                    </div>

                    <footer className="mt-8 border-t border-black/15 pt-6 text-center text-xs text-black/55">
                        <p>
                            {t(
                                'This certificate was generated electronically and is valid without a signature.',
                            )}
                        </p>
                        <p className="mt-1">
                            {t('Issued on')} {new Date().toLocaleDateString()}
                        </p>
                    </footer>
                </article>
            </div>
        </AppLayout>
    );
}
