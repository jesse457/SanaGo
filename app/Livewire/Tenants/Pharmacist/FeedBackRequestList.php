<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.pharmacist')]
class FeedBackRequestList extends Component
{
    use WithPagination;

    // Modal state
    public bool $showModal = false;

    public ?FeedBack $modalFeedback = null; // instance of FeedBack or null

    public string $modalTitle = 'Feedback Details'; // We should probably translate this in mount or leave as is if it's a default, but let's translate usage

    // reply draft bound to textarea
    public string $replyDraft = '';

    /**
     * Load a feedback for modal view and initialize draft state.
     */
    public function showFeedback($id): void
    {
        $tenantId = tenant('id') ?? optional(Auth::user())->tenant_id;

        $this->modalFeedback = FeedBack::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->first();

        if (! $this->modalFeedback) {
            session()->flash('error', __('Feedback not found.'));

            return;
        }

        // initialize modal fields
        $this->modalTitle = $this->modalFeedback->subject ?? __('Feedback Details');
        $this->replyDraft = $this->modalFeedback->response_draft ?? '';

        $this->showModal = true;

        // useful for front-end (Alpine) to focus reply field
        $this->dispatch('modal-opened', ['id' => $id]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->modalFeedback = null;
        $this->replyDraft = '';
    }

    /**
     * Renders the view with a paginated and filtered list of feedback requests.
     */
    public function render(): \Illuminate\View\View
    {
        $query = FeedBack::query();
        $query->where('user_id', Auth::id());

        $feedbacks = $query->paginate(10);

        return view('livewire.tenants.pharmacist.feed-back-request-list', [
            'feedbacks' => $feedbacks,
        ]);
    }
}
