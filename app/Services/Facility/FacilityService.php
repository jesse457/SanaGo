<?php

namespace App\Services\Facility;

use App\Models\Bed;
use App\Models\BedType;
use App\Models\Ward;

class FacilityService
{
    public function createWard(array $data): Ward
    {
        return Ward::create($data);
    }

    public function updateWard(Ward $ward, array $data): Ward
    {
        $ward->update($data);

        return $ward;
    }

    public function createBed(array $data): Bed
    {
        return Bed::create($data);
    }

    public function updateBed(Bed $bed, array $data): Bed
    {
        $bed->update($data);

        return $bed;
    }

    public function createBedType(array $data): BedType
    {
        return BedType::create($data);
    }

    public function updateBedType(BedType $bedType, array $data): BedType
    {
        $bedType->update($data);

        return $bedType;
    }
}
