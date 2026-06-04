import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { DocumentTextIcon } from '@heroicons/react/24/outline';

export default function MedicalRecordsIndex() {
    const { t } = useTranslation();
    return (
        <AppLayout
            headerTitle={t('page.medical_records_index.title')}
            headerSubtitle={t('page.medical_records_index.subtitle')}
        >
            <Head title={t('page.medical_records_index.head_title')} />
            <div className="mx-auto max-w-7xl">
                <div className="rounded-xl border border-dashed border-gray-200 bg-white p-16 text-center dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-xl bg-gray-50 dark:bg-neutral-700">
                        <DocumentTextIcon className="h-10 w-10 text-gray-300 dark:text-neutral-500" />
                    </div>
                    <h3 className="mb-2 text-xl font-bold text-gray-900 dark:text-neutral-100">
                        {t('page.medical_records_index.heading')}
                    </h3>
                    <p className="mx-auto max-w-xs text-sm font-medium text-gray-500 dark:text-neutral-400">
                        {t('page.medical_records_index.description')}
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
