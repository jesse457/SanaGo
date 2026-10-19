/**
 * Shared shapes for the Settings pages. Mirrors what the backend already sends
 * (see `InfastructureController@index` and `UserActivityController@index`) so
 * the page-local components can be typed without touching server code.
 */

export interface WardRecord {
    id: number;
    name?: string | null;
    code?: string | null;
    floor?: string | null;
    capacity?: number | string | null;
    description?: string | null;
    beds_count?: number | null;
    bed_type_id?: number | null;
    ward_id?: number | null;
    status?: string | null;
}

export interface BedRecord {
    id: number;
    code?: string | null;
    status?: string | null;
    ward?: { name?: string | null } | null;
}

export interface BedTypeRecord {
    id: number;
    name?: string | null;
    capacity?: number | string | null;
}

export type UtilitiesEntityType = 'ward' | 'bed' | 'type';
