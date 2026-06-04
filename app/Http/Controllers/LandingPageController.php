<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemoRequest;
use App\Models\DemoRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    /**
     * Homepage — marketing landing page.
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'canLogin' => true,
            'canRegister' => true,
        ]);
    }

    /**
     * Detailed features page.
     */
    public function features(): Response
    {
        return Inertia::render('Features');
    }

    /**
     * Pricing page.
     */
    public function pricing(): Response
    {
        return Inertia::render('Pricing');
    }

    /**
     * About page.
     */
    public function about(): Response
    {
        return Inertia::render('About');
    }

    /**
     * Contact / demo request form.
     */
    public function contact(): Response
    {
        return Inertia::render('Contact');
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
