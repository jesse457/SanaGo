import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { Certificate } from '@/types';

interface Props {
    certificate: Certificate;
}

export default function CertificatesPrint({ certificate }: Props) {
    const { t } = useTranslation();

    const typeLabel =
        certificate.type === 'sick_leave' ? t('SICK LEAVE CERTIFICATE') :
        certificate.type === 'medical_fitness' ? t('MEDICAL FITNESS CERTIFICATE') :
        t('DEATH CERTIFICATE');

    return (
        <AppLayout headerTitle={t('Print Certificate')}>
            <Head title={t('Print Certificate')} />

            <div className="max-w-2xl mx-auto">
                <div className="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-8 print:shadow-none print:border-none">
                    <div className="text-center mb-8">
                        <h1 className="text-xl font-bold text-gray-900 dark:text-white uppercase mb-2">{typeLabel}</h1>
                        <p className="text-sm text-gray-500 dark:text-neutral-400">{t('Serial #')}: {certificate.serial_number}</p>
                    </div>

                    <div className="space-y-4 text-sm">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('Patient')}:</span>
                                <p className="font-medium text-gray-900 dark:text-white">
                                    {certificate.patient?.last_name} {certificate.patient?.first_name}
                                </p>
                            </div>
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('Issued By')}:</span>
                                <p className="font-medium text-gray-900 dark:text-white">{certificate.doctor?.name ?? '—'}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('From')}:</span>
                                <p className="font-medium text-gray-900 dark:text-white">{certificate.from_date}</p>
                            </div>
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('To')}:</span>
                                <p className="font-medium text-gray-900 dark:text-white">{certificate.to_date}</p>
                            </div>
                        </div>

                        {certificate.duration_days && (
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('Duration')}:</span>
                                <p className="font-medium text-gray-900 dark:text-white">{certificate.duration_days} {t('days')}</p>
                            </div>
                        )}

                        {certificate.diagnosis_summary && (
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('Diagnosis')}:</span>
                                <p className="text-gray-900 dark:text-white">{certificate.diagnosis_summary}</p>
                            </div>
                        )}

                        {certificate.recommendations && (
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('Recommendations')}:</span>
                                <p className="text-gray-900 dark:text-white">{certificate.recommendations}</p>
                            </div>
                        )}

                        {certificate.employer_name && (
                            <div>
                                <span className="text-gray-500 dark:text-neutral-400">{t('Employer')}:</span>
                                <p className="font-medium text-gray-900 dark:text-white">{certificate.employer_name}</p>
                                {certificate.employer_address && <p className="text-gray-700 dark:text-neutral-300">{certificate.employer_address}</p>}
                            </div>
                        )}
                    </div>

                    <div className="mt-8 pt-6 border-t border-gray-200 dark:border-neutral-700 text-center text-xs text-gray-400 dark:text-neutral-500">
                        <p>{t('This certificate was generated electronically and is valid without a signature.')}</p>
                        <p className="mt-1">{t('Issued on')} {new Date().toLocaleDateString()}</p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
