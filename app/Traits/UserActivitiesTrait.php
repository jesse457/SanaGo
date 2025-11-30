<?php

namespace App\Traits;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

trait UserActivitiesTrait
{
    public function logActivity(string $type, string $description, array $properties = [])
    {
        UserActivity::create([
            'user_id' => Auth::id(),          // the receptionist
            'activity_type' => $type,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
