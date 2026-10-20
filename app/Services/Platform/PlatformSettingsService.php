<?php

declare(strict_types=1);

namespace App\Services\Platform;

use App\Models\PlatformSetting;
use App\Support\PlatformContext;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Reads and writes platform-wide settings.
 */
class PlatformSettingsService
{
    /**
     * Persist a map of settings and refresh the shared context cache.
     *
     * @param  array<string, mixed>  $settings
     */
    public function update(array $settings): void
    {
        foreach ($settings as $key => $value) {
            PlatformSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        PlatformContext::flush();
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return PlatformContext::settings();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return PlatformContext::setting($key, $default);
    }

    /**
     * Whether the settings table exists (fresh installs/tenant-only deploys).
     */
    public function isAvailable(): bool
    {
        try {
            return Schema::connection('central')->hasTable('platform_settings');
        } catch (Throwable) {
            return false;
        }
    }
}
