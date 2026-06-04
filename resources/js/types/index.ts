export type * from './auth';
export type * from './dashboard';
export type * from './inventory';

// Enum value constants and types — import from @/types/enums
export {
    AppointmentStatus,
    AdmissionStatus,
    BedStatus,
    BatchStatus,
    DepartmentType,
    EmergencyStatus,
    ExpenditureCategory,
    FeedbackStatus,
    Gender,
    InvoiceStatus,
    LabRequestStatus,
    LabResultStatus,
    PrescriptionStatus,
    ShiftType,
    SurgeryStatus,
    UrgencyLevel,
} from './enums';

export type UrgencyLevel = import('./enums').UrgencyLevelEnum;
export type Gender = import('./enums').GenderEnum;
export interface Notification {
    id: string;
    message: string;
    patient_name: string;
    doctor_name: string;
    urgency: UrgencyLevel;
    type: string;
    created_at: string;
    read: boolean;
}

export interface LabRequestEvent {
    consultation_id: number;
    patient_name: string;
    doctor_name: string;
    urgency: UrgencyLevel;
}

// Inertia Page Props Extension
export interface User {
    id: number;
    name: string;
    email: string;
    profile_picture?: string;
}

export interface Tenant {
    name: string;
}

export interface PageProps {
    auth: { user: User };
    tenant: Tenant;
    [key: string]: any;
}
/**
 * User Interface (Doctors/Nurses)
 */
export interface User {
    id: number;
    name: string;
    email: string;
    role?: string; // e.g., 'admin', 'doctor', 'reception', 'lab_tech', 'pharma'
}

/**
 * Patient Interface
 */
export interface Patient {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    email?: string;
    phone?: string;
    date_of_birth?: string; // ISO Date string
    gender?: Gender;
    blood_type?: string;
    allergies?: string;
    address?: string;
    emergency_contact_name?: string;
    emergency_contact_phone?: string;
    last_visit_date?: string;
    age?: number; // Computed accessor from Laravel
    created_at: string;
    updated_at: string;

    // Relationships
    medical_records?: MedicalRecord[];
    vitals?: Vital[];
}

/**
 * Medical Record / Consultation Interface
 */
export interface MedicalRecord {
    id: number;
    patient_id: number;
    doctor_id: number;
    record_date: string;
    chief_complaint: string;
    history_of_present_illness?: string;
    physical_exam?: string;
    diagnosis?: string;
    treatment_plan?: string;
    notes?: string;
    attachments?: string[]; // Array of file paths/names
    created_at: string;

    // Relationships
    patient?: Patient;
    doctor?: User;
}

/**
 * Vitals Interface
 */
export interface Vital {
    id: number;
    patient_id: number;
    recorded_by: number;
    recorded_at: string;
    temperature: number;
    systolic_bp?: number;
    diastolic_bp?: number;
    heart_rate?: number;
    oxygen_saturation?: number;
    respiratory_rate?: number;
    is_abnormal: boolean;
    notes?: string;
    created_at: string;

    // Relationships
    recorded_by_user?: User; // Depending on your 'with' alias
}

/**
 * Lab Test Definition Interface
 */
export interface LabTestDefinition {
    id: number;
    test_name: string;
    code: string;
    price: number;
    units: string;
    description?: string;
    is_active: boolean;
}

/**
 * Supply / Inventory Interface
 */
export interface Supply {
    id: number;
    name: string;
    category: string;
    unit_of_measure: string;
    cost_per_unit: number;
    current_stock: number;
    min_stock_level: number;
}
