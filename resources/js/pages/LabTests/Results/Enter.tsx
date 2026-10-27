import {
    ChatBubbleLeftEllipsisIcon,
    CheckIcon,
    CloudArrowUpIcon,
    CpuChipIcon,
    DocumentCheckIcon,
    DocumentTextIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useMemo } from 'react';
import {
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { cn } from '@/lib/utils';
import { AnalyzerImport } from '@/pages/LabTests/components/AnalyzerImport';
import {
    ParameterResultsSummary,
    ParameterResultsTable,
    parseResultParameters,
} from '@/pages/LabTests/components/ParameterResultsTable';
import {
    CollectionStatusBadge,
    ResultRequestSummary,
} from '@/pages/LabTests/components/ResultRequestSummary';

interface Props {
    labRequest: any;
    labResult?: any; // Optional: provided only during edit
    isEdit: boolean;
}

/** Status → translation key, so the badge label matches the select option. */
const statusLabelKey: Record<string, string> = {
    'In Progress': 'lab_tests.results.enter.in_progress',
    Completed: 'lab_tests.results.enter.completed_verified',
    Cancelled: 'lab_tests.results.enter.voided_cancelled',
};

export default function EnterResults({ labRequest, labResult, isEdit }: Props) {
    const { t } = useTranslation();

    // --- 1. Form Initialization (Resource Based) ---
    const { data, setData, post, put, processing, errors } = useForm({
        lab_request_id: labRequest.id,
        status: labResult?.status || labRequest.status || 'In Progress',
        result_date: labResult?.result_date
            ? new Date(labResult.result_date).toISOString().split('T')[0]
            : new Date().toISOString().split('T')[0],
        results_text: labResult?.results_text || '',
        analysis_comments: labResult?.analysis_comments || '',
        attachments: [] as File[],
    });

    // --- 2. Analyzer integration ---
    // The frame is read by the server (`lab.analyzer.preview`) and copied into
    // this form; the tech still verifies and saves it like a typed result.
    const applyAnalyzerValues = ({
        resultsText,
        status,
    }: {
        resultsText: string;
        status?: string;
    }) => {
        setData('results_text', resultsText);

        if (status !== undefined) {
            setData('status', status);
        }
    };

    // --- 3. Submit Handler ---
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isEdit) {
            put(route('lab-results.update', labResult.id), {
                forceFormData: true,
            });
        } else {
            post(route('lab-results.store'), { forceFormData: true });
        }
    };

    // The dense parameter grid is a structured read of the free-text findings,
    // which stay the submitted payload. Grid edits rewrite their source line.
    const parameters = useMemo(
        () => parseResultParameters(data.results_text),
        [data.results_text],
    );

    const rewriteParameter = (
        index: number,
        field: 'value' | 'unit' | 'referenceRange',
        value: string,
    ) => {
        const target = parameters[index];

        if (!target) {
            return;
        }

        const lines: string[] = String(data.results_text ?? '').split('\n');
        const lineIndex = lines.findIndex((line: string) =>
            line.trim().startsWith(`${target.name}:`),
        );

        if (lineIndex === -1) {
            return;
        }

        const next = { ...target, [field]: value };
        const range = next.referenceRange ? ` (${next.referenceRange})` : '';

        lines[lineIndex] =
            `${next.name}: ${next.value} ${next.unit}${range}`.trim();

        setData('results_text', lines.join('\n'));
    };

    const clearParameter = (index: number) => {
        const target = parameters[index];

        if (!target) {
            return;
        }

        setData(
            'results_text',
            String(data.results_text ?? '')
                .split('\n')
                .filter(
                    (line: string) =>
                        !line.trim().startsWith(`${target.name}:`),
                )
                .join('\n'),
        );
    };

    const title = isEdit
        ? t('lab_tests.results.enter.edit_title')
        : t('lab_tests.results.enter.create_title');

    return (
        <AppLayout
            headerTitle={title}
            headerSubtitle={t('lab_tests.results.enter.subtitle')}
        >
            <Head title={t('lab_tests.results.enter.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    eyebrow={t('lab_tests.results.enter.breadcrumb')}
                    title={title}
                    description={t('lab_tests.results.enter.subtitle')}
                    breadcrumbs={[
                        { label: t('lab_tests.results.enter.breadcrumb') },
                    ]}
                    meta={
                        <CollectionStatusBadge
                            status={data.status}
                            label={t(statusLabelKey[data.status] ?? '')}
                        />
                    }
                />

                <ResultRequestSummary
                    patient={labRequest.patient ?? null}
                    testName={labRequest.test_definition?.test_name ?? '—'}
                    status={labRequest.status}
                    labels={{
                        patient: t('lab_tests.results.enter.subject_patient'),
                        test: t('lab_tests.results.enter.test_definition'),
                        lifecycle: t(
                            'lab_tests.results.enter.current_lifecycle',
                        ),
                    }}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <SectionCard
                        icon={CpuChipIcon}
                        title={t('lab_tests.results.enter.analyst_integration')}
                        description={t(
                            'lab_tests.results.enter.integration_description',
                        )}
                    >
                        <AnalyzerImport onApply={applyAnalyzerValues} />
                    </SectionCard>

                    <SectionCard
                        icon={DocumentCheckIcon}
                        title={t('lab_tests.results.enter.status_assignment')}
                    >
                        <FieldSet columns={2}>
                            <Field
                                label={t(
                                    'lab_tests.results.enter.status_assignment',
                                )}
                                htmlFor="status"
                                required
                                error={errors.status}
                            >
                                <Select
                                    id="status"
                                    name="status"
                                    value={data.status}
                                    onChange={(e) =>
                                        setData('status', e.target.value)
                                    }
                                    invalid={Boolean(errors.status)}
                                >
                                    <option value="In Progress">
                                        {t(
                                            'lab_tests.results.enter.in_progress',
                                        )}
                                    </option>
                                    <option value="Completed">
                                        {t(
                                            'lab_tests.results.enter.completed_verified',
                                        )}
                                    </option>
                                    <option value="Cancelled">
                                        {t(
                                            'lab_tests.results.enter.voided_cancelled',
                                        )}
                                    </option>
                                </Select>
                            </Field>

                            <Field
                                label={t(
                                    'lab_tests.results.enter.verification_date',
                                )}
                                htmlFor="result_date"
                                required
                                error={errors.result_date}
                            >
                                <Input
                                    id="result_date"
                                    name="result_date"
                                    type="date"
                                    value={data.result_date}
                                    onChange={(e) =>
                                        setData('result_date', e.target.value)
                                    }
                                    invalid={Boolean(errors.result_date)}
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <SectionCard
                        icon={DocumentCheckIcon}
                        title={t('lab_tests.results.enter.findings')}
                    >
                        <Field
                            label={t('lab_tests.results.enter.findings')}
                            htmlFor="results_text"
                            required
                            error={errors.results_text}
                            hint={t(
                                'lab_tests.results.enter.results_placeholder',
                            )}
                        >
                            <Textarea
                                id="results_text"
                                name="results_text"
                                rows={6}
                                value={data.results_text}
                                onChange={(e) =>
                                    setData('results_text', e.target.value)
                                }
                                placeholder={t(
                                    'lab_tests.results.enter.results_placeholder',
                                )}
                                className="font-mono text-[12.5px]"
                                invalid={Boolean(errors.results_text)}
                            />
                        </Field>

                        {parameters.length > 0 && (
                            <div className="mt-4">
                                <ParameterResultsTable
                                    parameters={parameters}
                                    onChange={rewriteParameter}
                                    onClearRow={clearParameter}
                                />
                                <ParameterResultsSummary
                                    parameters={parameters}
                                />
                            </div>
                        )}
                    </SectionCard>

                    <SectionCard
                        icon={ChatBubbleLeftEllipsisIcon}
                        title={t(
                            'lab_tests.results.enter.analytical_interpretation',
                        )}
                    >
                        <Field
                            label={t(
                                'lab_tests.results.enter.analytical_interpretation',
                            )}
                            htmlFor="analysis_comments"
                            optional
                            error={errors.analysis_comments}
                        >
                            <Textarea
                                id="analysis_comments"
                                name="analysis_comments"
                                rows={4}
                                value={data.analysis_comments}
                                onChange={(e) =>
                                    setData('analysis_comments', e.target.value)
                                }
                                placeholder={t(
                                    'lab_tests.results.enter.interpretation_placeholder',
                                )}
                                invalid={Boolean(errors.analysis_comments)}
                            />
                        </Field>
                    </SectionCard>

                    <SectionCard
                        icon={CloudArrowUpIcon}
                        title={t('lab_tests.results.enter.attach_reports')}
                    >
                        <label
                            htmlFor="attachments"
                            onDragOver={(e) => {
                                e.preventDefault();
                            }}
                            onDrop={(e) => {
                                e.preventDefault();

                                if (e.dataTransfer.files?.length) {
                                    setData(
                                        'attachments',
                                        Array.from(e.dataTransfer.files),
                                    );
                                }
                            }}
                            className={cn(
                                'flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed bg-surface-muted px-6 py-8 text-center transition-colors duration-200 ease-premium focus-within:border-accent hover:border-accent',
                                errors.attachments
                                    ? 'border-critical/70'
                                    : 'border-line-strong',
                            )}
                        >
                            <CloudArrowUpIcon className="mb-2 h-8 w-8 text-ink-subtle" />
                            <p className="text-[13px] font-medium text-ink">
                                {data.attachments.length > 0
                                    ? t(
                                          'lab_tests.results.enter.files_selected',
                                          {
                                              count: data.attachments.length,
                                          },
                                      )
                                    : t('lab_tests.results.enter.drop_files')}
                            </p>
                            <p className="mt-1 text-[12px] text-ink-subtle">
                                {t('lab_tests.results.enter.max_file_size')}
                            </p>
                            <input
                                id="attachments"
                                type="file"
                                multiple
                                aria-invalid={
                                    Boolean(errors.attachments) || undefined
                                }
                                className="sr-only"
                                onChange={(e) =>
                                    setData(
                                        'attachments',
                                        Array.from(e.target.files || []),
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
                                {Array.from(data.attachments).map((file, i) => (
                                    <li
                                        key={i}
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
                                                const files = Array.from(
                                                    data.attachments,
                                                );
                                                files.splice(i, 1);
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
                            {t('lab_tests.results.enter.finalizing_notice')}
                        </p>
                        <div className="flex flex-wrap items-center gap-2">
                            <Button
                                variant="secondary"
                                href="/lab/test-requests"
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                icon={CheckIcon}
                                loading={processing}
                            >
                                {isEdit
                                    ? t('lab_tests.results.enter.update_record')
                                    : t(
                                          'lab_tests.results.enter.submit_result',
                                      )}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
