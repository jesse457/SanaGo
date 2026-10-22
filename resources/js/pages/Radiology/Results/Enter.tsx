import {
    CameraIcon,
    ChatBubbleLeftEllipsisIcon,
    CheckIcon,
    CloudArrowUpIcon,
    DocumentCheckIcon,
    DocumentMagnifyingGlassIcon,
    DocumentTextIcon,
    MagnifyingGlassIcon,
    UserCircleIcon,
    XMarkIcon,
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
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    humanise,
    imagingTypeLabel,
    RADIOLOGY_STATUS_LABEL_KEYS,
    RadiologyRequestSummary,
    RadiologyStatusBadge,
} from '@/pages/Radiology/components';
import type { RadiologyRequestRow } from '@/pages/Radiology/components';

interface ResultRecord {
    id: number;
    request_id: number;
    status: string;
    findings?: string | null;
    conclusion?: string | null;
    report_date?: string | null;
    performed_by?: { id: number; name: string } | null;
    validated_by?: { id: number; name: string } | null;
}

interface Props {
    request: RadiologyRequestRow & {
        clinical_notes?: string | null;
        patient?: {
            first_name: string;
            last_name: string;
            patient_uid?: string;
            date_of_birth?: string | null;
            gender?: string | null;
        } | null;
    };
    /** Provided only when an existing report is being edited. */
    result: ResultRecord | null;
    isEdit: boolean;
}

export default function EnterRadiologyResult({
    request,
    result,
    isEdit,
}: Props) {
    const { t } = useTranslation();

    const { data, setData, post, put, processing, errors } = useForm({
        request_id: request.id,
        status: result?.status || 'in_progress',
        findings: result?.findings || '',
        conclusion: result?.conclusion || '',
        attachments: [] as File[],
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();

        if (isEdit && result) {
            put(route('radiology.results.update', result.id), {
                forceFormData: true,
            });

            return;
        }

        post(route('radiology.results.store'), { forceFormData: true });
    };

    const title = isEdit
        ? t('radiology.results.enter.edit_title')
        : t('radiology.results.enter.create_title');

    const labelForStatus = (status: string) => {
        const fallback = status.replace(/_/g, ' ');
        const key = RADIOLOGY_STATUS_LABEL_KEYS[status];

        return key ? t(key, fallback) : fallback;
    };

    const requestStatusLabel = labelForStatus(request.status);

    return (
        <AppLayout
            headerTitle={title}
            headerSubtitle={t('radiology.results.enter.subtitle')}
        >
            <Head title={t('radiology.results.enter.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={CameraIcon}
                    eyebrow={t('radiology.results.enter.breadcrumb')}
                    title={title}
                    description={t('radiology.results.enter.subtitle')}
                    breadcrumbs={[
                        {
                            label: t('radiology.requests.imaging_requests'),
                            href: route('radiology.requests.index'),
                        },
                        { label: t('radiology.results.enter.breadcrumb') },
                    ]}
                    meta={
                        <RadiologyStatusBadge
                            status={data.status}
                            label={labelForStatus(data.status)}
                            dot
                        />
                    }
                />

                <RadiologyRequestSummary
                    patient={request.patient ?? null}
                    imagingType={request.imaging_type}
                    bodyPart={request.body_part}
                    status={request.status}
                    statusLabel={requestStatusLabel}
                    labels={{
                        patient: t('radiology.results.enter.subject_patient'),
                        exam: t('radiology.results.enter.exam'),
                        lifecycle: t(
                            'radiology.results.enter.current_lifecycle',
                        ),
                    }}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <SectionCard
                        icon={DocumentMagnifyingGlassIcon}
                        title={t('radiology.results.enter.order')}
                        description={t(
                            'radiology.results.enter.order_description',
                        )}
                    >
                        <KeyValueGrid columns={3}>
                            <KeyValue
                                label={t(
                                    'radiology.results.enter.imaging_type',
                                )}
                                value={imagingTypeLabel(request.imaging_type)}
                            />
                            <KeyValue
                                label={t('radiology.results.enter.body_part')}
                                value={humanise(request.body_part)}
                            />
                            <KeyValue
                                label={t('radiology.results.enter.referred_by')}
                                value={request.requested_by?.name ?? '—'}
                            />
                        </KeyValueGrid>

                        {request.clinical_notes && (
                            <div className="mt-4 rounded-lg bg-surface-muted px-3.5 py-3">
                                <p className="text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                    {t(
                                        'radiology.results.enter.clinical_notes',
                                    )}
                                </p>
                                <p className="mt-1.5 text-[13px] leading-relaxed text-ink-muted">
                                    {request.clinical_notes}
                                </p>
                            </div>
                        )}
                    </SectionCard>

                    <SectionCard
                        icon={DocumentCheckIcon}
                        title={t('radiology.results.enter.status_assignment')}
                    >
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field
                                label={t(
                                    'radiology.results.enter.status_assignment',
                                )}
                                htmlFor="status"
                                required
                                error={errors.status}
                            >
                                <Select
                                    id="status"
                                    value={data.status}
                                    onChange={(event) =>
                                        setData('status', event.target.value)
                                    }
                                    invalid={Boolean(errors.status)}
                                >
                                    <option value="in_progress">
                                        {t('radiology.status.in_progress')}
                                    </option>
                                    <option value="completed">
                                        {t('radiology.status.completed')}
                                    </option>
                                    <option value="validated">
                                        {t('radiology.status.validated')}
                                    </option>
                                </Select>
                            </Field>

                            <KeyValue
                                tile
                                icon={UserCircleIcon}
                                label={t(
                                    'radiology.results.enter.performed_by',
                                )}
                                value={
                                    result?.performed_by?.name ??
                                    t('radiology.results.enter.you')
                                }
                            />
                        </div>
                    </SectionCard>

                    <SectionCard
                        icon={MagnifyingGlassIcon}
                        title={t('radiology.results.enter.findings')}
                    >
                        <Field
                            label={t('radiology.results.enter.findings')}
                            htmlFor="findings"
                            required
                            error={errors.findings}
                            hint={t('radiology.results.enter.findings_hint')}
                        >
                            <Textarea
                                id="findings"
                                rows={7}
                                value={data.findings}
                                onChange={(event) =>
                                    setData('findings', event.target.value)
                                }
                                placeholder={t(
                                    'radiology.results.enter.findings_placeholder',
                                )}
                                invalid={Boolean(errors.findings)}
                            />
                        </Field>
                    </SectionCard>

                    <SectionCard
                        icon={ChatBubbleLeftEllipsisIcon}
                        title={t('radiology.results.enter.conclusion')}
                    >
                        <Field
                            label={t('radiology.results.enter.conclusion')}
                            htmlFor="conclusion"
                            optional
                            error={errors.conclusion}
                            hint={t('radiology.results.enter.conclusion_hint')}
                        >
                            <Textarea
                                id="conclusion"
                                rows={4}
                                value={data.conclusion}
                                onChange={(event) =>
                                    setData('conclusion', event.target.value)
                                }
                                placeholder={t(
                                    'radiology.results.enter.conclusion_placeholder',
                                )}
                                invalid={Boolean(errors.conclusion)}
                            />
                        </Field>
                    </SectionCard>

                    <SectionCard
                        icon={CloudArrowUpIcon}
                        title={t('radiology.results.enter.attach_report')}
                    >
                        <label
                            htmlFor="attachments"
                            onDragOver={(event) => {
                                event.preventDefault();
                            }}
                            onDrop={(event) => {
                                event.preventDefault();

                                if (event.dataTransfer.files?.length) {
                                    setData(
                                        'attachments',
                                        Array.from(event.dataTransfer.files),
                                    );
                                }
                            }}
                            className="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-line-strong bg-surface-muted px-6 py-8 text-center transition-colors duration-200 ease-premium focus-within:border-accent hover:border-accent"
                        >
                            <CloudArrowUpIcon className="mb-2 h-8 w-8 text-ink-subtle" />
                            <p className="text-[13px] font-medium text-ink">
                                {data.attachments.length > 0
                                    ? t(
                                          'radiology.results.enter.files_selected',
                                          { count: data.attachments.length },
                                      )
                                    : t('radiology.results.enter.drop_files')}
                            </p>
                            <p className="mt-1 text-[12px] text-ink-subtle">
                                {t('radiology.results.enter.max_file_size')}
                            </p>
                            <input
                                id="attachments"
                                type="file"
                                multiple
                                accept="application/pdf,image/jpeg,image/png,image/webp"
                                className="sr-only"
                                onChange={(event) =>
                                    setData(
                                        'attachments',
                                        Array.from(event.target.files || []),
                                    )
                                }
                            />
                        </label>

                        {errors.attachments && (
                            <p className="mt-2 text-[12px] text-critical-ink">
                                {errors.attachments}
                            </p>
                        )}

                        {data.attachments.length > 0 && (
                            <ul className="mt-3 space-y-1.5">
                                {data.attachments.map((file, index) => (
                                    <li
                                        key={`${file.name}-${index}`}
                                        className="flex items-center justify-between gap-3 rounded-md bg-surface-muted px-3 py-2"
                                    >
                                        <div className="flex min-w-0 items-center gap-2">
                                            <DocumentTextIcon className="h-4 w-4 shrink-0 text-accent" />
                                            <span className="truncate text-[13px] font-medium text-ink-muted">
                                                {file.name}
                                            </span>
                                            <span className="shrink-0 text-[11px] text-ink-subtle tabular-nums">
                                                {(file.size / 1024).toFixed(1)}{' '}
                                                KB
                                            </span>
                                        </div>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            icon={XMarkIcon}
                                            aria-label={`Remove ${file.name}`}
                                            onClick={() => {
                                                const files = [
                                                    ...data.attachments,
                                                ];
                                                files.splice(index, 1);
                                                setData('attachments', files);
                                            }}
                                        />
                                    </li>
                                ))}
                            </ul>
                        )}
                    </SectionCard>

                    <div className="flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                        <p className="text-[12px] text-ink-subtle">
                            {t('radiology.results.enter.finalizing_notice')}
                        </p>
                        <div className="flex flex-wrap items-center gap-2">
                            <Button
                                variant="secondary"
                                href={route('radiology.requests.index')}
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                icon={CheckIcon}
                                loading={processing}
                            >
                                {isEdit
                                    ? t('radiology.results.enter.update_report')
                                    : t(
                                          'radiology.results.enter.submit_report',
                                      )}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
