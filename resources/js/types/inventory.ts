export interface Batch {
    id: number;
    batch_number: string;
    expiry_date: string;
    initial_quantity: number;
    current_quantity: number;
    purchase_price: number | null;
    purchase_date: string | null;
    supplier: string | null;
    status: 'active' | 'depleted' | 'expired';
    notes: string | null;
    created_at: string;
    updated_at: string;
}

export interface BatchWithItem extends Batch {
    batchable_type: string;
    batchable_id: number;
}