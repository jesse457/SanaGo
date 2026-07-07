import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeftIcon, CameraIcon, UserIcon, DocumentTextIcon, CheckBadgeIcon } from '@heroicons/react/24/outline';
import type { FormEvent } from 'react';
import DetailItem from '@/Components/DetailItem';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { RadiologyRequest } from '@/types';

interface Props {
    request: RadiologyRequest;
}

export default function RadiologyRequestsShow({ request: req }: Props) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm({
        findings: req.result?.findings ?? '',
        conclusion: req.result?.conclusion ?? '',
    });

    const statusColor = (status: string) => {
        switch (status) {
            case 'pending':
                return 'amber';
            case 'in_progress':
                return 'blue';
            case 'completed':
                return 'emerald';
            case 'validated':
                return 'purple';
            default:
                return 'gray';
        }
    };

    const handleSubmitResult = (e: FormEvent) => {
        e.preventDefault();
        post(route('radiology.results.validate', req.result?.id), {
            onSuccess: () => {
                // Page will reload with updated data
            },
        });
    };

    return (
        <AppLayout headerTitle={t('Radiology Request Detail')} headerSubtitle={`${req.imaging_type} — ${req.body_part}`}>
            <Head title={t('Radiology Request')} />
            <PageBreadcrumb items={[{ label: t('Lab & Diagnostics') }, { label: t('Radiology'), href: route('radiology.requests.index') }, { label: `#${req.id}` }]} />

            <div className="max-w-4xl mx-auto space-y-6">
                <Link href={route('radiology.requests.index')} className="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-neutral-400 hover:text-blue-600">
                    <ArrowLeftIcon className="h-4 w-4" />
                    {t('Back to Requests')}
                </Link>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <PageSection title={t('Request Details')} icon={CameraIcon}>
                        <div className="space-y-3">
                            <DetailItem label={t('Patient')} value={req.patient ? `${req.patient.first_name} ${req.patient.last_name}` : '—'} />
                            <DetailItem label={t('Imaging Type')} value={req.imaging_type.replace('_', ' ')} />
                            <DetailItem label={t('Body Part')} value={req.body_part.replace('_', ' ')} />
                            <DetailItem label={t('Requested By')} value={req.requested_by_user?.name ?? '—'} />
                            <div className="flex items-center gap-2">
                                <span className="text-xs uppercase text-gray-500 dark:text-neutral-400">{t('Status')}:</span>
                                <StatusBadge label={t(req.status)} color={statusColor(req.status)} />
                            </div>
                        </div>
                    </PageSection>

                    {req.clinical_notes && (
                        <PageSection title={t('Clinical Notes')} icon={DocumentTextIcon}>
                            <p className="text-sm text-gray-700 dark:text-neutral-300">{req.clinical_notes}</p>
                        </PageSection>
                    )}
                </div>

                {req.result && (
                    <PageSection title={t('Radiology Result')} icon={CheckBadgeIcon}>
                        <div className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <DetailItem label={t('Performed By')} value={req.result.performed_by_user?.name ?? '—'} />
                                <DetailItem label={t('Validated By')} value={req.result.validated_by_user?.name ?? '—'} />
                                <DetailItem label={t('Status')} value={t(req.result.status)} />
                                {req.result.validated_at && <DetailItem label={t('Validated At')} value={new Date(req.result.validated_at).toLocaleString()} />}
                            </div>
                            {req.result.findings && (
                                <div>
                                    <h4 className="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">{t('Findings')}</h4>
                                    <p className="text-sm text-gray-600 dark:text-neutral-400 bg-gray-50 dark:bg-neutral-900 p-3 rounded-lg">{req.result.findings}</p>
                                </div>
                            )}
                            {req.result.conclusion && (
                                <div>
                                    <h4 className="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">{t('Conclusion')}</h4>
                                    <p className="text-sm text-gray-600 dark:text-neutral-400 bg-gray-50 dark:bg-neutral-900 p-3 rounded-lg">{req.result.conclusion}</p>
                                </div>
                            )}
                        </div>
                    </PageSection>
                )}

                {req.status !== 'validated' && req.status !== 'completed' && (
                    <PageSection title={t('Enter Result')} icon={DocumentTextIcon} description={t('Record findings and conclusion for this radiology exam')}>
                        <form onSubmit={handleSubmitResult} className="space-y-4">
                            <FormTextarea name="findings" label={t('Findings')} value={data.findings} onChange={(v) => setData('findings', v)} errors={errors} rows={4} required />
                            <FormTextarea name="conclusion" label={t('Conclusion')} value={data.conclusion} onChange={(v) => setData('conclusion', v)} errors={errors} rows={3} required />
                            <div className="flex justify-end">
                                <button type="submit" disabled={processing} className="px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 cursor-pointer">
                                    {processing ? t('Saving...') : t('Save & Validate Result')}
                                </button>
                            </div>
                        </form>
                    </PageSection>
                )}
            </div>
        </AppLayout>
    );
}
