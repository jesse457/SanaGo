import {
    DocumentTextIcon,
    BanknotesIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatCard from '@/Components/StatCard';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

export default function RpmReport() {
    const { t } = useTranslation();
    const [dateRange, setDateRange] = useState({ from: '', to: '' });

    return (
        <AppLayout
            headerTitle={t('MINSANTE RPM Report')}
            headerSubtitle={t(
                'Recettes-Prestations-Maladies — Legally required financial report for the Ministry of Health',
            )}
        >
            <Head title={t('RPM Report')} />
            <PageBreadcrumb
                items={[
                    { label: t('Finance') },
                    { label: t('Reports') },
                    { label: t('RPM') },
                ]}
            />

            <div className="mx-auto max-w-5xl space-y-6">
                <div className="rounded-xl border border-line bg-surface p-6">
                    <h2 className="mb-4 text-lg font-semibold text-ink">
                        {t('Report Period')}
                    </h2>
                    <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="mb-1 block text-xs text-ink-subtle uppercase">
                                {t('From')}
                            </label>
                            <input
                                type="date"
                                value={dateRange.from}
                                onChange={(e) =>
                                    setDateRange({
                                        ...dateRange,
                                        from: e.target.value,
                                    })
                                }
                                className="w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm text-ink outline-none focus:border-accent focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-xs text-ink-subtle uppercase">
                                {t('To')}
                            </label>
                            <input
                                type="date"
                                value={dateRange.to}
                                onChange={(e) =>
                                    setDateRange({
                                        ...dateRange,
                                        to: e.target.value,
                                    })
                                }
                                className="w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm text-ink outline-none focus:border-accent focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                    </div>
                    <button className="cursor-pointer rounded-lg bg-accent px-4 py-2 text-sm font-medium text-ink-inverse transition-colors hover:bg-accent-hover">
                        {t('Generate Report')}
                    </button>
                </div>

                <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <StatCard
                        icon={BanknotesIcon}
                        label={t('Total Revenue')}
                        value="—"
                        color="blue"
                    />
                    <StatCard
                        icon={UserGroupIcon}
                        label={t('Total Patients')}
                        value="—"
                        color="emerald"
                    />
                    <StatCard
                        icon={ClipboardDocumentCheckIcon}
                        label={t('Consultations')}
                        value="—"
                        color="purple"
                    />
                    <StatCard
                        icon={DocumentTextIcon}
                        label={t('Claims Submitted')}
                        value="—"
                        color="amber"
                    />
                </div>

                <div className="rounded-xl border border-line bg-surface p-8 text-center">
                    <DocumentTextIcon className="mx-auto mb-4 h-12 w-12 text-ink-subtle" />
                    <h3 className="mb-2 text-base font-medium text-ink-muted">
                        {t('MINSANTE RPM Report')}
                    </h3>
                    <p className="mx-auto max-w-md text-sm text-ink-subtle">
                        {t(
                            "Select a date range and click Generate Report to create the legally required RPM (Recettes-Prestations-Maladies) report for Cameroon's Ministry of Public Health.",
                        )}
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
