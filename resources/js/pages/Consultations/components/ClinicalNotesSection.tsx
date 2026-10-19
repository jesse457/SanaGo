import {
    BeakerIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    ClipboardDocumentListIcon,
    DocumentTextIcon,
    HeartIcon,
} from '@heroicons/react/24/outline';
import {
    Badge,
    Checkbox,
    Field,
    FieldSet,
    Input,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { REVIEW_OF_SYSTEMS } from './helpers';
import type { ReviewOfSystem, SetConsultationData, Translate } from './types';

interface ComplaintAndDiagnosisProps {
    chiefComplaint: string;
    diagnosis: string;
    chiefComplaintError?: string;
    setData: SetConsultationData;
    t: Translate;
}

/**
 * ComplaintAndDiagnosisSection — the two half-width narrative panels that open
 * the clinical picture.
 */
export function ComplaintAndDiagnosisSection({
    chiefComplaint,
    diagnosis,
    chiefComplaintError,
    setData,
    t,
}: ComplaintAndDiagnosisProps) {
    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <SectionCard
                icon={ClipboardDocumentListIcon}
                title={t('chiefComplaint')}
                description={t('primaryReasonForVisit')}
            >
                <Field
                    label={t('chiefComplaint')}
                    htmlFor="chief_complaint"
                    required
                    error={chiefComplaintError}
                >
                    <Textarea
                        id="chief_complaint"
                        rows={4}
                        value={chiefComplaint}
                        invalid={Boolean(chiefComplaintError)}
                        onChange={(event) =>
                            setData('chief_complaint', event.target.value)
                        }
                        placeholder={t('primaryReasonForVisit')}
                    />
                </Field>
            </SectionCard>

            <SectionCard
                icon={ClipboardDocumentCheckIcon}
                title={t('diagnosis')}
                description={t('clinicalDiagnosis')}
            >
                <Field label={t('diagnosis')} htmlFor="diagnosis">
                    <Textarea
                        id="diagnosis"
                        rows={4}
                        value={diagnosis}
                        onChange={(event) =>
                            setData('diagnosis', event.target.value)
                        }
                        placeholder={t('clinicalDiagnosis')}
                    />
                </Field>
            </SectionCard>
        </div>
    );
}

interface HistorySectionProps {
    historyOfPresentIllness: string;
    setData: SetConsultationData;
    t: Translate;
}

/**
 * HistorySection — extended history of the present illness.
 */
export function HistorySection({
    historyOfPresentIllness,
    setData,
    t,
}: HistorySectionProps) {
    return (
        <SectionCard
            icon={DocumentTextIcon}
            title={t('historyOfPresentIllness')}
            description={t('detailedPresentation')}
        >
            <Textarea
                aria-label={t('historyOfPresentIllness')}
                rows={4}
                value={historyOfPresentIllness}
                onChange={(event) =>
                    setData('history_of_present_illness', event.target.value)
                }
                placeholder={t('detailedPresentation')}
            />
        </SectionCard>
    );
}

interface ReviewOfSystemsSectionProps {
    selected: ReviewOfSystem[];
    onToggle: (system: ReviewOfSystem) => void;
    t: Translate;
}

/**
 * ReviewOfSystemsSection — the 14-system checklist, replacing the hand-rolled
 * `<details>` block.
 */
export function ReviewOfSystemsSection({
    selected,
    onToggle,
    t,
}: ReviewOfSystemsSectionProps) {
    return (
        <SectionCard
            icon={HeartIcon}
            title={t('Review of Systems')}
            actions={
                <Badge tone="accent">
                    {selected.length} {t('selected')}
                </Badge>
            }
        >
            <div className="grid grid-cols-2 gap-x-3 gap-y-2 sm:grid-cols-3 lg:grid-cols-4">
                {REVIEW_OF_SYSTEMS.map((system) => (
                    <label
                        key={system}
                        className="flex cursor-pointer items-center gap-2 rounded-md px-1.5 py-1.5 text-[13px] text-ink-muted transition-colors duration-150 ease-premium hover:bg-surface-hover"
                    >
                        <Checkbox
                            checked={selected.includes(system)}
                            onChange={() => onToggle(system)}
                        />
                        <span className="truncate">{system}</span>
                    </label>
                ))}
            </div>
        </SectionCard>
    );
}

interface ExamAndPlanSectionProps {
    physicalExam: string;
    treatmentPlan: string;
    setData: SetConsultationData;
    t: Translate;
}

/**
 * ExamAndPlanSection — physical examination findings and the treatment plan.
 */
export function ExamAndPlanSection({
    physicalExam,
    treatmentPlan,
    setData,
    t,
}: ExamAndPlanSectionProps) {
    const examHint = t(
        'Document findings by system: general, HEENT, cardiovascular, respiratory, abdominal, neurological, musculoskeletal, skin',
    );

    return (
        <>
            <SectionCard
                icon={ClipboardDocumentCheckIcon}
                title={t('physicalExamination')}
                description={examHint}
            >
                <Textarea
                    aria-label={t('physicalExamination')}
                    rows={5}
                    value={physicalExam}
                    onChange={(event) =>
                        setData('physical_exam', event.target.value)
                    }
                    placeholder={t(
                        'e.g. General: Alert, no acute distress. CV: Regular rate and rhythm, no murmurs. Respiratory: Clear to auscultation bilaterally. Abdomen: Soft, non-tender.',
                    )}
                />
            </SectionCard>

            <SectionCard
                icon={BeakerIcon}
                title={t('treatmentPlan')}
                description={t('treatmentPlanPlaceholder')}
            >
                <Textarea
                    aria-label={t('treatmentPlan')}
                    rows={3}
                    value={treatmentPlan}
                    onChange={(event) =>
                        setData('treatment_plan', event.target.value)
                    }
                    placeholder={t('treatmentPlanPlaceholder')}
                />
            </SectionCard>
        </>
    );
}

interface FollowUpSectionProps {
    followUpDate: string;
    followUpInstructions: string;
    notes: string;
    setData: SetConsultationData;
    t: Translate;
}

/**
 * FollowUpSection — follow-up scheduling plus free-form provider notes.
 */
export function FollowUpSection({
    followUpDate,
    followUpInstructions,
    notes,
    setData,
    t,
}: FollowUpSectionProps) {
    return (
        <SectionCard
            icon={CalendarDaysIcon}
            title={t('Follow-up')}
            description={t('followUpDescription')}
        >
            <FieldSet columns={2}>
                <Field label={t('Follow-up Date')} htmlFor="follow_up_date">
                    <Input
                        id="follow_up_date"
                        type="date"
                        value={followUpDate}
                        onChange={(event) =>
                            setData('follow_up_date', event.target.value)
                        }
                    />
                </Field>
                <Field
                    label={t('Follow-up Instructions')}
                    htmlFor="follow_up_instructions"
                >
                    <Textarea
                        id="follow_up_instructions"
                        rows={2}
                        value={followUpInstructions}
                        onChange={(event) =>
                            setData(
                                'follow_up_instructions',
                                event.target.value,
                            )
                        }
                        placeholder={t(
                            'e.g. Return in 2 weeks for review. Come sooner if symptoms worsen.',
                        )}
                    />
                </Field>
                <Field
                    label={t('notes')}
                    htmlFor="notes"
                    className="sm:col-span-2"
                >
                    <Textarea
                        id="notes"
                        rows={2}
                        value={notes}
                        onChange={(event) =>
                            setData('notes', event.target.value)
                        }
                        placeholder={t('additionalNotes')}
                    />
                </Field>
            </FieldSet>
        </SectionCard>
    );
}
