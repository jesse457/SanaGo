export interface MonthlyData {
    month: string;
    revenue: number;
    expenditure: number;
    profit: number;
    invoices: number;
    surgeries: number;
    emergencies: number;
    admissions: number;
}

export interface RevenueSummary {
    total_revenue: number;
    invoices: number;
    invoice_count: number;
    surgeries: number;
    surgery_count: number;
    emergencies: number;
    emergency_count: number;
    admissions: number;
}

export interface AdminDashboardMetrics {
    patient_stats?: {
        total: number;
        new_today: number;
        active_admissions: number;
    };
    appointment_stats?: {
        total: number;
        waiting: number;
        completed: number;
        cancelled: number;
    };
    clinical_stats?: {
        active_surgeries: number;
        pending_lab_requests: number;
        pending_prescriptions: number;
    };
    emergency_stats?: {
        active_count: number;
    };
    revenue_summary?: RevenueSummary | null;
    outstanding_invoices?: number;
    outstanding_invoices_count?: number;
    bed_occupancy?: {
        percentage: number;
        occupied: number;
        total: number;
    };
    monthly_data?: MonthlyData[];
    recent_activity?: {
        id: number;
        description: string;
        event: string | null;
        causer_name: string;
        created_at: string;
    }[];
    // Role-specific optional stats
    lab_stats?: {
        pending_requests: number;
        completed_today: number;
        in_progress: number;
    };
    pharmacy_stats?: {
        pending_prescriptions: number;
        dispensed_today: number;
        low_stock_count: number;
    };
    radiology_stats?: {
        pending: number;
        completed_today: number;
    };
    // [key: string]: any;
}

export interface LabRequest {
    id: number;
    patient?: {
        first_name: string;
        last_name: string;
        patient_uid: string;
    };
    test_definition?: {
        test_name: string;
    };
    doctor?: {
        name: string;
    };
    urgency_level: 'Normal' | 'High' | 'Urgent';
    created_at: string;
}

export interface DashboardProps {
    completedTestsToday: any[];
    inProgressTest: any[];
    pendingLabRequests: LabRequest[];
}
