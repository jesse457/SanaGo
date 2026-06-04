// Mirrors PHP enums in app/Enums/

export const AppointmentStatus = {
    SCHEDULED: 'scheduled',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
} as const;
export type AppointmentStatusEnum =
    (typeof AppointmentStatus)[keyof typeof AppointmentStatus];

export const AdmissionStatus = {
    ADMITTED: 'admitted',
    DISCHARGED: 'discharged',
    TRANSFERRED: 'transferred',
    PENDING: 'pending',
} as const;
export type AdmissionStatusEnum =
    (typeof AdmissionStatus)[keyof typeof AdmissionStatus];

export const BatchStatus = {
    ACTIVE: 'active',
    EXPIRED: 'expired',
    DEPLETED: 'depleted',
} as const;
export type BatchStatusEnum = (typeof BatchStatus)[keyof typeof BatchStatus];

export const BedStatus = {
    AVAILABLE: 'available',
    OCCUPIED: 'occupied',
    MAINTENANCE: 'maintenance',
} as const;
export type BedStatusEnum = (typeof BedStatus)[keyof typeof BedStatus];

export const DepartmentType = {
    CLINICAL: 'clinical',
    ADMINISTRATIVE: 'administrative',
    SUPPORT: 'support',
} as const;
export type DepartmentTypeEnum =
    (typeof DepartmentType)[keyof typeof DepartmentType];

export const FeedbackStatus = {
    PENDING: 'pending',
    ADDRESSED: 'addressed',
    DISMISSED: 'dismissed',
} as const;
export type FeedbackStatusEnum =
    (typeof FeedbackStatus)[keyof typeof FeedbackStatus];

export const Gender = {
    MALE: 'male',
    FEMALE: 'female',
    OTHER: 'other',
} as const;
export type GenderEnum = (typeof Gender)[keyof typeof Gender];

export const InvoiceStatus = {
    PENDING: 'pending',
    PAID: 'paid',
    OVERDUE: 'overdue',
} as const;
export type InvoiceStatusEnum =
    (typeof InvoiceStatus)[keyof typeof InvoiceStatus];

export const LabRequestStatus = {
    PENDING: 'pending',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
} as const;
export type LabRequestStatusEnum =
    (typeof LabRequestStatus)[keyof typeof LabRequestStatus];

export const LabResultStatus = {
    NORMAL: 'normal',
    ABNORMAL: 'abnormal',
    CRITICAL: 'critical',
} as const;
export type LabResultStatusEnum =
    (typeof LabResultStatus)[keyof typeof LabResultStatus];

export const PrescriptionStatus = {
    NEW: 'new',
    PARTIAL: 'partial',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
} as const;
export type PrescriptionStatusEnum =
    (typeof PrescriptionStatus)[keyof typeof PrescriptionStatus];

export const ShiftType = {
    MORNING: 'morning',
    AFTERNOON: 'afternoon',
    NIGHT: 'night',
} as const;
export type ShiftTypeEnum = (typeof ShiftType)[keyof typeof ShiftType];

export const UrgencyLevel = {
    NORMAL: 'normal',
    URGENT: 'urgent',
    EMERGENCY: 'emergency',
} as const;
export type UrgencyLevelEnum = (typeof UrgencyLevel)[keyof typeof UrgencyLevel];

export const SurgeryStatus = {
    SCHEDULED: 'scheduled',
    IN_PROGRESS: 'in_progress',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled',
} as const;
export type SurgeryStatusEnum =
    (typeof SurgeryStatus)[keyof typeof SurgeryStatus];

export const EmergencyStatus = {
    TRIAGE: 'triage',
    ACTIVE: 'active',
    STABILIZED: 'stabilized',
    ADMITTED: 'admitted',
    DISCHARGED: 'discharged',
    DECEASED: 'deceased',
} as const;
export type EmergencyStatusEnum =
    (typeof EmergencyStatus)[keyof typeof EmergencyStatus];

export const ExpenditureCategory = {
    SALARIES: 'salaries',
    SUPPLIES: 'supplies',
    UTILITIES: 'utilities',
    MAINTENANCE: 'maintenance',
    EQUIPMENT: 'equipment',
    PHARMACEUTICALS: 'pharmaceuticals',
    ADMINISTRATIVE: 'administrative',
    OTHER: 'other',
} as const;
export type ExpenditureCategoryEnum =
    (typeof ExpenditureCategory)[keyof typeof ExpenditureCategory];
