<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class Feedbacks
 *
 * Displays a simple paginated list of feedback history for the logged-in Doctor.
 */
#[Layout('components.layouts.doctor')]
class Feedbacks extends Component
{
    use WithPagination;

    /**
     * Controls the visibility of the details modal.
     */
    public bool $showModal = false;

    /**
     * The specific feedback record currently being viewed in the modal.
     */
    public ?FeedBack $modalFeedback = null;

    /**
     * Load a specific feedback record into the modal.
     *
     * @param  int  $id  The ID of the feedback to view.
     */
    public function showFeedback(int $id): void
    {
        // Fetch the feedback ensuring it belongs to the current user
        $this->modalFeedback = FeedBack::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($this->modalFeedback) {
            $this->showModal = true;
        }
    }

    /**
     * Close the modal and clear the selected data.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->modalFeedback = null;
    }

    /**
     * Renders the feedback list view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // specific user's feedback, ordered by newest first
        $feedbacks = FeedBack::query()
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.tenants.doctor.feedbacks', [
            'feedbacks' => $feedbacks,
        ]);
    }
}
