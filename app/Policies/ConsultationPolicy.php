<?php

namespace App\Policies;

use App\Enums\PaymentStatus;
use App\Models\MedicalRecord;
use App\Models\User;

/**
 * Clinical records and their price link.
 *
 * A doctor may only touch the billing snapshot of their own consultation, and
 * only while the linked charge is unpaid. Once the patient has paid, the
 * snapshot and link are immutable: correcting the money belongs to the cash
 * desk, not to the clinical record.
 */
class ConsultationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-consultations');
    }

    public function view(User $user, MedicalRecord $consultation): bool
    {
        return $user->can('view-consultations');
    }

    public function update(User $user, MedicalRecord $consultation): bool
    {
        return $this->updatePrice($user, $consultation);
    }

    /**
     * May this user set or change the consultation's price and charge link?
     */
    public function updatePrice(User $user, MedicalRecord $consultation): bool
    {
        if ($user->id !== $consultation->doctor_id) {
            return false;
        }

        $charge = $consultation->charge;

        if ($charge === null) {
            return true;
        }

        return $charge->settled_at === null
            && $charge->payment_status === PaymentStatus::Pending;
    }
}
