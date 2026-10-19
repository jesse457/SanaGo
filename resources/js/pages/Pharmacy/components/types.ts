/** Shared shapes for the pharmacy dispensation screen. */

/** Prescription line as delivered by `DispensationController`. */
export interface PrescriptionItem {
    id: any;
    quantity: number;
    dosage: string;
    medication: any;
}

export interface Prescription {
    id: any;
    prescription_date: string;
    doctor?: { name?: string } | null;
    items: PrescriptionItem[];
}

export interface DispensePatient {
    id: any;
    first_name: string;
    last_name: string;
    patient_uid: string;
    prescriptions: Prescription[];
}

export interface DispenseMedication {
    id: any;
    name: string;
    total_stock: number;
    dosage_units: string;
    unit_cost: number;
}

/** One line of the dispense cart — the exact payload key set the form posts. */
export interface DispenseCartItem {
    medication_id: any;
    name: string;
    quantity: number;
    max: number;
    stock: number;
    price: number;
}

export type DispenseViewMode = 'prescription' | 'walkin';
