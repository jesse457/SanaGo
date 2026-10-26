/** Shapes shared by the cash desk screen and its components. */

export interface PatientOption {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid?: string | null;
    phone?: string | null;
    gender?: string | null;
}

/** One line on a bill: a module charge, a catalogue service, or hand-written. */
export interface BillLine {
    id: number;
    description: string;
    item_type: string;
    module: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    settled: boolean;
    /** Written at the desk, so it may be corrected or removed. */
    manual: boolean;
    adjustable: boolean;
}

export interface BillClaim {
    id: number;
    status: string;
    claim_amount: number;
    payer?: string | null;
}

export interface BillPayment {
    id: number;
    amount: number;
    method: string;
    paid_at?: string | null;
}

export interface Bill {
    id: number;
    invoice_number: string;
    status: string;
    invoice_date?: string | null;
    total_amount: number;
    total_paid: number;
    remaining_balance: number;
    is_open: boolean;
    claim: BillClaim | null;
    payments: BillPayment[];
    items: BillLine[];
}

/** Heading a hand-written charge can be filed under. */
export interface ChargeCategory {
    value: string;
    module: string;
}

export interface CatalogueService {
    id: number;
    name: string;
    code?: string | null;
    category?: string | null;
    price: number;
    requires_upfront_payment: boolean;
}

/** Where a billable line comes from: the record the counter sells it from. */
export type PickSource =
    | 'service'
    | 'medication'
    | 'lab_test'
    | 'admission_bed'
    | 'admission_service';

/**
 * One thing the cashier can put on a bill.
 *
 * A drug comes from the pharmacy stock, a test from the laboratory catalogue, a
 * bed day from the patient's open stay and the rest from the price list — the
 * price is always the catalogue's, never typed.
 */
export interface CatalogueItem {
    key: string;
    source: PickSource;
    source_id: number;
    item_type: string;
    item_id: number | null;
    module: string;
    name: string;
    code?: string | null;
    price: number;
    unit?: string | null;
    stock?: number | null;
    /** Units the line should start with — the nights of a stay, usually 1. */
    quantity: number;
    note?: string | null;
}

/** A line the counter has put in the basket, not yet paid. */
export interface BasketLine {
    key: string;
    source: PickSource;
    source_id: number;
    module: string;
    name: string;
    unit?: string | null;
    price: number;
    quantity: number;
}

/** The stay the patient is in right now, auto-detected from the admission. */
export interface OngoingAdmission {
    id: number;
    admitted_at?: string | null;
    nights: number;
    ward?: string | null;
    bed?: string | null;
    bed_type?: string | null;
    doctor?: string | null;
    reason?: string | null;
    suggestions: CatalogueItem[];
}

export interface CounterModule {
    value: string;
    label: string;
}

export interface ClaimContext {
    can_claim: boolean;
    payers: { id: number; name: string; coverage_percentage: number }[];
    insurances: {
        id: number;
        payer_id: number;
        payer: string | null;
        policy_number: string | null;
        is_primary: boolean;
    }[];
}

/** Everything the desk loads for one patient. */
export interface BillPayload {
    bills: Bill[];
    catalogue: CatalogueItem[];
    modules: CounterModule[];
    admission: OngoingAdmission | null;
    categories: ChargeCategory[];
    claim_context?: ClaimContext;
}

/** One row of the merged bills list. */
export interface BillRow {
    id: number;
    invoice_number: string;
    patient_id: number;
    patient_name: string;
    patient_uid?: string | null;
    invoice_date?: string | null;
    status: string;
    total_amount: number;
    remaining_balance: number;
    items_count: number;
}

export interface PaginatedBills {
    data: BillRow[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
}

/** A charge typed by the cashier but not yet saved. */
export interface ChargeDraft {
    category: string;
    description: string;
    quantity: string;
    unit_price: string;
}
