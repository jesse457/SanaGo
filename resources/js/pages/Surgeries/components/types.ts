/**
 * Shared vocabulary for the Surgeries module. Both the form page and the
 * section components import their shapes from here so the sections stay
 * drop-in composable.
 */

export interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    date_of_birth: string | null;
    gender: string | null;
}

export interface Doctor {
    id: number;
    name: string;
}

export interface Bed {
    id: number;
    code: string;
    ward_name: string;
    type_name: string;
}

export interface Surgery {
    id: number;
    patient_id: number;
    surgeon_id: number | null;
    surgeon_name: string | null;
    anesthetist_id: number | null;
    anesthetist_name: string | null;
    bed_id: number | null;
    surgery_name: string;
    description: string | null;
    scheduled_at: string;
    completed_at: string | null;
    estimated_duration_minutes: number | null;
    urgency: string;
    status: string;
    surgery_fee: number | string;
    notes: string | null;
    complications: string | null;
    patient: Patient;
    surgeon?: { id: number; name: string } | null;
    anesthetist?: { id: number; name: string } | null;
}

export interface SurgeryForm {
    patient_id: string;
    surgeon_id: string;
    surgeon_name: string;
    anesthetist_id: string;
    anesthetist_name: string;
    bed_id: string;
    surgery_name: string;
    description: string;
    scheduled_at: string;
    completed_at: string;
    estimated_duration_minutes: string;
    urgency: string;
    status: string;
    surgery_fee: string;
    notes: string;
    complications: string;
}

/** The Inertia form instance threaded through every surgery section. */
export type SurgeryFormApi = {
    data: SurgeryForm;
    setData: {
        <K extends keyof SurgeryForm>(key: K, value: SurgeryForm[K]): void;
        (data: Partial<SurgeryForm>): void;
    };
    errors: Partial<Record<keyof SurgeryForm, string>>;
};

export type SetSurgeryData = SurgeryFormApi['setData'];

export type FormErrors = Partial<Record<keyof SurgeryForm, string>>;

export type Translate = (
    key: string,
    replacements?: Record<string, string | number>,
) => string;

export interface StatusOption {
    value: string;
    label: string;
}
