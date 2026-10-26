<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemoRequest;
use App\Models\DemoRequest;
use App\Support\MarketingContent;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The public sales site.
 *
 * Every page shares one `marketing` prop assembled by {@see MarketingContent},
 * so the plan grid, the capability matrix, the support contacts and the
 * announcement strip are all read from the console's data rather than retyped
 * in React.
 */
class LandingPageController extends Controller
{
    /**
     * Homepage — the product story, the module bento, the live plan shelf and
     * the demo conversion.
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'canLogin' => true,
            'canRegister' => true,
            'marketing' => MarketingContent::toArray(),
        ]);
    }

    /**
     * The complete module catalogue: every capability the platform gates.
     */
    public function features(): Response
    {
        return Inertia::render('Features', [
            'marketing' => MarketingContent::toArray(),
        ]);
    }

    /**
     * Plan comparison, built from the plans an operator published.
     */
    public function pricing(): Response
    {
        return Inertia::render('Pricing', [
            'marketing' => MarketingContent::toArray(),
        ]);
    }

    /**
     * Company story and the principles behind the product.
     */
    public function about(): Response
    {
        return Inertia::render('About', [
            'marketing' => MarketingContent::toArray(),
        ]);
    }

    /**
     * Contact / demo request form.
     */
    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'marketing' => MarketingContent::toArray(),
        ]);
    }

    /**
     * Data-handling notice. The footer links here, so it must exist.
     */
    public function privacy(): Response
    {
        return Inertia::render('Legal/Privacy', [
            'marketing' => MarketingContent::toArray(),
            'updated_at' => now()->startOfMonth()->toDateString(),
        ]);
    }

    /**
     * Service terms.
     */
    public function terms(): Response
    {
        return Inertia::render('Legal/Terms', [
            'marketing' => MarketingContent::toArray(),
            'updated_at' => now()->startOfMonth()->toDateString(),
        ]);
    }

    /**
     * Handle demo request submission.
     */
    public function submitDemoRequest(StoreDemoRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['status'] = 'new';

        DemoRequest::create($validated);

        return redirect()->back()->with('success', 'Thank you for your interest! We will be in touch shortly.');
    }
}
