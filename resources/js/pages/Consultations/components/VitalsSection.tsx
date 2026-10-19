import { HeartIcon } from '@heroicons/react/24/outline';
import type { ReactNode } from 'react';
import { Badge, Field, FieldSet, Input, SectionCard } from '@/Components/ui';
import type { SetConsultationData, Translate } from './types';

interface VitalFieldProps {
    id: string;
    label: string;
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    step?: string;
    min?: string;
    max?: string;
    hint?: ReactNode;
}

/**
 * VitalField — one numeric vitals entry with tabular figures. The repeated
 * tile that used to be copy-pasted nine times.
 */
export function VitalField({
    id,
    label,
    value,
    onChange,
    placeholder,
    step,
    min,
    max,
    hint,
}: VitalFieldProps) {
    return (
        <Field label={label} htmlFor={id} hint={hint}>
            <Input
                id={id}
                type="number"
                inputMode="decimal"
                step={step}
                min={min}
                max={max}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                className="tabular-nums"
            />
        </Field>
    );
}

interface BloodPressureFieldProps {
    systolic: string;
    diastolic: string;
    onSystolicChange: (value: string) => void;
    onDiastolicChange: (value: string) => void;
    label: string;
}

/**
 * BloodPressureField — the single "120 / 80" paired control.
 */
export function BloodPressureField({
    systolic,
    diastolic,
    onSystolicChange,
    onDiastolicChange,
    label,
}: BloodPressureFieldProps) {
    return (
        <Field label={label}>
            <div className="flex items-center gap-1.5">
                <Input
                    aria-label={`${label} systolic`}
                    type="number"
                    inputMode="numeric"
                    value={systolic}
                    onChange={(event) => onSystolicChange(event.target.value)}
                    placeholder="120"
                    className="tabular-nums"
                />
                <span
                    aria-hidden="true"
                    className="text-[13px] text-ink-subtle"
                >
                    /
                </span>
                <Input
                    aria-label={`${label} diastolic`}
                    type="number"
                    inputMode="numeric"
                    value={diastolic}
                    onChange={(event) => onDiastolicChange(event.target.value)}
                    placeholder="80"
                    className="tabular-nums"
                />
            </div>
        </Field>
    );
}

interface BMITileProps {
    bmi: number;
    label: string;
    categoryLabel: string;
    tone: 'positive' | 'warning' | 'critical' | 'neutral';
}

/**
 * BMITile — derived read-out that sits in the vitals grid.
 */
export function BMITile({ bmi, label, categoryLabel, tone }: BMITileProps) {
    return (
        <Field label={label}>
            <div className="flex h-10 items-center gap-2 rounded-md border border-line bg-surface-muted px-3">
                <span className="text-[13px] font-semibold text-ink tabular-nums">
                    {bmi}
                </span>
                <span className="text-[11px] text-ink-subtle">kg/m²</span>
                <Badge tone={tone} className="ml-auto">
                    {categoryLabel}
                </Badge>
            </div>
        </Field>
    );
}

interface VitalsSectionProps {
    data: {
        temperature: string;
        systolic_bp: string;
        diastolic_bp: string;
        heart_rate: string;
        oxygen_saturation: string;
        respiratory_rate: string;
        pain_score: string;
        weight_kg: string;
        height_cm: string;
    };
    setData: SetConsultationData;
    bmi: number | null;
    bmiCategory: {
        label: string;
        tone: 'positive' | 'warning' | 'critical' | 'neutral';
    } | null;
    t: Translate;
}

/**
 * VitalsSection — the vitals block of the clinical tab.
 */
export function VitalsSection({
    data,
    setData,
    bmi,
    bmiCategory,
    t,
}: VitalsSectionProps) {
    return (
        <SectionCard
            icon={HeartIcon}
            title={t('Vital Signs')}
            description={t('vitalSignsDescription')}
        >
            <FieldSet columns={4}>
                <VitalField
                    id="temperature"
                    label={t('Temp (°C)')}
                    value={data.temperature}
                    onChange={(value) => setData('temperature', value)}
                    placeholder="37.0"
                    step="0.1"
                />

                <BloodPressureField
                    label={t('BP Sys/Dia')}
                    systolic={data.systolic_bp}
                    diastolic={data.diastolic_bp}
                    onSystolicChange={(value) => setData('systolic_bp', value)}
                    onDiastolicChange={(value) =>
                        setData('diastolic_bp', value)
                    }
                />

                <VitalField
                    id="heart_rate"
                    label={t('HR (bpm)')}
                    value={data.heart_rate}
                    onChange={(value) => setData('heart_rate', value)}
                    placeholder="72"
                />

                <VitalField
                    id="oxygen_saturation"
                    label={t('SpO₂ (%)')}
                    value={data.oxygen_saturation}
                    onChange={(value) => setData('oxygen_saturation', value)}
                    placeholder="98"
                />

                <VitalField
                    id="pain_score"
                    label={t('Pain (0-10)')}
                    value={data.pain_score}
                    onChange={(value) => setData('pain_score', value)}
                    placeholder="0"
                    min="0"
                    max="10"
                />

                <VitalField
                    id="respiratory_rate"
                    label={t('RR (/min)')}
                    value={data.respiratory_rate}
                    onChange={(value) => setData('respiratory_rate', value)}
                    placeholder="16"
                />

                <VitalField
                    id="weight_kg"
                    label={t('Weight (kg)')}
                    value={data.weight_kg}
                    onChange={(value) => setData('weight_kg', value)}
                    placeholder="70"
                    step="0.1"
                />

                <VitalField
                    id="height_cm"
                    label={t('Height (cm)')}
                    value={data.height_cm}
                    onChange={(value) => setData('height_cm', value)}
                    placeholder="170"
                    step="0.1"
                />

                {bmi !== null && bmiCategory && (
                    <BMITile
                        bmi={bmi}
                        label={t('BMI')}
                        categoryLabel={bmiCategory.label}
                        tone={bmiCategory.tone}
                    />
                )}
            </FieldSet>
        </SectionCard>
    );
}
