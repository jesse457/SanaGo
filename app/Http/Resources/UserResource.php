<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at, // Crucial for React logic
            'user_picture' => $this->profile_picture
                ? Storage::disk('s3')->temporaryUrl($this->profile_picture, now()->addMinutes(15))
                : null,
            'role' => $this->role,
            'tenant_id' => $this->tenant_id,
            'department_id' => $this->department_id,
            'phone_number' => $this->phone_number,
            'profile_picture' => $this->profile_picture,
            'is_active' => (bool) $this->is_active,
            // Include relationships if loaded
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
