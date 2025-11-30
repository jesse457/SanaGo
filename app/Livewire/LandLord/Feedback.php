<?php

namespace App\Livewire\LandLord;

use App\Models\FeedBack as FeedbackModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

// Use the landlord layout for this Livewire component
#[Layout('components.layouts.landlord')]
class Feedback extends Component
{
    /**
     * The feedbacks to be displayed in the table.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $feedbacks;

    /**
     * Mount the component and fetch the data.
     */
    public function mount()
    {
        // Fetch all feedbacks and load their related tenant model to display the name.
        // Orders by the latest feedback first.
        $this->feedbacks = FeedbackModel::orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.land-lord.feedback');
    }
}
