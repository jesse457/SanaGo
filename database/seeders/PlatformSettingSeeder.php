<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use App\Support\PlatformContext;
use Illuminate\Database\Seeder;

/**
 * Writes the default platform settings so the panel has real values to edit.
 * Idempotent: existing values are never overwritten.
 */
class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PlatformContext::settings() as $key => $value) {
            PlatformSetting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        PlatformContext::flush();
    }
}
