<?php

namespace App\Models;

use Database\Factories\LabTestDefinitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['test_name', 'code', 'price', 'units', 'description', 'is_active'])]
class LabTestDefinition extends Model
{
    /** @use HasFactory<LabTestDefinitionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class, 'test_definition_id');
    }
}
