<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.nurse')]
class Feedbacks extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = '';
    public $perPage = 10;

    // Modal state
    public $showModal = false;
    public $modalFeedback = null; // instance of FeedBack or null
    public $modalTitle = 'Feedback Details';

    // reply draft bound to textarea
    public $replyDraft = '';

    // small loading flag for publish actions
    public $publishing = false;

    protected $queryString = ['search', 'filterCategory', 'perPage', 'page'];

    protected $listeners = [
        'refreshList' => '$refresh',
    ];

    protected $rules = [
        'replyDraft' => 'nullable|string|max:5000',
    ];

    public function mount()
    {
        $this->perPage = (int) ($this->perPage ?: 10);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function goToSendFeedback()
    {
        // adapt route name to your app; this returns a redirect response Livewire recognizes
        return $this->redirect(route('livewire.tenants.admin.send-admin-feedback'), navigate: true);
    }

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

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'ilike', '%' . $search . '%')
                    ->orWhere('message', 'ilike', '%' . $search . '%')
                    ->orWhere('response', 'ilike', '%' . $search . '%');
            });
        }

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        $feedbacks = $query->paginate((int) $this->perPage);

        return view('livewire.tenants.nurse.feedbacks');
    }
}
