<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Platform\PlatformAuditService;
use App\Services\Platform\PlatformSettingsService;
use App\Support\PlatformContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform-wide settings: currency, trial defaults, support contacts and the
 * notification transport switches.
 */
class PlatformSettingsController extends Controller
{
    public function __construct(
        private readonly PlatformSettingsService $settings,
        private readonly PlatformAuditService $audit,
    ) {}

    public function edit(): Response
    {
        return Inertia::render('Platform/Settings', [
            'settings' => PlatformContext::settings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform_name' => ['required', 'string', 'max:120'],
            'platform_tagline' => ['nullable', 'string', 'max:160'],
            'currency' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:8'],
            'default_locale' => ['required', 'in:en,fr'],
            'supported_locales' => ['array', 'min:1'],
            'supported_locales.*' => ['in:en,fr'],
            'trial_days' => ['required', 'integer', 'min:0', 'max:365'],
            'grace_days' => ['required', 'integer', 'min:0', 'max:90'],
            'support_email' => ['required', 'email', 'max:160'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'realtime_enabled' => ['boolean'],
            'email_notifications_enabled' => ['boolean'],
            'maintenance_mode' => ['boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        $this->settings->update($validated);

        $this->audit->log(
            action: 'platform.settings_updated',
            description: 'Platform settings updated',
            properties: $validated,
        );

        return redirect()->back()->with('success', 'Platform settings saved.');
    }
}
