<?php

namespace App\Livewire\LandLord;

use App\Models\FeedBack;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

// Use the landlord layout for this Livewire component
#[Layout('components.layouts.landlord')]
class RespondFeedback extends Component
{
    // Public property to hold the Feedback model instance
    public Feedback $feedback;

    // Properties for the form
    public string $status;

    public string $response = '';

    public bool $sendEmail = true;

    public bool $sendInApp = true;

    // Use a mount method to get dynamic data if needed
    public function mount(FeedBack $feedback)
    {
        // Use route model binding to automatically get the Feedback model
        $this->feedback = $feedback->load('user'); // Load the related user (tenant)
        $this->status = $feedback->status;

        // If a draft response exists, pre-fill the form
        if ($this->feedback->response_draft) {
            $this->response = $this->feedback->response_draft;
        }
    }

    /**
     * Saves the current response as a draft.
     */
    public function saveDraft()
    {
        // Validate the response to ensure it's not empty
        $this->validate(['response' => 'nullable|max:5000']);

        // Update the draft column in the database
        $this->feedback->response_draft = $this->response;
        $this->feedback->save();

        $this->dispatch('notify', type: 'success', message: 'Draft saved successfully!');
    }

    /**
     * Sends the final response and updates the ticket.
     */
    public function sendResponse()
    {
        // Validate the required fields
        $this->validate([
            'response' => 'required|min:10',
            'status' => 'required|in:open,pending,in_progress,resolved,closed',
        ]);
        // Update the feedback record
        $this->feedback->status = $this->status;
        $this->feedback->response = $this->response;
        // Clear the draft once the final response is sent
        $this->feedback->response_draft = null;
        $this->feedback->save();

        // Implement notification logic
        if ($this->sendInApp) {
            // TODO: Implement logic to send in-app notification to the tenant
            // Example: Notification::make()->title('Response received')->body('Your feedback has been updated.')->sendTo($this->feedback->tenant);
        }

        if ($this->sendEmail) {
            // TODO: Implement logic to send email to the tenant
            // Example: Mail::to($this->feedback->tenant->email)->send(new FeedbackResponseMail($this->feedback));
        }

        LivewireAlert::title('Success')->success()->text('Response Sent successfully')->show();
        // Redirect to the feedbacks page
        $this->redirect(route('landlord.feedbacks'), navigate: true);
    }

    /**
     * Renders the Livewire component view.
     */
    public function render()
    {
        return view('livewire.land-lord.respond-feedback');
    }
}
