<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $tenant = tenancy()->tenant;
        $locale = app()->getLocale();
        $translations = [];

        $path = lang_path("{$locale}.json");
        if (File::exists($path)) {
            $translations = json_decode(File::get($path), true) ?? [];
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'locale' => $locale,
            'translations' => $translations,
            'flash' => [
                'success' => fn () => session('success'),
                'error' => fn () => session('error'),
            ],
            'tenant' => $tenant ? [
                'id' => $tenant->getTenantKey(),
                'name' => $tenant->getAttribute('data')['name'] ?? $tenant->getTenantKey(),
                'slug' => $tenant->getTenantKey(),
            ] : null,
            'announcements' => fn () => Announcement::where('is_published', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->orderByDesc('published_at')
                ->get()
                ->map(fn (Announcement $a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'body' => $a->body,
                    'type' => $a->type,
                    'published_at' => $a->published_at?->toISOString(),
                    'expires_at' => $a->expires_at?->toISOString(),
                ])
                ->toArray(),
            'auth' => [
                'user' => $request->user() ? array_merge(
                    $request->user()->toArray(),
                    [
                        'roles' => $request->user()->getRoleNames()->values()->toArray(),
                        'permissions' => $request->user()->getAllPermissions()->pluck('name')->values()->toArray(),
                    ]
                ) : null,
            ],
        ];
    }
}
