<?php

declare(strict_types=1);

namespace App\Services\Platform;

use App\Models\PlatformAuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Writes the central audit trail for landlord / platform actions.
 *
 * Logging must never break the action it describes: if the central database
 * is unavailable the entry is reported and dropped.
 */
class PlatformAuditService
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function log(
        string $action,
        ?string $description = null,
        Model|string|null $subject = null,
        ?string $tenantId = null,
        array $properties = [],
        ?User $actor = null,
    ): ?PlatformAuditLog {
        try {
            $actor ??= Auth::user();

            [$subjectType, $subjectId] = $this->resolveSubject($subject);

            return PlatformAuditLog::create([
                'actor_id' => $actor?->getKey(),
                'actor_name' => $actor?->name,
                'actor_email' => $actor?->email,
                'action' => $action,
                'description' => $description,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'tenant_id' => $tenantId,
                'properties_json' => $properties === [] ? null : $properties,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string) Request::userAgent(), 0, 255),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function resolveSubject(Model|string|null $subject): array
    {
        if ($subject instanceof Tenant) {
            return [Tenant::class, (string) $subject->getTenantKey()];
        }

        if ($subject instanceof Model) {
            return [$subject::class, (string) $subject->getKey()];
        }

        return [null, $subject];
    }
}
