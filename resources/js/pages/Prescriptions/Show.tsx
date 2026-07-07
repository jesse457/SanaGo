import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeftIcon, DocumentTextIcon, UserIcon, BeakerIcon, CheckCircleIcon } from '@heroicons/react/24/outline';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { Prescription } from '@/types';

interface Props {
    prescription: Prescription;
}

export default function PrescriptionsShow({ prescription }: Props) {
    const { t } = useTranslation();

    const statusColor = (status: string) => {
        switch (status) {
            case 'active':
            case 'new':
                return 'blue';
            case 'dispensed':
            case 'completed':
                return 'emerald';
            case 'cancelled':
                return 'red';
            default:
                return 'gray';
        }
    };

    const handleDispense = () => {
        if (confirm(t('Mark this prescription as dispensed?'))) {
            router.post(route('prescriptions.dispense', prescription.id));
        }
    };

    return (
        <AppLayout headerTitle={t('Prescription Detail')} headerSubtitle={`${t('Prescribed by')} ${prescription.doctor?.name ?? '—'}`}>
            <Head title={t('Prescription Detail')} />
            <PageBreadcrumb items={[{ label: t('Clinical') }, { label: t('Prescriptions'), href: route('prescriptions.index') }, { label: `#${prescription.id}` }]} />

            <div className="max-w-4xl mx-auto space-y-6">
                <div className="flex items-center justify-between">
                    <Link href={route('prescriptions.index')} className="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-neutral-400 hover:text-blue-600">
                        <ArrowLeftIcon className="h-4 w-4" />
                        {t('Back to Prescriptions')}
                    </Link>
                    <div className="flex items-center gap-3">
                        <StatusBadge label={t(prescription.status)} color={statusColor(prescription.status)} />
                        {prescription.status !== 'dispensed' && prescription.status !== 'completed' && prescription.status !== 'cancelled' && (
                            <button onClick={handleDispense} className="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 cursor-pointer">
                                <CheckCircleIcon className="h-4 w-4" />
                                {t('Dispense')}
                            </button>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <PageSection title={t('Patient Information')} icon={UserIcon}>
                        <div className="space-y-3">
                            <DetailItem label={t('Patient')} value={prescription.patient ? `${prescription.patient.first_name} ${prescription.patient.last_name}` : '—'} />
                            <DetailItem label={t('Doctor')} value={prescription.doctor?.name ?? '—'} />
                            <DetailItem label={t('Date')} value={new Date(prescription.prescription_date).toLocaleDateString()} />
                            <DetailItem label={t('Total')} value={`${Number(prescription.total_amount).toLocaleString()} XAF`} />
                        </div>
                    </PageSection>

                    {prescription.notes && (
                        <PageSection title={t('Notes')} icon={DocumentTextIcon}>
                            <p className="text-sm text-gray-700 dark:text-neutral-300">{prescription.notes}</p>
                        </PageSection>
                    )}
                </div>

                <PageSection title={t('Medications')} icon={BeakerIcon}>
                    {prescription.items && prescription.items.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="bg-gray-50 dark:bg-neutral-900">
                                    <tr>
                                        <th className="px-4 py-2 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Medication')}</th>
                                        <th className="px-4 py-2 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Dosage')}</th>
                                        <th className="px-4 py-2 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Frequency')}</th>
                                        <th className="px-4 py-2 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Duration')}</th>
                                        <th className="px-4 py-2 text-right font-medium text-gray-600 dark:text-neutral-400">{t('Qty')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {prescription.items.map((item) => (
                                        <tr key={item.id}>
                                            <td className="px-4 py-2 text-gray-900 dark:text-white font-medium">{item.drug_name}</td>
                                            <td className="px-4 py-2 text-gray-700 dark:text-neutral-300">{item.dosage}</td>
                                            <td className="px-4 py-2 text-gray-700 dark:text-neutral-300">{item.frequency}</td>
                                            <td className="px-4 py-2 text-gray-700 dark:text-neutral-300">{item.duration}</td>
                                            <td className="px-4 py-2 text-right text-gray-700 dark:text-neutral-300">{item.quantity}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="text-sm text-gray-500 dark:text-neutral-400">{t('No medications in this prescription.')}</p>
                    )}
                </PageSection>
            </div>
        </AppLayout>
    );
}
