<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Supply extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'tenant_id',
        'unit_of_measure',
        'current_stock',
        'min_stock_level',
    ];

    public function usages()
    {
        return $this->hasMany(SupplyUsage::class);
    }
}
