import type { InertiaFormProps } from '@inertiajs/react';

/**
 * Shared vocabulary for the Consultations module. Every component under
 * `pages/Consultations/components` imports its form shape from here so the
 * sections stay drop-in composable.
 */

export interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    date_of_birth: string | null;
    gender: string | null;
    phone: string | null;
    allergies: string | null;
    age: number | null;
    photo_url: string | null;
}

export interface Medication {
    id: number;
    name: string;
    unit_cost?: number;
    category?: string;
    allergies_warning?: string[];
}

export interface LabTestDefinition {
    id: number;
    test_name: string;
    code: string;
    category?: string;
}

export interface PrescriptionFormItem {
    medication_id: number | null;
    drug_name: string;
    dosage: string;
    frequency: string;
    duration: string;
    quantity: number;
    instructions: string;
}

export interface LabRequestFormItem {
    test_definition_id: number;
    test_name: string;
    code: string;
    urgency: 'routine' | 'urgent' | 'stat';
    reason_for_test: string;
}

export type ReviewOfSystem =
    | 'Constitutional'
    | 'Eyes'
    | 'ENT'
    | 'Cardiovascular'
    | 'Respiratory'
    | 'Gastrointestinal'
    | 'Genitourinary'
    | 'Musculoskeletal'
    | 'Skin'
    | 'Neurological'
    | 'Psychiatric'
    | 'Endocrine'
    | 'Hematologic/Lymphatic'
    | 'Allergic/Immunologic';

export type VisitType =
    | 'new'
    | 'follow_up'
    | 'consultation'
    | 'emergency'
    | 'pre_op'
    | 'post_op'
    | 'routine_checkup'
    | 'specialist_referral';

export type SmokingStatus =
    | 'never'
    | 'former'
    | 'current'
    | 'passive'
    | 'unknown';

export type AlcoholUse =
    | 'none'
    | 'occasional'
    | 'moderate'
    | 'heavy'
    | 'former'
    | 'unknown';

export type AllergyType =
    | 'drug'
    | 'food'
    | 'environmental'
    | 'latex'
    | 'insect'
    | 'other';

export type AllergySeverity =
    | 'mild'
    | 'moderate'
    | 'severe'
    | 'life_threatening';

export type AllergyReaction =
    | 'rash'
    | 'hives'
    | 'swelling'
    | 'anaphylaxis'
    | 'respiratory_distress'
    | 'gi_symptoms'
    | 'other';

export interface Allergy {
    id?: number;
    allergen: string;
    type: AllergyType;
    severity: AllergySeverity;
    reaction: AllergyReaction[];
    notes?: string;
    diagnosed_date?: string;
    status: 'active' | 'inactive' | 'resolved';
    recorded_by?: string;
}

export interface ConsultationForm {
    patient_id: number | string;
    visit_type: VisitType | '';
    consultation_date: string;
    chief_complaint: string;
    history_of_present_illness: string;
    review_of_systems: ReviewOfSystem[];
    smoking_status: SmokingStatus | '';
    alcohol_use: AlcoholUse | '';
    occupation?: string;
    living_situation?: string;
    temperature: string;
    systolic_bp: string;
    diastolic_bp: string;
    heart_rate: string;
    oxygen_saturation: string;
    respiratory_rate: string;
    pain_score: string;
    weight_kg: string;
    height_cm: string;
    physical_exam: string;
    diagnosis: string;
    differential_diagnoses?: string;
    severity_assessment?: 'mild' | 'moderate' | 'severe' | 'critical' | '';
    treatment_plan: string;
    allergies: Allergy[];
    new_allergies?: Allergy[];
    allergy_notes?: string;
    follow_up_date: string;
    follow_up_instructions: string;
    notes: string;
    patient_education?: string;
    return_precautions?: string;
    prescription_items: PrescriptionFormItem[];
    lab_requests: LabRequestFormItem[];
    attachments: File[];
    consultation_duration?: number;
    supervising_provider?: string;
}

export interface ConsultationPageProps {
    patients: Patient[];
    medications: Medication[];
    labTests: LabTestDefinition[];
    patient_allergies?: Allergy[];
}

/** The Inertia form instance threaded through every clinical section. */
export type ConsultationFormApi = InertiaFormProps<ConsultationForm>;

export type SetConsultationData = ConsultationFormApi['setData'];

export type FormErrors = Partial<Record<keyof ConsultationForm, string>>;

export type Translate = (
    key: string,
    replacements?: Record<string, string | number>,
) => string;
