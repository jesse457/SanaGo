<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use App\Support\PlatformContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;
use Throwable;

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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'locale' => app()->getLocale(),
            'translations' => fn () => $this->translations(),
            'flash' => [
                // Landlord controllers historically flashed a bare `flash`
                // key that no component read, so their success messages were
                // silent. Mapping it here avoids touching every controller.
                'success' => fn () => session('success') ?? session('flash'),
                'error' => fn () => session('error'),
                'info' => fn () => session('info'),
            ],
            'tenant' => fn () => $this->tenantPayload(),
            'platform' => fn () => PlatformContext::toArray(),
            'announcements' => fn () => $this->publishedAnnouncements(),
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

    /**
     * Translation dictionary for the active locale.
     *
     * @return array<string, string>
     */
    private function translations(): array
    {
        $path = lang_path(app()->getLocale().'.json');

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }

    /**
     * The active tenant plus its plan, quotas, features and usage.
     *
     * @return array<string, mixed>|null
     */
    private function tenantPayload(): ?array
    {
        $tenant = tenant();

        if ($tenant === null) {
            return null;
        }

        try {
            $context = PlatformContext::forTenant($tenant);

            return [
                'id' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'slug' => $tenant->getTenantKey(),
                'subscription' => $context['subscription'],
                'limits' => $context['limits'],
                'features' => $context['features'],
                'usage' => $context['usage'],
            ];
        } catch (Throwable) {
            // A broken landlord database must never take the hospital app down.
            return [
                'id' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'slug' => $tenant->getTenantKey(),
                'subscription' => null,
                'limits' => [],
                'features' => [],
                'usage' => [],
            ];
        }
    }

    /**
     * Published platform announcements.
     *
     * The announcements table lives on the landlord connection and may be
     * missing (fresh install, tenant-only deployment, minimal test database).
     * A missing table must degrade to "no announcements" instead of throwing
     * inside the shared-prop pipeline — otherwise every page 500s and the
     * exception handler re-enters this middleware, which used to cost 20-40
     * seconds per request before failing.
     *
     * @return array<int, array<string, mixed>>
     */
    private function publishedAnnouncements(): array
    {
        try {
            if (! Schema::connection('central')->hasTable('announcements')) {
                return [];
            }

            return Announcement::query()
                ->where('is_published', true)
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->orderByDesc('published_at')
                ->limit(5)
                ->get()
                ->map(fn (Announcement $announcement) => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'body' => $announcement->body,
                    'type' => $announcement->type,
                    'published_at' => $announcement->published_at?->toISOString(),
                    'expires_at' => $announcement->expires_at?->toISOString(),
                ])
                ->all();
        } catch (Throwable) {
            return [];
        }
    }
}
