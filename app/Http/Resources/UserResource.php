<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
  // app/Http/Resources/UserResource.php

public function toArray(Request $request): array
{
    // Sort the loaded shifts by date to find the most recent one
    $latestShift = $this->shifts->sortByDesc('shift_date')->first();

    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'email_verified_at' => $this->email_verified_at,
        'user_picture' => $this->profile_picture
            ? Storage::disk('s3')->temporaryUrl($this->profile_picture, now()->addMinutes(15))
            : null,
        'role' => $this->role,
        'phone_number' => $this->phone_number,
        'is_active' => (bool) $this->is_active,

        // FIX: Provide the ID of the latest shift for the React State
        'upcoming_shift_id' => $latestShift ? $latestShift->id : null,

        // Optional: Provide a formatted string for the UI table
        'latest_shift_details' => $latestShift ? [
            'type' => $latestShift->shift_type,
            'date' => $latestShift->shift_date->format('Y-m-d'),
        ] : null,
    ];
}
}
