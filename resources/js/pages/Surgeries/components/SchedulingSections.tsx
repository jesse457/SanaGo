import {
    ArchiveBoxArrowDownIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    ClockIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import {
    Badge,
    Field,
    Input,
    SectionCard,
    Select,
    Textarea,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import type { SetSurgeryData, StatusOption, Translate } from './types';

interface SchedulingSectionProps {
    scheduledAt: string;
    estimatedDurationMinutes: string;
    urgency: string;
    surgeryFee: string;
    errors: Partial<Record<string, string>>;
    setData: SetSurgeryData;
    t: Translate;
}

/**
 * SchedulingSection — when the theatre is booked, how long it should take, how
 * urgent it is and what it costs.
 */
export function SchedulingSection({
    scheduledAt,
    estimatedDurationMinutes,
    urgency,
    surgeryFee,
    errors,
    setData,
    t,
}: SchedulingSectionProps) {
    return (
        <SectionCard
            icon={ArchiveBoxArrowDownIcon}
            title={t('surgeries.create.section_scheduling')}
        >
            <div className="grid grid-cols-1 gap-x-4 gap-y-5 md:grid-cols-2">
                <Field
                    label={t('surgeries.create.scheduled_date_time')}
                    htmlFor="scheduled_at"
                    required
                    error={errors.scheduled_at}
                >
                    <Input
                        id="scheduled_at"
                        type="datetime-local"
                        value={scheduledAt}
                        icon={CalendarDaysIcon}
                        invalid={Boolean(errors.scheduled_at)}
                        required
                        onChange={(event) =>
                            setData('scheduled_at', event.target.value)
                        }
                    />
                </Field>

                <Field
                    label={t('surgeries.create.est_duration')}
                    htmlFor="estimated_duration_minutes"
                    error={errors.estimated_duration_minutes}
                >
                    <Input
                        id="estimated_duration_minutes"
                        type="number"
                        value={estimatedDurationMinutes}
                        icon={ClockIcon}
                        invalid={Boolean(errors.estimated_duration_minutes)}
                        className="tabular-nums"
                        placeholder={t('surgeries.create.duration_placeholder')}
                        onChange={(event) =>
                            setData(
                                'estimated_duration_minutes',
                                event.target.value,
                            )
                        }
                    />
                </Field>

                <Field
                    label={t('surgeries.create.urgency_level')}
                    htmlFor="urgency"
                    error={errors.urgency}
                >
                    <Select
                        id="urgency"
                        value={urgency}
                        invalid={Boolean(errors.urgency)}
                        onChange={(event) =>
                            setData('urgency', event.target.value)
                        }
                    >
                        <option value="normal">
                            {t('surgeries.create.normal')}
                        </option>
                        <option value="urgent">
                            {t('surgeries.create.urgent')}
                        </option>
                        <option value="emergency">
                            {t('surgeries.create.emergency')}
                        </option>
                    </Select>
                </Field>

                <Field
                    label={t('surgeries.create.surgery_fee')}
                    htmlFor="surgery_fee"
                    required
                    error={errors.surgery_fee}
                >
                    <div className="relative">
                        <span className="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-[11px] font-semibold tracking-wide text-ink-subtle">
                            FCFA
                        </span>
                        <Input
                            id="surgery_fee"
                            type="number"
                            step="0.01"
                            value={surgeryFee}
                            invalid={Boolean(errors.surgery_fee)}
                            required
                            className="pl-14 tabular-nums"
                            placeholder="0.00"
                            onChange={(event) =>
                                setData('surgery_fee', event.target.value)
                            }
                        />
                    </div>
                </Field>
            </div>
        </SectionCard>
    );
}

export const STATUS_TONES: Record<string, BadgeTone> = {
    scheduled: 'accent',
    in_progress: 'warning',
    completed: 'positive',
    cancelled: 'critical',
};

interface StatusSectionProps {
    status: string;
    completedAt: string;
    scheduledAt: string;
    complications: string;
    statusOptions: StatusOption[];
    errors: Partial<Record<string, string>>;
    setData: SetSurgeryData;
    t: Translate;
}

/**
 * StatusSection — the surgical record's state, its completion timestamp and any
 * complications noted.
 */
export function StatusSection({
    status,
    completedAt,
    scheduledAt,
    complications,
    statusOptions,
    errors,
    setData,
    t,
}: StatusSectionProps) {
    const currentLabel = statusOptions.find(
        (option) => option.value === status,
    )?.label;

    return (
        <SectionCard
            icon={ClipboardDocumentCheckIcon}
            title={t('surgeries.create.section_status')}
        >
            <div className="grid grid-cols-1 gap-x-4 gap-y-5 md:grid-cols-2">
                <Field
                    label={t('surgeries.create.status')}
                    htmlFor="status"
                    required
                    error={errors.status}
                    labelAction={
                        status && currentLabel ? (
                            <Badge tone={STATUS_TONES[status] ?? 'neutral'} dot>
                                {currentLabel}
                            </Badge>
                        ) : null
                    }
                >
                    <Select
                        id="status"
                        value={status}
                        invalid={Boolean(errors.status)}
                        onChange={(event) =>
                            setData('status', event.target.value)
                        }
                    >
                        {statusOptions.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </Select>
                </Field>

                {status === 'completed' && (
                    <Field
                        label={t('surgeries.create.completed_date')}
                        htmlFor="completed_at"
                        error={errors.completed_at}
                    >
                        <Input
                            id="completed_at"
                            type="datetime-local"
                            value={completedAt || scheduledAt}
                            icon={CalendarDaysIcon}
                            invalid={Boolean(errors.completed_at)}
                            onChange={(event) =>
                                setData('completed_at', event.target.value)
                            }
                        />
                    </Field>
                )}

                <Field
                    label={t('surgeries.create.complications')}
                    htmlFor="complications"
                    error={errors.complications}
                    className="md:col-span-2"
                >
                    <Textarea
                        id="complications"
                        rows={2}
                        value={complications}
                        invalid={Boolean(errors.complications)}
                        placeholder={t(
                            'surgeries.create.complications_placeholder',
                        )}
                        onChange={(event) =>
                            setData('complications', event.target.value)
                        }
                    />
                </Field>
            </div>
        </SectionCard>
    );
}

interface NotesSectionProps {
    notes: string;
    error?: string;
    setData: SetSurgeryData;
    t: Translate;
}

/**
 * NotesSection — the optional free-text field that closes the record.
 */
export function NotesSection({ notes, error, setData, t }: NotesSectionProps) {
    return (
        <SectionCard
            icon={DocumentTextIcon}
            title={t('surgeries.create.additional_notes')}
            description={t('surgeries.create.optional')}
        >
            <Field
                label={t('surgeries.create.additional_notes')}
                htmlFor="notes"
                optional
                error={error}
            >
                <Textarea
                    id="notes"
                    rows={3}
                    value={notes}
                    invalid={Boolean(error)}
                    placeholder={t('surgeries.create.notes_placeholder')}
                    onChange={(event) => setData('notes', event.target.value)}
                />
            </Field>
        </SectionCard>
    );
}
