import {
    ArrowLeftIcon,
    CameraIcon,
    DocumentTextIcon,
    CheckBadgeIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Field,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { RadiologyStatusBadge } from '@/pages/Radiology/components';
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

    const handleSubmitResult = (e: FormEvent) => {
        e.preventDefault();
        post(route('radiology.results.validate', req.result?.id), {
            onSuccess: () => {
                // Page will reload with updated data
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('Radiology Request Detail')}
            headerSubtitle={`${req.imaging_type} — ${req.body_part}`}
        >
            <Head title={t('Radiology Request')} />

            <div className="mx-auto flex max-w-4xl flex-col gap-6">
                <PageHeader
                    title={t('Radiology Request Detail')}
                    description={`${req.imaging_type.replace('_', ' ')} — ${req.body_part.replace('_', ' ')}`}
                    breadcrumbs={[
                        { label: t('Lab & Diagnostics') },
                        {
                            label: t('Radiology'),
                            href: route('radiology.requests.index'),
                        },
                        { label: `#${req.id}` },
                    ]}
                    meta={
                        <RadiologyStatusBadge
                            status={req.status}
                            label={t(req.status)}
                            dot
                        />
                    }
                    actions={
                        <Button
                            variant="secondary"
                            icon={ArrowLeftIcon}
                            href={route('radiology.requests.index')}
                        >
                            {t('Back to Requests')}
                        </Button>
                    }
                />

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <SectionCard title={t('Request Details')} icon={CameraIcon}>
                        <KeyValueGrid columns={1}>
                            <KeyValue
                                label={t('Patient')}
                                value={
                                    req.patient
                                        ? `${req.patient.first_name} ${req.patient.last_name}`
                                        : '—'
                                }
                            />
                            <KeyValue
                                label={t('Imaging Type')}
                                value={req.imaging_type.replace('_', ' ')}
                            />
                            <KeyValue
                                label={t('Body Part')}
                                value={req.body_part.replace('_', ' ')}
                            />
                            <KeyValue
                                label={t('Requested By')}
                                value={req.requested_by_user?.name ?? '—'}
                            />
                            <KeyValue
                                label={t('Status')}
                                value={
                                    <RadiologyStatusBadge
                                        status={req.status}
                                        label={t(req.status)}
                                    />
                                }
                            />
                        </KeyValueGrid>
                    </SectionCard>

                    {req.clinical_notes && (
                        <SectionCard
                            title={t('Clinical Notes')}
                            icon={DocumentTextIcon}
                        >
                            <p className="text-[13px] leading-relaxed text-ink-muted">
                                {req.clinical_notes}
                            </p>
                        </SectionCard>
                    )}
                </div>

                {req.result && (
                    <SectionCard
                        title={t('Radiology Result')}
                        icon={CheckBadgeIcon}
                    >
                        <div className="space-y-4">
                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label={t('Performed By')}
                                    value={
                                        req.result.performed_by_user?.name ??
                                        '—'
                                    }
                                />
                                <KeyValue
                                    label={t('Validated By')}
                                    value={
                                        req.result.validated_by_user?.name ??
                                        '—'
                                    }
                                />
                                <KeyValue
                                    label={t('Status')}
                                    value={t(req.result.status)}
                                />
                                {req.result.validated_at && (
                                    <KeyValue
                                        label={t('Validated At')}
                                        value={new Date(
                                            req.result.validated_at,
                                        ).toLocaleString()}
                                    />
                                )}
                            </KeyValueGrid>

                            {req.result.findings && (
                                <div>
                                    <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                        {t('Findings')}
                                    </p>
                                    <p className="mt-1.5 rounded-lg bg-surface-muted px-3.5 py-3 text-[13px] leading-relaxed text-ink-muted">
                                        {req.result.findings}
                                    </p>
                                </div>
                            )}

                            {req.result.conclusion && (
                                <div>
                                    <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                        {t('Conclusion')}
                                    </p>
                                    <p className="mt-1.5 rounded-lg bg-surface-muted px-3.5 py-3 text-[13px] leading-relaxed text-ink-muted">
                                        {req.result.conclusion}
                                    </p>
                                </div>
                            )}
                        </div>
                    </SectionCard>
                )}

                {req.status !== 'validated' && req.status !== 'completed' && (
                    <SectionCard
                        title={t('Enter Result')}
                        icon={DocumentTextIcon}
                        description={t(
                            'Record findings and conclusion for this radiology exam',
                        )}
                        footer={
                            <Button type="submit" form="radiology-result-form">
                                {processing
                                    ? t('Saving...')
                                    : t('Save & Validate Result')}
                            </Button>
                        }
                    >
                        <form
                            id="radiology-result-form"
                            onSubmit={handleSubmitResult}
                            className="space-y-4"
                        >
                            <Field
                                label={t('Findings')}
                                htmlFor="findings"
                                required
                                error={errors.findings}
                            >
                                <Textarea
                                    id="findings"
                                    value={data.findings}
                                    onChange={(e) =>
                                        setData('findings', e.target.value)
                                    }
                                    rows={4}
                                    invalid={Boolean(errors.findings)}
                                />
                            </Field>

                            <Field
                                label={t('Conclusion')}
                                htmlFor="conclusion"
                                required
                                error={errors.conclusion}
                            >
                                <Textarea
                                    id="conclusion"
                                    value={data.conclusion}
                                    onChange={(e) =>
                                        setData('conclusion', e.target.value)
                                    }
                                    rows={3}
                                    invalid={Boolean(errors.conclusion)}
                                />
                            </Field>
                        </form>
                    </SectionCard>
                )}
            </div>
        </AppLayout>
    );
}
