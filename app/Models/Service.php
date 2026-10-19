<?php

namespace App\Models;

use App\Enums\ServiceCategory;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'code', 'category', 'description', 'source_model', 'source_id', 'default_price', 'is_active', 'requires_upfront_payment'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'category' => ServiceCategory::class,
            'default_price' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_upfront_payment' => 'boolean',
        ];
    }

    public function admissionServices(): HasMany
    {
        return $this->hasMany(AdmissionService::class);
    }

    /**
     * Lab test definitions linked to this service.
     */
    public function labTestDefinition(): HasOne
    {
        return $this->hasOne(LabTestDefinition::class);
    }

    /**
     * Bed types linked to this service.
     */
    public function bedType(): HasOne
    {
        return $this->hasOne(BedType::class);
    }

    /**
     * Medications linked to this service.
     */
    public function medication(): HasOne
    {
        return $this->hasOne(Medication::class);
    }

    /**
     * Returns the source model instance if source_model and source_id are set.
     */
    public function source(): ?Model
    {
        if (! $this->source_model || ! $this->source_id) {
            return null;
        }

        return $this->source_model::find($this->source_id);
    }

    /**
     * Scope for active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for services that require upfront payment.
     */
    public function scopeUpfront($query)
    {
        return $query->where('requires_upfront_payment', true);
    }

    /**
     * Scope for services that can be billed after rendering.
     */
    public function scopePostPay($query)
    {
        return $query->where('requires_upfront_payment', false);
    }
}
