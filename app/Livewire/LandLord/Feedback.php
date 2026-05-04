<?php

namespace App\Livewire\LandLord;

use App\Models\FeedBack as FeedbackModel;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Use the landlord layout for this Livewire component
#[Layout('components.layouts.landlord')]
class Feedback extends Component
{
    use WithPagination;

    /**
     * Mount the component and fetch the data.
     */
    public function mount()
    {
        // Fetch all feedbacks and load their related tenant model to display the name.
        // Orders by the latest feedback first.

    }

    public function render()
    {
        $feedbacks = FeedbackModel::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.land-lord.feedback', [
            'feedbacks' => $feedbacks,
        ]);
    }
}
