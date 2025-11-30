<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.lab-technician')]
class Feedbacks extends Component
{
    use WithPagination;

    // Modal state
    public $showModal = false;

    public $modalFeedback = null; // instance of FeedBack or null

    public $modalTitle = 'Feedback Details';

    // reply draft bound to textarea
    public $replyDraft = '';

    protected $rules = [
        'replyDraft' => 'nullable|string|max:5000',
    ];

    /**
     * Load a feedback for modal view and initialize draft state.
     */
    public function showFeedback($id)
    {
        $this->modalFeedback = FeedBack::where('id', $id)
            ->first();

        if (! $this->modalFeedback) {
            session()->flash('error', 'Feedback not found.');

            return;
        }

        // initialize modal fields
        $this->modalTitle = $this->modalFeedback->subject ?? 'Feedback Details';
        $this->replyDraft = $this->modalFeedback->response_draft ?? '';

        $this->showModal = true;

        // useful for front-end (Alpine) to focus reply field
        $this->dispatch('modal-opened', ['id' => $id]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalFeedback = null;
        $this->replyDraft = '';
    }

    public function render()
    {

        // build query for top-level feedbacks only if you want (here we show all for the user)
        $query = FeedBack::query()
            ->orderBy('created_at', 'desc');

        // If you only want feedbacks created by current user:
        $query->where('user_id', Auth::id());

        $feedbacks = $query->paginate(10);

        return view('livewire.tenants.lab-technician.feedbacks', [
            'feedbacks' => $feedbacks,
        ]);
    }
}
