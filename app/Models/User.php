<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'phone_number', 'address', 'date_of_birth', 'gender', 'can_assign_shift', 'email', 'password', 'is_active', 'department_id', 'hire_date', 'locale'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'can_assign_shift' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('profile_picture') ?: null;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(UserShift::class);
    }

    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function doctorPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class, 'doctor_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    public function nurseCareReports(): HasMany
    {
        return $this->hasMany(NurseCareReport::class, 'nurse_id');
    }

    public function vitalsRecorded(): HasMany
    {
        return $this->hasMany(Vital::class, 'recorded_by');
    }

    public function dispensations(): HasMany
    {
        return $this->hasMany(Dispensation::class, 'dispensed_by');
    }

    public function supplyUsages(): HasMany
    {
        return $this->hasMany(SupplyUsage::class, 'used_by');
    }

    public function feedbackResponses(): HasMany
    {
        return $this->hasMany(Feedback::class, 'responder_id');
    }

    public function attendingDoctorAdmissions(): HasMany
    {
        return $this->hasMany(Admission::class, 'attending_doctor_id');
    }

    public function surgeries(): HasMany
    {
        return $this->hasMany(Surgery::class, 'surgeon_id');
    }

    public function anesthetistSurgeries(): HasMany
    {
        return $this->hasMany(Surgery::class, 'anesthetist_id');
    }

    public function emergencyCases(): HasMany
    {
        return $this->hasMany(EmergencyCase::class, 'attending_doctor_id');
    }

    public function triagedEmergencies(): HasMany
    {
        return $this->hasMany(EmergencyCase::class, 'triage_nurse_id');
    }

    public function recordedExpenditures(): HasMany
    {
        return $this->hasMany(Expenditure::class, 'recorded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_picture')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }
}
