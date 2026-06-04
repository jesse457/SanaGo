export interface Patient {
    first_name: string;
    last_name: string;
    patient_uid: string;
}

export interface TestDefinition {
    test_name: string;
}

export interface Doctor {
    name: string;
}

export interface Attachment {
    id: number;
    file_path: string;
    file_name: string;
}

export interface LabRequest2 {
    id: number;
    patient: Patient;
    test_definition: TestDefinition;
    doctor: Doctor;
    reason_for_test: string | null;
    results_text?: string;
    analysis_comments?: string;
    result?: any; // If this exists, we are in Edit mode
    attachments?: Attachment[];
}
export interface LabResult {
    id: number;
    lab_request_id: number;
    status: 'Completed' | 'Cancelled' | 'In Progress';
    result_date: string;
    lab_request?: {
        patient?: {
            first_name: string;
            last_name: string;
            patient_uid: string;
        };
        test_definition?: {
            test_name: string;
        };
    };
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    to: number | null;
    last_page: number;
    per_page: number;
    total: number;
    path: string;
}

export interface PaginatedResults<T> {
    data: T[];
    links: PaginationLink[];
    meta: PaginationMeta;
}

export interface LabTest {
    id: number;
    test_name: string;
    code?: string;
    price: number;
    units: string;
    description?: string;
}
export type LabRequestStatus = 'Pending' | 'In_Progress' | 'Completed';

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
    request_date: string;
    status: LabRequestStatus;
    result: any | null; // Checking if result exists to toggle Enter/Edit
}
