import { DocumentTextIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { Button, Card, EmptyState, PageHeader } from '@/Components/ui';
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

            <div className="mx-auto max-w-7xl space-y-5 pb-12">
                <PageHeader
                    title={t('page.medical_records_index.title')}
                    description={t('page.medical_records_index.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        { label: t('page.medical_records_index.title') },
                    ]}
                    actions={
                        <Button
                            href="/consultations/create"
                            variant="secondary"
                        >
                            {t('common.new')}
                        </Button>
                    }
                />

                <Card>
                    <EmptyState
                        panel={false}
                        icon={DocumentTextIcon}
                        title={t('page.medical_records_index.heading')}
                        description={t(
                            'page.medical_records_index.description',
                        )}
                    />
                </Card>
            </div>
        </AppLayout>
    );
}
