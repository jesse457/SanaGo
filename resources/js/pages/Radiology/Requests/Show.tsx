import {
    ArrowLeftIcon,
    CameraIcon,
    CheckBadgeIcon,
    DocumentArrowDownIcon,
    DocumentTextIcon,
    PencilSquareIcon,
    PrinterIcon,
} from '@heroicons/react/24/outline';
import { Head, router, usePage } from '@inertiajs/react';
import {
    Button,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    humanise,
    imagingTypeLabel,
    RadiologyPatientIdentity,
    RadiologyStatusBadge,
} from '@/pages/Radiology/components';

interface RequestRecord {
    id: number;
    status: string;
    request_date?: string | null;
    imaging_type: string;
    body_part: string;
    clinical_notes?: string | null;
    patient?: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid?: string;
        date_of_birth?: string | null;
        gender?: string | null;
    } | null;
    requested_by?: { id: number; name: string } | null;
    result?: {
        id: number;
        request_id: number;
        status: string;
        findings?: string | null;
        conclusion?: string | null;
        performed_by?: string | null;
        validated_by?: string | null;
        validated_at?: string | null;
        report_url?: string | null;
    } | null;
}

interface Props {
    request: RequestRecord;
}

/** Permission check against the shared `auth.user.permissions` list. */
function useCan(permission: string): boolean {
    const { props } = usePage();
    const permissions = ((props.auth as any)?.user?.permissions ??
        []) as string[];

    return permissions.includes(permission);
}

export default function RadiologyRequestsShow({ request }: Props) {
    const { t } = useTranslation();
    const canValidate = useCan('validate-radiology');

    const statusLabel = (status: string) => {
        const fallback = humanise(status);

        return t(`radiology.status.${status}`, fallback);
    };

    const reportHref = request.result
        ? route('radiology.results.edit', request.result.id)
        : route('radiology.results.create', { request_id: request.id });

    const handleValidate = () => {
        if (!request.result) {
            return;
        }

        router.post(
            route('radiology.results.validate', request.result.id),
            {},
            { preserveScroll: true },
        );
    };

    return (
        <AppLayout
            headerTitle={t('radiology.requests.detail_title')}
            headerSubtitle={`${imagingTypeLabel(request.imaging_type)} · ${humanise(request.body_part)}`}
        >
            <Head title={t('radiology.requests.detail_title')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    eyebrow={t('radiology.requests.imaging_requests')}
                    title={t('radiology.requests.detail_title')}
                    description={`${imagingTypeLabel(request.imaging_type)} · ${humanise(request.body_part)}`}
                    breadcrumbs={[
                        {
                            label: t('radiology.requests.imaging_requests'),
                            href: route('radiology.requests.index'),
                        },
                        { label: `#${request.id}` },
                    ]}
                    meta={
                        <RadiologyStatusBadge
                            status={request.status}
                            label={statusLabel(request.status)}
                            dot
                        />
                    }
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                icon={ArrowLeftIcon}
                                href={route('radiology.requests.index')}
                            >
                                {t('radiology.requests.back_to_requests')}
                            </Button>
                            <Button
                                href={reportHref}
                                icon={
                                    request.result
                                        ? PencilSquareIcon
                                        : CameraIcon
                                }
                            >
                                {request.result
                                    ? t('radiology.requests.open_report')
                                    : t('radiology.requests.process_result')}
                            </Button>
                        </>
                    }
                />

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <SectionCard
                        title={t('radiology.requests.order')}
                        icon={CameraIcon}
                    >
                        <KeyValueGrid columns={1}>
                            <KeyValue
                                label={t('radiology.requests.patient')}
                                value={
                                    request.patient ? (
                                        <RadiologyPatientIdentity
                                            request={{
                                                patient: request.patient,
                                            }}
                                        />
                                    ) : (
                                        '—'
                                    )
                                }
                            />
                            <KeyValue
                                label={t('radiology.requests.imaging_type')}
                                value={imagingTypeLabel(request.imaging_type)}
                            />
                            <KeyValue
                                label={t('radiology.requests.body_part')}
                                value={humanise(request.body_part)}
                            />
                            <KeyValue
                                label={t('radiology.requests.referred_by')}
                                value={request.requested_by?.name ?? '—'}
                            />
                            <KeyValue
                                label={t('radiology.requests.requested_on')}
                                value={formatDate(request.request_date)}
                            />
                        </KeyValueGrid>
                    </SectionCard>

                    <SectionCard
                        title={t('radiology.requests.clinical_notes')}
                        icon={DocumentTextIcon}
                    >
                        <p className="text-[13px] leading-relaxed text-ink-muted">
                            {request.clinical_notes?.trim() ||
                                t('radiology.requests.no_clinical_notes')}
                        </p>
                    </SectionCard>
                </div>

                {request.result ? (
                    <SectionCard
                        title={t('radiology.results.enter.report')}
                        icon={CheckBadgeIcon}
                        description={t(
                            'radiology.results.enter.report_description',
                        )}
                    >
                        <KeyValueGrid columns={3}>
                            <KeyValue
                                label={t('radiology.results.enter.status')}
                                value={
                                    <RadiologyStatusBadge
                                        status={request.result.status}
                                        label={statusLabel(
                                            request.result.status,
                                        )}
                                        dot
                                    />
                                }
                            />
                            <KeyValue
                                label={t(
                                    'radiology.results.enter.performed_by',
                                )}
                                value={request.result.performed_by ?? '—'}
                            />
                            <KeyValue
                                label={t(
                                    'radiology.results.enter.validated_by',
                                )}
                                value={request.result.validated_by ?? '—'}
                            />
                        </KeyValueGrid>

                        <div className="mt-4 space-y-3">
                            <ReportBlock
                                label={t('radiology.results.enter.findings')}
                                value={request.result.findings}
                            />
                            <ReportBlock
                                label={t('radiology.results.enter.conclusion')}
                                value={request.result.conclusion}
                            />
                        </div>

                        <div className="mt-5 flex flex-wrap items-center gap-2 border-t border-line pt-4">
                            {request.result.report_url && (
                                <Button
                                    variant="secondary"
                                    icon={DocumentArrowDownIcon}
                                    href={request.result.report_url}
                                >
                                    {t(
                                        'radiology.results.enter.download_report',
                                    )}
                                </Button>
                            )}
                            {canValidate &&
                                request.result.status !== 'validated' && (
                                    <Button
                                        icon={CheckBadgeIcon}
                                        onClick={handleValidate}
                                    >
                                        {t(
                                            'radiology.results.enter.validate_report',
                                        )}
                                    </Button>
                                )}
                        </div>
                    </SectionCard>
                ) : (
                    <SectionCard
                        title={t('radiology.results.enter.no_report')}
                        icon={DocumentTextIcon}
                        description={t(
                            'radiology.results.enter.no_report_description',
                        )}
                        footer={
                            <Button href={reportHref} icon={CameraIcon}>
                                {t('radiology.requests.process_result')}
                            </Button>
                        }
                    >
                        <p className="text-[13px] text-ink-muted">
                            {t('radiology.results.enter.no_report_body')}
                        </p>
                    </SectionCard>
                )}

                <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                    <Button
                        variant="ghost"
                        icon={PrinterIcon}
                        href={route('radiology.requests.print', request.id)}
                    >
                        {t('radiology.requests.print')}
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}

function formatDate(value?: string | null): string {
    return value ? new Date(value).toLocaleDateString() : '—';
}

function ReportBlock({
    label,
    value,
}: {
    label: string;
    value?: string | null;
}) {
    return (
        <div>
            <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                {label}
            </p>
            <p className="mt-1.5 rounded-lg bg-surface-muted px-3.5 py-3 text-[13px] leading-relaxed whitespace-pre-line text-ink-muted">
                {value?.trim() || '—'}
            </p>
        </div>
    );
}
