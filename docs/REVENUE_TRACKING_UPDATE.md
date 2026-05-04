# Revenue Tracking Logic Update

## Changes Made

### Problem
The system was tracking **both** observation fees and bed fees in the revenue calculations, with both being multiplied by the number of days a patient was admitted.

### Solution
**Removed bed fee tracking entirely** from the revenue system. Now only the **admission/observation fee** is calculated daily.

---

## Files Modified

### 1. `app/Traits/TracksRevenue.php`

#### Change 1: Revenue Columns (Line 117)
**Before:**
```php
'Admission' => ['admission_revenue', 'bed_fee_revenue'],
```

**After:**
```php
'Admission' => ['admission_revenue'], // Only track observation fee, not bed fee
```

#### Change 2: Revenue Calculation (Lines 148-196)
**Before:**
- Calculated both observation fee AND bed fee
- Returned array with 2 values: `[$totalObs, $totalBed]`
- Accessed bed type price via `$this->bed->bedType->price_per_day`

**After:**
- Calculates ONLY observation fee
- Returns array with 1 value: `[$totalObs]`
- Removed all bed fee calculation logic

**Calculation Logic:**
```php
// Days calculation (unchanged)
if (is_null($dischargeDateRaw)) {
    $days = 1; // Initial admission day
} else {
    $days = (int) $start->diffInDays($end) + 1; // Full duration
}

// Only observation fee is tracked
$obsFee = (float) ($val('observation_fee') ?? 0);
$totalObs = (float) ($days * $obsFee);

return [$totalObs]; // Single value, not two
```

---

### 2. `app/Livewire/Tenants/Admin/RevenueDashboard.php`

#### Change 1: Removed Property (Line 26)
**Removed:**
```php
public float $bedFeeRevenue = 0.0;
```

#### Change 2: Stats Assignment (Line 56)
**Removed:**
```php
$this->bedFeeRevenue = $currentStats->total_bed_fee;
```

#### Change 3: Aggregated Revenue Query (Lines 74-90)
**Before:**
```php
DB::raw('COALESCE(SUM(bed_fee_revenue), 0) as total_bed_fee')
// ...
'total_bed_fee' => (float) $result->total_bed_fee,
```

**After:**
- Removed `bed_fee_revenue` from SELECT
- Removed `total_bed_fee` from return object

#### Change 4: Sum Calculation (Lines 94-100)
**Before:**
```php
return $stats->total_medication +
       $stats->total_appointment +
       $stats->total_lab +
       $stats->total_admission +
       $stats->total_bed_fee; // ❌ Removed
```

**After:**
```php
return $stats->total_medication +
       $stats->total_appointment +
       $stats->total_lab +
       $stats->total_admission; // ✅ Only 4 components
```

#### Change 5: Patient Revenue Query (Lines 142-156)
**Before:**
```php
DB::raw('SUM(bed_fee_revenue) as bed_fees'),
DB::raw('(... + SUM(bed_fee_revenue)) as total')
```

**After:**
- Removed `bed_fees` column
- Removed from total calculation

---

## Impact

### Revenue Calculation
| Component | Before | After |
|-----------|--------|-------|
| Medications | ✅ Tracked | ✅ Tracked |
| Appointments | ✅ Tracked | ✅ Tracked |
| Lab Tests | ✅ Tracked | ✅ Tracked |
| Admission Fee | ✅ Tracked (daily) | ✅ Tracked (daily) |
| Bed Fee | ✅ Tracked (daily) | ❌ **Removed** |

### Example Calculation

**Scenario:** Patient admitted for 3 days with $50/day observation fee

**Before:**
- Observation Fee: 3 days × $50 = $150
- Bed Fee: 3 days × $30 = $90
- **Total Admission Revenue: $240**

**After:**
- Observation Fee: 3 days × $50 = $150
- Bed Fee: Not tracked
- **Total Admission Revenue: $150**

---

## Database Note

The `revenue_summaries` table still has the `bed_fee_revenue` column, but it will no longer be updated with new data. Existing historical data remains unchanged.

If you want to clean up old bed fee data, you can run:
```sql
UPDATE revenue_summaries SET bed_fee_revenue = 0;
```

---

## Testing Checklist

- [ ] Create new admission → verify only observation fee is tracked
- [ ] Update admission (change days) → verify calculation updates correctly
- [ ] Discharge patient → verify final revenue is observation fee × days
- [ ] Check revenue dashboard → verify bed fee column removed
- [ ] Check patient revenue table → verify bed fees not shown

---

**Date**: 2025-12-31
**Modified By**: AI Assistant
