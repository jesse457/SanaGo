<?php

namespace App\Models;

use App\Enums\Gender;
use Carbon\Carbon;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['patient_uid', 'first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender', 'address', 'last_visit_date', 'allergies', 'blood_type'])]
class Patient extends Model implements HasMedia
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, InteractsWithMedia, LogsActivity;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'last_visit_date' => 'datetime',
            'gender' => Gender::class,
        ];
    }

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['age', 'photo_url'];

    /**
     * Accessor for Age.
     */
    public function getAgeAttribute()
    {
        if (! $this->date_of_birth) {
            return null;
        }

        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }

    // app/Models/Patient.php

    public function latestAdmission()
    {
        return $this->hasOne(Admission::class)->latestOfMany();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function nurseCareReports(): HasMany
    {
        return $this->hasMany(NurseCareReport::class);
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class);
    }

    public function dispensations(): HasMany
    {
        return $this->hasMany(Dispensation::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function supplyUsages(): HasMany
    {
        return $this->hasMany(SupplyUsage::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf', 'image/jpeg', 'image/png',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
    }
}
