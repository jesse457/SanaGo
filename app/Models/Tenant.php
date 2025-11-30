<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant; // Import the Str facade

class Tenant extends BaseTenant
{
    use HasDomains,HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false; // Important for UUIDs

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string'; // Important for UUIDs

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Listen for the "creating" event on the User model
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

       /**
     * Get the subscription associated with the tenant.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get the active subscription for the tenant.
     */
    public function activeSubscription()
    {
        return $this->subscription()->active();
    }

    /**
     * Check if tenant has an active subscription
     */
    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Get the current plan for the tenant
     */
    public function getCurrentPlan()
    {
        return $this->activeSubscription()?->plan ?? 'free';
    }

    /**
     * Check if tenant can access a feature
     */
    public function canAccessFeature($feature)
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->hasFeature($feature) : false;
    }

    /**
     * Get the maximum number of users allowed
     */
    public function getMaxUsers()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->max_users : 5; // Default for free plan
    }

    /**
     * Get the maximum storage allowed in MB
     */
    public function getMaxStorage()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->max_storage : 512; // Default for free plan
    }

 

}
