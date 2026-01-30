<?php

namespace App\Services;

use App\Models\Bed;
use App\Models\BedType;
use App\Models\Department;
use App\Models\Supply;
use App\Models\UserActivity;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsService
{
    /**
     * Private helper to log user activity
     */
    private function logActivity(string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => $type, // e.g., 'created', 'updated', 'deleted'
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get Query for entities with search
     */
    public function getEntityQuery(string $type, string $search = ''): Builder
    {
        $tenantId = tenant('id');

        $query = match ($type) {
            'department' => Department::query(),
            'ward' => Ward::with('department'),
            'bed-type' => BedType::query(),
            'bed' => Bed::with(['ward.department', 'bedType']),
            'supply' => Supply::query(),
            default => throw new \Exception('Invalid setting type'),
        };

        return $query->where('tenant_id', $tenantId)
            ->when($search, function ($q) use ($type, $search) {
                $field = ($type === 'bed') ? 'bed_number' : 'name';

                return $q->where($field, 'ILIKE', "%{$search}%");
            });
    }

    /**
     * Update Tenant Info & Logo
     */
    public function updateGeneralSettings(array $data, $logoFile = null): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($data, $logoFile) {
            $tenant = tenant();
            $tenant->update([
                'name' => $data['name'],
                'address' => $data['address'],
                'contact_email' => $data['email'],
            ]);

            if ($logoFile) {
                if ($tenant->logo) {
                    Storage::disk('s3')->delete($tenant->logo);
                }
                $tenant->logo = $logoFile->store('logos', 's3');
                $tenant->save();
            }

            $this->logActivity('updated', "Updated general tenant settings and logo for: {$tenant->name}");
        });
    }

    /**
     * CRUD: Create
     */
    public function createItem(string $type, array $data)
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($type, $data) {
            $data['tenant_id'] = tenant('id');
            if ($type === 'bed') {
                $data['is_occupied'] = false;
            }

            $item = match ($type) {
                'department' => Department::create($data),
                'ward' => Ward::create($data),
                'bed-type' => BedType::create($data),
                'bed' => Bed::create($data),
                'supply' => Supply::create($data),
            };

            $identifier = $item->name ?? $item->bed_number ?? $item->id;
            $this->logActivity('created', "Created new {$type}: {$identifier}");

            return $item;
        });
    }

    /**
     * CRUD: Update
     */
    public function updateItem(string $type, int $id, array $data)
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($type, $id, $data) {
            $item = $this->getEntityQuery($type)->findOrFail($id);
            $item->update($data);

            $identifier = $item->name ?? $item->bed_number ?? $item->id;
            $this->logActivity('updated', "Updated {$type}: {$identifier}");

            return $item;
        });
    }

    /**
     * CRUD: Delete
     */
    public function deleteItem(string $type, int $id): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($type, $id) {
            $item = $this->getEntityQuery($type)->findOrFail($id);
            $identifier = $item->name ?? $item->bed_number ?? $item->id;

            $item->delete();

            $this->logActivity('deleted', "Deleted {$type}: {$identifier}");
        });
    }

    /**
     * Helper to get dropdown options for forms
     */
    public function getFormOptions(): array
    {
        $tenantId = tenant('id');

        return [
            'departments' => Department::where('tenant_id', $tenantId)->get(['id', 'name']),
            'wards' => Ward::where('tenant_id', $tenantId)->get(['id', 'name']),
            'bedTypes' => BedType::where('tenant_id', $tenantId)->get(['id', 'name']),
        ];
    }
}
