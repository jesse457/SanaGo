/** Shared shapes for the drug inventory screens. Mirrors `MedicationController@index`. */

export interface DrugBatch {
    id: number;
    batch_number: string;
    expiry_date: string;
    current_quantity: number;
    purchase_price: number;
    status: string;
}

export interface Drug {
    id: number;
    name: string;
    image_url: string | null;
    dosage_units: string;
    min_stock_level: number;
    description: string;
    total_stock: number;
    batches: DrugBatch[];
}

export interface BatchRow extends DrugBatch {
    drugName: string;
    drugDosage: string;
    drugId: number;
}

export interface DrugFilters {
    search?: string;
    tab?: string;
}

/** The single definition of "this batch no longer holds stock". */
export function isBatchDepleted(batch: DrugBatch): boolean {
    return batch.current_quantity === 0 || batch.status === 'depleted';
}

/** Whole days from today until `dateStr`; negative means already expired. */
export function daysUntil(dateStr: string): number {
    return (new Date(dateStr).getTime() - Date.now()) / (1000 * 3600 * 24);
}

export function formatDate(dateStr?: string | null): string {
    if (!dateStr) {
        return '—';
    }

    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
    });
}
