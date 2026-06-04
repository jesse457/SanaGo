import { DocumentTextIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

export default function MedicalRecordsIndex() {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle={t('page.medical_records_index.title')}
            headerSubtitle={t('page.medical_records_index.subtitle')}
        >
            <Head title={t('page.medical_records_index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.medical_records_index.title') },
                    ]}
                />

                <EmptyState
                    icon={DocumentTextIcon}
                    title={t('page.medical_records_index.heading')}
                    description={t('page.medical_records_index.description')}
                />
            </div>
        </AppLayout>
    );
}
