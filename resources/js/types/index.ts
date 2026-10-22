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
 * Radiology Request Interface — the queue row `RadiologyRequestController`
 * projects for the index, detail and report screens.
 */
export interface RadiologyRequest {
    id: number;
    patient_id?: number;
    consultation_id?: number | null;
    requested_by?: { id: number; name: string } | null;
    imaging_type: string;
    body_part: string;
    clinical_notes?: string | null;
    status: RadiologyStatusEnum;
    request_date?: string | null;
    patient?: Patient | null;
    result?: RadiologyResult | null;
}

/**
 * Radiology Result Interface — a report as the archive and the report form
 * receive it.
 */
export interface RadiologyResult {
    id: number;
    request_id: number;
    performed_by?: { id: number; name: string } | null;
    findings?: string | null;
    conclusion?: string | null;
    validated_by?: { id: number; name: string } | null;
    validated_at?: string | null;
    status: RadiologyStatusEnum;
}

/**
 * Cash Shift Interface
 */
export interface CashShift {
    id: number;
    user_id: number;
    /** The till this shift belongs to (reception, laboratory, pharmacy...). */
    cash_point_id?: number | null;
    cash_point?: { id: number; name: string; receipt_prefix?: string } | null;
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
    cash_point_id?: number | null;
    cash_point?: { id: number; name: string } | null;
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

/**
 * Paginated Laravel collection envelope (`->paginate()`).
 */
export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
}

/**
 * A vitals row as the API serialises it: decimals arrive as strings, and the
 * recorder is exposed through the `recordedBy` relation.
 */
export interface VitalRecord {
    id: number;
    patient_id: number;
    recorded_by: number | null;
    recorded_at: string;
    temperature: string | number | null;
    systolic_bp: number | null;
    diastolic_bp: number | null;
    heart_rate: number | null;
    oxygen_saturation: number | null;
    respiratory_rate: number | null;
    is_abnormal: boolean;
    notes: string | null;
    created_at: string;
    recordedBy?: { id: number; name: string } | null;
}

/**
 * Admission row (list/dossier form) with the bed and ward it occupies.
 */
export interface AdmissionRecord {
    id: number;
    patient_id: number;
    bed_id: number | null;
    attending_doctor_id: number | null;
    admission_date: string | null;
    discharge_date: string | null;
    status: string;
    reason_for_admission: string | null;
    observation_fee: string | number | null;
    bed?: {
        id: number;
        code: string;
        ward?: { id: number; name: string } | null;
    } | null;
    attending_doctor?: { id: number; name: string } | null;
}

/**
 * Shared hospital identity from `HandleInertiaRequests` (`tenant` prop).
 */
export interface TenantIdentity {
    id: string;
    name: string;
    slug: string;
}

/**
 * A radiology request as the Print view receives it (`patient` +
 * `requestedBy` loaded).
 */
export interface RadiologyRequestRecord {
    id: number;
    patient_id: number;
    consultation_id: number | null;
    requested_by: number | null;
    imaging_type: string;
    body_part: string;
    clinical_notes: string | null;
    status: string;
    created_at: string;
    updated_at: string;
    patient?: {
        id: number;
        patient_uid: string;
        first_name: string;
        last_name: string;
        date_of_birth?: string | null;
        gender?: string | null;
        age?: number | null;
        phone?: string | null;
        blood_type?: string | null;
    } | null;
    requestedBy?: { id: number; name: string } | null;
}

/**
 * A cash payment as the Receipt view receives it (shift, patient and invoice
 * loaded).
 */
export interface CashPaymentRecord {
    id: number;
    cash_shift_id: number;
    invoice_id: number | null;
    patient_id: number | null;
    amount_paid: string | number;
    payment_method: string;
    created_at: string;
    updated_at: string;
    cashShift?: {
        id: number;
        payment_point: string;
        opened_at: string | null;
        closed_at: string | null;
        status: string;
        user?: { id: number; name: string } | null;
    } | null;
    patient?: {
        id: number;
        patient_uid: string;
        first_name: string;
        last_name: string;
    } | null;
    invoice?: {
        id: number;
        invoice_number: string;
        total_amount: string | number;
    } | null;
}
