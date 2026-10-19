import type {
    BeneficiaryTypeEnum,
    CashShiftStatusEnum,
    CertificateStatusEnum,
    CertificateTypeEnum,
    ClaimStatusEnum,
    GenderEnum,
    InvoiceStatusEnum,
    PayerTypeEnum,
    PaymentMethodEnum,
    PrescriptionStatusEnum,
    RadiologyStatusEnum,
    UrgencyLevelEnum,
} from './enums';

export type * from './auth';
export type * from './dashboard';
export type * from './inventory';

// Enum value constants and types — import from @/types/enums
export {
    AppointmentStatus,
    AdmissionStatus,
    BedStatus,
    BatchStatus,
    BeneficiaryType,
    CashShiftStatus,
    CertificateStatus,
    CertificateType,
    ClaimStatus,
    DepartmentType,
    EmergencyStatus,
    ExpenditureCategory,
    FeedbackStatus,
    Gender,
    InvoiceStatus,
    LabRequestStatus,
    LabResultStatus,
    PayerType,
    PaymentMethod,
    PrescriptionStatus,
    RadiologyStatus,
    ShiftType,
    SurgeryStatus,
    UrgencyLevel,
} from './enums';

export type UrgencyLevel = UrgencyLevelEnum;
export type Gender = GenderEnum;
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

/**
 * Payer / Insurance Company Interface
 */
export interface Payer {
    id: number;
    name: string;
    type: PayerTypeEnum;
    contact_person?: string;
    phone?: string;
    email?: string;
    coverage_percentage: number;
    reimbursement_days: number;
    is_active: boolean;
    patient_insurances_count?: number;
    claims_count?: number;
}

/**
 * Patient Insurance Interface
 */
export interface PatientInsurance {
    id: number;
    patient_id: number;
    payer_id: number;
    policy_number: string;
    card_number?: string;
    valid_from: string;
    valid_until: string;
    beneficiary_type: BeneficiaryTypeEnum;
    is_primary: boolean;
    patient?: Patient;
    payer?: Payer;
    claims?: Claim[];
}

/**
 * Claim Interface
 */
export interface Claim {
    id: number;
    invoice_id: number;
    payer_id: number;
    patient_insurance_id?: number;
    claim_amount: number;
    submitted_at?: string;
    status: ClaimStatusEnum;
    rejection_reason?: string;
    paid_at?: string;
    remittance_reference?: string;
    invoice?: Invoice;
    payer?: Payer;
    patient_insurance?: PatientInsurance;
}

/**
 * Invoice Interface (minimal)
 */
export interface Invoice {
    id: number;
    invoice_number: string;
    patient_id: number;
    invoice_date: string;
    due_date: string;
    subtotal: number;
    tax_amount: number;
    total_amount: number;
    status: InvoiceStatusEnum;
    paid_at?: string;
    patient?: Patient;
    items?: InvoiceItem[];
}

export interface InvoiceItem {
    id: number;
    invoice_id: number;
    item_type: string;
    item_id?: number;
    description: string;
    quantity: number;
    unit_price: number;
    total_price: number;
}

/**
 * Certificate Interface
 */
export interface Certificate {
    id: number;
    patient_id: number;
    consultation_id?: number;
    issued_by: number;
    type: CertificateTypeEnum;
    serial_number: string;
    from_date: string;
    to_date: string;
    duration_days?: number;
    diagnosis_summary?: string;
    recommendations?: string;
    employer_name?: string;
    employer_address?: string;
    status: CertificateStatusEnum;
    patient?: Patient;
    doctor?: User;
}

/**
 * Radiology Request Interface
 */
export interface RadiologyRequest {
    id: number;
    patient_id: number;
    consultation_id?: number;
    requested_by: number;
    imaging_type: string;
    body_part: string;
    clinical_notes?: string;
    status: RadiologyStatusEnum;
    patient?: Patient;
    requested_by_user?: User;
    result?: RadiologyResult;
}

/**
 * Radiology Result Interface
 */
export interface RadiologyResult {
    id: number;
    request_id: number;
    performed_by?: number;
    findings?: string;
    conclusion?: string;
    validated_by?: number;
    validated_at?: string;
    status: RadiologyStatusEnum;
    performed_by_user?: User;
    validated_by_user?: User;
}

/**
 * Cash Shift Interface
 */
export interface CashShift {
    id: number;
    user_id: number;
    payment_point: string;
    opening_balance: number;
    closing_balance?: number;
    expected_total?: number;
    variance?: number;
    status: CashShiftStatusEnum;
    opened_at: string;
    closed_at?: string;
    payments?: CashPayment[];
    payments_count?: number;
    user?: User;
}

/**
 * Cash Payment Interface
 */
export interface CashPayment {
    id: number;
    cash_shift_id: number;
    invoice_id?: number;
    patient_id?: number;
    amount_paid: number;
    payment_method: PaymentMethodEnum;
    patient?: Patient;
    invoice?: Invoice;
    cash_shift?: CashShift;
}

/**
 * Prescription Interface
 */
export interface Prescription {
    id: number;
    patient_id: number;
    doctor_id: number;
    prescription_date: string;
    status: PrescriptionStatusEnum;
    notes?: string;
    total_amount: number;
    patient?: Patient;
    doctor?: User;
    items?: PrescriptionItem[];
    items_count?: number;
}

export interface PrescriptionItem {
    id: number;
    prescription_id: number;
    medication_id: number;
    drug_name: string;
    dosage: string;
    frequency: string;
    duration: string;
    quantity: number;
    unit_price: number;
    instructions?: string;
    medication?: Medication;
}

export interface Medication {
    id: number;
    name: string;
    strength?: string;
    dosage_units?: string;
    unit_cost: number;
}
