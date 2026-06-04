<?php

namespace App\Services\Clinical;

use App\Models\Vital;

class VitalService
{
    public function record(array $data): Vital
    {
        return Vital::create($data);
    }

    public function update(Vital $vital, array $data): Vital
    {
        $vital->update($data);

        return $vital;
    }
}
