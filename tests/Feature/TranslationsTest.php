<?php

declare(strict_types=1);

use App\Enums\Gender;
use App\Enums\PlanFeature;
use App\Enums\ServiceCategory;
use Illuminate\Support\Facades\Session;

/**
 * Every `t('some.dotted.key')` the React app asks for.
 *
 * Only dotted keys are collected: a single-word or sentence key is its own
 * English label, so a missing entry renders correctly by definition.
 *
 * @return list<string>
 */
function frontendTranslationKeys(): array
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(resource_path('js')),
    );

    $keys = [];

    foreach ($files as $file) {
        if (! $file->isFile() || ! preg_match('/\.tsx?$/', $file->getFilename())) {
            continue;
        }

        $contents = File::get($file->getPathname());

        if (preg_match_all('/\bt\(\s*([\'"])([^\'"\n]+)\1/', $contents, $matches) === 0) {
            continue;
        }

        foreach ($matches[2] as $key) {
            if (str_contains($key, '.') && ! str_contains($key, ' ')) {
                $keys[$key] = true;
            }
        }
    }

    return array_keys($keys);
}

/**
 * The dictionary a locale receives: English, overlaid with the locale's own
 * copy. Mirrors `HandleInertiaRequests::translations()`.
 *
 * @return array<string, mixed>
 */
function localePayload(string $locale): array
{
    $english = json_decode(File::get(lang_path('en.json')), true) ?? [];
    $localised = json_decode(File::get(lang_path("{$locale}.json")), true) ?? [];

    return array_replace_recursive($english, $localised);
}

/**
 * Keys built at runtime (`` t(`prefix_${value}`) ``), which a static scan
 * cannot see. Each entry is a prefix plus every value the UI can pass.
 *
 * @return list<string>
 */
function dynamicTranslationKeys(): array
{
    $keys = [];

    foreach (['all', 'salaries', 'supplies', 'utilities', 'maintenance', 'equipment', 'pharmaceuticals', 'administrative', 'other'] as $category) {
        $keys[] = "finance.expenditures.filter_{$category}";
    }

    foreach (['all', 'scheduled', 'in_progress', 'completed', 'cancelled'] as $status) {
        $keys[] = "surgeries.index.filter_{$status}";
    }

    foreach (['all', 'draft', 'pending', 'paid', 'overdue', 'cancelled'] as $status) {
        $keys[] = "finance.invoices.index.status_{$status}";
    }

    foreach (['all', ...ServiceCategory::values()] as $category) {
        $keys[] = "finance.services.index.category_{$category}";
    }

    foreach (['scheduled', 'in_consultation', 'pending', 'completed', 'cancelled'] as $status) {
        $keys[] = "page.appointment_calendar.status.{$status}";
    }

    foreach (PlanFeature::values() as $feature) {
        $keys[] = "page.billing_page.features.{$feature}";
    }

    foreach (['admin', 'doctor', 'nurse', 'lab_tech', 'pharmacist'] as $role) {
        foreach ([1, 2, 3, 4] as $task) {
            $keys[] = "marketing.roles.{$role}.tasks_{$task}";
        }
    }

    foreach (['all', 'unread'] as $filter) {
        $keys[] = "common.{$filter}";
    }

    foreach (Gender::cases() as $gender) {
        $keys[] = "common.{$gender->value}";
    }

    return $keys;
}

it('resolves every dotted key the frontend uses, in every locale', function () {
    $keys = [...frontendTranslationKeys(), ...dynamicTranslationKeys()];

    expect($keys)->not->toBeEmpty();

    foreach (['en', 'fr'] as $locale) {
        $payload = localePayload($locale);
        $missing = [];
        $notStrings = [];

        foreach ($keys as $key) {
            $value = data_get($payload, $key);

            if ($value === null) {
                $missing[] = $key;
            } elseif (! is_string($value)) {
                $notStrings[] = $key;
            }
        }

        expect($missing)->toBe([], "{$locale}.json is missing: ".implode(', ', $missing));
        expect($notStrings)->toBe([], "{$locale}.json resolves these to an object: ".implode(', ', $notStrings));
    }
});

it('never lets a translation key resolve to a namespace object', function () {
    foreach (['en', 'fr'] as $locale) {
        $payload = localePayload($locale);

        // `finance` used to be the label "Finance" in English and a namespace
        // in French, so `t('finance')` rendered an object and crashed React.
        expect(data_get($payload, 'finance.breadcrumb'))->toBeString();

        foreach (frontendTranslationKeys() as $key) {
            expect(data_get($payload, $key))->not->toBeArray();
        }
    }
});

it('falls back to English copy for keys a locale has not translated yet', function () {
    Session::put('locale', 'fr');

    $response = $this->get('/login');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('locale', 'fr')
            ->where(
                'translations.page.patients_create.basic_information',
                'Basic information',
            )
            ->where('translations.finance.breadcrumb', 'Finances')
        );
});
