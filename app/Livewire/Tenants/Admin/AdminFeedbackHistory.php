<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\FeedBack;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class AdminFeedbackHistory extends Component
{
    // Livewire trait to handle pagination functionality
    use WithPagination;

    // --- Modal State Properties ---

    /**
     * Controls the visibility of the feedback details modal.
     */
    public bool $showModal = false;

    /**
     * The currently selected FeedBack model instance for display in the modal.
     */
    public ?FeedBack $modalFeedback = null;

    /**
     * The title displayed at the top of the details modal.
     */
    public string $modalTitle = 'Feedback Details';

    /**
     * The administrator's reply draft, bound to the textarea in the modal.
     */
    public string $replyDraft = '';

    /**
     * Flag to prevent double submission during publish/save actions (optional but good practice).
     */
    public bool $publishing = false;

    // --- Validation Rules ---

    protected array $rules = [
        'replyDraft' => 'required|string|max:1000',
    ];

    // --- Modal Control Methods ---

    /**
     * Loads a specific feedback record, initializes the modal state, and opens the modal.
     *
     * @param  int  $id  The ID of the FeedBack record to display.
     */
    public function showFeedback(int $id): void
    {
        // Fetch the feedback record
        $feedback = FeedBack::query()->where('id', $id)->first();

        // Handle case where feedback is not found
        if (! $feedback) {
            session()->flash('error', 'Feedback not found.');

            return;
        }

        /** @var \App\Models\FeedBack $feedback */
        $this->modalFeedback = $feedback;

        // Initialize modal fields using the fetched data
        $this->modalTitle = $this->modalFeedback->subject ?? 'Feedback Details';
        // Use the saved 'response_draft' if it exists, otherwise use 'response' (if you store a final reply there)
        $this->replyDraft = $this->modalFeedback->response_draft ?? $this->modalFeedback->response ?? '';

        $this->showModal = true;

        // Dispatch event to the front-end (Alpine/JS) for custom actions (e.g., focusing a field)
        $this->dispatch('modal-opened', ['id' => $id]);
    }

    /**
     * Closes the feedback details modal and resets its state variables.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->modalFeedback = null;
        $this->replyDraft = '';
        $this->resetValidation();
    }

    // --- Action Methods ---

    /**
     * Saves the administrator's reply draft to the selected feedback record.
     */
    public function publishReply(): void
    {
        $this->validate();

        if (is_null($this->modalFeedback)) {
            session()->flash('error', 'Cannot publish reply: No feedback selected.');
            $this->closeModal();

            return;
        }

        $this->publishing = true;

        try {
            // Update the response and draft fields
            $this->modalFeedback->update([
                // You might choose to save the reply to 'response_draft' first
                'response_draft' => $this->replyDraft,
                // If you want to mark it as published/final, you might set a 'status' field here
                'response' => $this->replyDraft, // For simplicity, setting 'response' as the final reply
                'responded_at' => now(),
            ]);

            session()->flash('success', 'Reply successfully published and saved.');
            $this->closeModal();
            $this->dispatch('refreshList'); // Refresh the main list to update statuses/dates

        } catch (Exception $e) {
            session()->flash('error', 'Error publishing reply: '.$e->getMessage());
        } finally {
            $this->publishing = false;
        }
    }

    // --- Render Method ---

    /**
     * Renders the view and supplies the paginated list of feedback records.
     */
    public function render(): View
    {
        $query = FeedBack::query()

            // Order by creation date descending (most recent first)
            ->orderBy('created_at', 'desc');

        $query->where('user_id', Auth::id());

        $feedbacks = $query->paginate(10);

        return \view('livewire.tenants.admin.admin-feedback-history', [
            'feedbacks' => $feedbacks,
        ]);
    }
}
