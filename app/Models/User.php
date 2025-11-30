<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // Important for tenancy
use Stancl\Tenancy\Database\Concerns\BelongsToTenant; // Import BelongsTo for type hinting

class User extends Authenticatable implements MustVerifyEmail
{
    use BelongsToTenant, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'role',
        'email',
        'password',
        'is_active',
        'department_id',
         'tenant_id',
        'can_assign_shift',
        'profile_picture', // Ensure profile_picture is fillable
        'address', // Add if it's a user property
        'date_of_birth', // Add if it's a user property
        'gender', // Add if it's a user property
        'hire_date', // Add if it's a user property
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'can_assign_shift' => 'boolean',
        'date_of_birth' => 'date', // Cast as date if storing as date
        'hire_date' => 'date', // Cast as date if storing as date
    ];

    // Relationships

    /**
     * Get the department that the user belongs to.
     */
    public function department(): BelongsTo // Changed to singular and added return type hinting
    {
        return $this->belongsTo(Department::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(UserShift::class);
    }
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }
    public function patients()
    {
        // For doctors: patients under their care
        return $this->hasMany(Patient::class, 'assigned_doctor_id'); // Assuming you'd add assigned_doctor_id to patients if needed
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class, 'requested_by_doctor_id');
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class, 'lab_technician_id');
    }

    public function vitalsRecorded()
    {
        return $this->hasMany(Vital::class, 'nurse_id');
    }

    public function dispensations()
    {
        return $this->hasMany(Dispensation::class, 'pharmacist_id');
    }

    public function suppliesUsed()
    {
        return $this->hasMany(SupplyUsage::class, 'user_id');
    }
}
