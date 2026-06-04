<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['en', 'fr'];

        // Priority: 1) Session, 2) User preference, 3) Browser Accept-Language, 4) Config default
        $locale = session('locale')
            ?? $request->user()?->locale
            ?? $request->getPreferredLanguage($allowedLocales)
            ?? config('app.locale', 'en');

        if (! in_array($locale, $allowedLocales)) {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);
        session()->put('locale', $locale);

        return $next($request);
    }
}
