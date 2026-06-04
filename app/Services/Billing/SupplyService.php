<?php

namespace App\Services\Billing;

use App\Models\Supply;
use App\Models\SupplyUsage;

class SupplyService
{
    public function recordUsage(array $data): SupplyUsage
    {
        $supply = Supply::lockForUpdate()->find($data['supply_id']);
        if ($supply->current_stock < $data['quantity_used']) {
            throw new \Exception("Insufficient stock for {$supply->name}");
        }
        $supply->decrement('current_stock', $data['quantity_used']);

        return SupplyUsage::create($data);
    }
}
