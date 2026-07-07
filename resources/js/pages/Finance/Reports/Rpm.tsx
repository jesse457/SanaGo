import { Head } from '@inertiajs/react';
import { DocumentTextIcon, BanknotesIcon, UserGroupIcon, ClipboardDocumentCheckIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

export default function RpmReport() {
    const { t } = useTranslation();
    const [dateRange, setDateRange] = useState({ from: '', to: '' });

    return (
        <AppLayout headerTitle={t('MINSANTE RPM Report')} headerSubtitle={t('Recettes-Prestations-Maladies — Legally required financial report for the Ministry of Health')}>
            <Head title={t('RPM Report')} />
            <PageBreadcrumb items={[{ label: t('Finance') }, { label: t('Reports') }, { label: t('RPM') }]} />

            <div className="max-w-5xl mx-auto space-y-6">
                <div className="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">{t('Report Period')}</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-xs uppercase text-gray-500 dark:text-neutral-400 mb-1">{t('From')}</label>
                            <input
                                type="date"
                                value={dateRange.from}
                                onChange={(e) => setDateRange({ ...dateRange, from: e.target.value })}
                                className="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"
                            />
                        </div>
                        <div>
                            <label className="block text-xs uppercase text-gray-500 dark:text-neutral-400 mb-1">{t('To')}</label>
                            <input
                                type="date"
                                value={dateRange.to}
                                onChange={(e) => setDateRange({ ...dateRange, to: e.target.value })}
                                className="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"
                            />
                        </div>
                    </div>
                    <button className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                        {t('Generate Report')}
                    </button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <StatCard icon={BanknotesIcon} label={t('Total Revenue')} value="—" color="blue" />
                    <StatCard icon={UserGroupIcon} label={t('Total Patients')} value="—" color="emerald" />
                    <StatCard icon={ClipboardDocumentCheckIcon} label={t('Consultations')} value="—" color="purple" />
                    <StatCard icon={DocumentTextIcon} label={t('Claims Submitted')} value="—" color="amber" />
                </div>

                <div className="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-8 text-center">
                    <DocumentTextIcon className="h-12 w-12 mx-auto text-gray-300 dark:text-neutral-600 mb-4" />
                    <h3 className="text-base font-medium text-gray-700 dark:text-neutral-300 mb-2">{t('MINSANTE RPM Report')}</h3>
                    <p className="text-sm text-gray-500 dark:text-neutral-400 max-w-md mx-auto">
                        {t('Select a date range and click Generate Report to create the legally required RPM (Recettes-Prestations-Maladies) report for Cameroon\'s Ministry of Public Health.')}
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
