<?php

namespace App\Enums;

/**
 * What a hand-written patient charge is for.
 *
 * The price list (`services`) prices the recurring catalogue. A hospital stay
 * also produces charges nobody pre-priced — an admission fee, an extra night,
 * oxygen, a dressing done twice, a procedure added at the desk. The cashier
 * bills those by hand, and this enum says under which heading.
 *
 * The value doubles as the invoice line `item_type`, so till permissions
 * (`CashPoint::canCollect()`) and the module badge on the counter keep working
 * without a second lookup table.
 */
enum PatientChargeCategory: string
{
    case Admission = 'admission';
    case Bed = 'bed';
    case Consultation = 'consultation';
    case Procedure = 'procedure';
    case Nursing = 'nursing';
    case Observation = 'observation';
    case Surgery = 'surgery';
    case Emergency = 'emergency';
    case Laboratory = 'laboratory';
    case Radiology = 'radiology';
    case Pharmacy = 'pharmacy';
    case Other = 'other';
}
