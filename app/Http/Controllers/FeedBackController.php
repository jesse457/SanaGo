<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use App\Services\System\CommunicationService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedBackController extends Controller
{
    /**
     * Display a listing of the feedback.
     */
    public function index()
    {
        return Inertia::render('Feedback/Index', [
            'feedbacks' => Feedback::where('user_id', Auth::id())
                ->with('responder:id,name') // Load admin who replied
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new feedback.
     */
    public function create()
    {
        return Inertia::render('Feedback/Create');
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(FeedbackRequest $request, CommunicationService $service)
    {
        $service->submitFeedback($request->validated());

        return redirect()->route('feedback.index')
            ->with('success', 'Your feedback has been submitted successfully.');
    }
}
