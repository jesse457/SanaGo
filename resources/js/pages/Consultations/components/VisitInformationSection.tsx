import {
    CalendarDaysIcon,
    IdentificationIcon,
} from '@heroicons/react/24/outline';
import { Field, FieldSet, Input, Select, SectionCard } from '@/Components/ui';
import type {
    AlcoholUse,
    SetConsultationData,
    SmokingStatus,
    Translate,
    VisitType,
} from './types';

interface VisitInformationSectionProps {
    visitType: VisitType | '';
    consultationDate: string;
    onVisitTypeChange: (value: VisitType | '') => void;
    onConsultationDateChange: (value: string) => void;
    error?: string;
    t: Translate;
}

/**
 * VisitInformationSection — visit type and consultation timestamp.
 */
export function VisitInformationSection({
    visitType,
    consultationDate,
    onVisitTypeChange,
    onConsultationDateChange,
    error,
    t,
}: VisitInformationSectionProps) {
    return (
        <SectionCard
            icon={CalendarDaysIcon}
            title={t('Visit Information')}
            description={t('visitInformationDescription')}
        >
            <FieldSet columns={2}>
                <Field
                    label={t('Visit Type')}
                    htmlFor="visit_type"
                    required
                    error={error}
                >
                    <Select
                        id="visit_type"
                        value={visitType}
                        invalid={Boolean(error)}
                        onChange={(event) =>
                            onVisitTypeChange(event.target.value as VisitType)
                        }
                    >
                        <option value="">{t('selectVisitType')}</option>
                        <option value="new">{t('New Patient')}</option>
                        <option value="follow_up">{t('Follow-up')}</option>
                        <option value="consultation">
                            {t('Consultation')}
                        </option>
                        <option value="emergency">{t('Emergency')}</option>
                        <option value="pre_op">{t('Pre-op')}</option>
                        <option value="post_op">{t('Post-op')}</option>
                    </Select>
                </Field>

                <Field
                    label={t('Consultation Date')}
                    htmlFor="consultation_date"
                >
                    <Input
                        id="consultation_date"
                        type="datetime-local"
                        value={consultationDate}
                        onChange={(event) =>
                            onConsultationDateChange(event.target.value)
                        }
                    />
                </Field>
            </FieldSet>
        </SectionCard>
    );
}

interface SocialHistorySectionProps {
    smokingStatus: SmokingStatus | '';
    alcoholUse: AlcoholUse | '';
    occupation: string;
    livingSituation: string;
    setData: SetConsultationData;
    t: Translate;
}

/**
 * SocialHistorySection — the four social determinants recorded at intake.
 */
export function SocialHistorySection({
    smokingStatus,
    alcoholUse,
    occupation,
    livingSituation,
    setData,
    t,
}: SocialHistorySectionProps) {
    return (
        <SectionCard
            icon={IdentificationIcon}
            title={t('Social History')}
            description={t('socialHistoryDescription')}
        >
            <FieldSet columns={4}>
                <Field label={t('Smoking')} htmlFor="smoking_status">
                    <Select
                        id="smoking_status"
                        value={smokingStatus}
                        onChange={(event) =>
                            setData(
                                'smoking_status',
                                event.target.value as SmokingStatus,
                            )
                        }
                    >
                        <option value="">{t('select')}</option>
                        <option value="never">{t('Never')}</option>
                        <option value="former">{t('Former')}</option>
                        <option value="current">{t('Current')}</option>
                    </Select>
                </Field>

                <Field label={t('Alcohol')} htmlFor="alcohol_use">
                    <Select
                        id="alcohol_use"
                        value={alcoholUse}
                        onChange={(event) =>
                            setData(
                                'alcohol_use',
                                event.target.value as AlcoholUse,
                            )
                        }
                    >
                        <option value="">{t('select')}</option>
                        <option value="none">{t('None')}</option>
                        <option value="occasional">{t('Occasional')}</option>
                        <option value="moderate">{t('Moderate')}</option>
                        <option value="heavy">{t('Heavy')}</option>
                    </Select>
                </Field>

                <Field label={t('Occupation')} htmlFor="occupation">
                    <Input
                        id="occupation"
                        value={occupation}
                        onChange={(event) =>
                            setData('occupation', event.target.value)
                        }
                        placeholder="e.g. Teacher"
                    />
                </Field>

                <Field label={t('Living Situation')} htmlFor="living_situation">
                    <Input
                        id="living_situation"
                        value={livingSituation}
                        onChange={(event) =>
                            setData('living_situation', event.target.value)
                        }
                        placeholder="e.g. Lives alone"
                    />
                </Field>
            </FieldSet>
        </SectionCard>
    );
}
