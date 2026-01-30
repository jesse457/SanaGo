<?php

namespace App\Services;

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Helper to log activities
     */
    private function logActivity(int $userId, string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create User with Transaction & S3 logic
     */
    public function createUser(array $data, ?UploadedFile $profilePicture, $tenantId): ?User
    {
        $storedPath = null;
        try {
            $user = DB::connection('pgsql')->transaction(function () use ($data, $profilePicture, $tenantId, &$storedPath) {
                if ($profilePicture) {
                    $storedPath = $profilePicture->store('profile_pictures', 's3');
                }

                $newUser = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'address' => $data['address'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'profile_picture' => $storedPath,
                    'department_id' => $data['department_id'] ?? null,
                    'role' => $data['role'],
                    'is_active' => $data['is_active'] ?? true,
                    'password' => Hash::make(Str::random(32)),
                    'tenant_id' => $tenantId,
                ]);

                // LOG ACTIVITY
                $this->logActivity($newUser->id, 'created', "Account created for {$newUser->name} ({$newUser->role})");

                return $newUser;
            });

            if ($user) {
                $this->sendInvitation($user);
            }

            return $user;
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('s3')->delete($storedPath);
            }
            Log::error('USER_CREATION_FAILED', ['msg' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update User & Manage Shift Pivot
     */
    public function updateUser(int $userId, array $data): User
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);
            $user->update(collect($data)->except('selected_shift_id')->toArray());

            if (array_key_exists('selected_shift_id', $data)) {
                $currentUpcoming = $user->shifts()->where('shift_date', '>=', now()->startOfDay())->first();
                if ($currentUpcoming?->id !== $data['selected_shift_id']) {
                    if ($currentUpcoming) {
                        $user->shifts()->detach($currentUpcoming->id);
                    }
                    if ($data['selected_shift_id']) {
                        $user->shifts()->attach($data['selected_shift_id']);
                    }
                }
            }

            // LOG ACTIVITY
            $this->logActivity($user->id, 'updated', 'User profile details updated.');

            return $user;
        });
    }

    public function getAttachmentPreview(int $userId): array
    {
        $user = User::findOrFail($userId);

        return [
            'url' => $user->profile_picture ? Storage::disk('s3')->temporaryUrl($user->profile_picture, now()->addMinutes(15)) : null,
            'mime' => $user->profile_picture ? Storage::disk('s3')->mimeType($user->profile_picture) : null,
            'name' => $user->profile_picture,
        ];
    }

    public function sendInvitation(User $user): void
    {
        $token = Password::broker()->createToken($user);
        $domain = function_exists('tenant') && tenant() ? (tenant()->domains->first()->domain ?? request()->getHost()) : request()->getHost();
        $tenantName = function_exists('tenant') && tenant() ? tenant('name') : config('app.name');
        Mail::to($user->email)->queue(new UserInvitationMail($user, $token, $domain, $tenantName));
    }

    public function getUsersQuery(array $filters = [])
    {
        return User::query()
            ->with('shifts')
            ->where('role', '!=', 'admin')
            ->when(! empty($filters['search']), fn ($q) => $q->where(fn ($sq) => $sq->where('name', 'ILIKE', "%{$filters['search']}%")->orWhere('email', 'ILIKE', "%{$filters['search']}%")))
            ->when(! empty($filters['role']), fn ($q) => $q->where('role', $filters['role']))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('is_active', $filters['status'] === 'active'))
            ->orderBy('name');
    }

    public function getUserById($id): ?User
    {
        return User::find($id);
    }
}
