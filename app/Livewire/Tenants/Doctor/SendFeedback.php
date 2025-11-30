<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

/**
 * Class SendFeedback
 *
 * Handles the submission of feedback from a Doctor (Tenant) to the system admins.
 *
 * @package App\Livewire\Tenants\Doctor
 */
#[Layout('components.layouts.doctor')]
class SendFeedback extends Component
{
    /**
     * The subject/summary of the feedback.
     *
     * @var string
     */
    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    /**
     * The specific system category the feedback relates to.
     *
     * @var string
     */
    #[Rule('required|in:dashboard,appointments,consultation,test-request,view-patient-info,view-lab-test-and-prescription,profile,patient')]
    public string $category = '';


    /**
     * The main content of the feedback.
     *
     * @var string
     */
    #[Rule('required|string|min:10')]
    public string $message = '';

    /**
     * Handles form submission to create a new feedback record.
     *
     * Validates inputs, creates the record in the database,
     * resets the form, and triggers a success notification.
     *
     * @return void
     */
    public function submit(): void
    {
        // 1. Validate form inputs based on #[Rule] attributes
        $this->validate();

        // 2. Retrieve current user context
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // 3. Create the feedback record
        FeedBack::create([
            'user_id'    => $user->id,
            'subject'    => $this->subject,
            'category'   => $this->category,
            'message'    => $this->message,
            'status'     => 'pending',
        ]);

        // 4. Reset form fields to their default state
        $this->clearData();

        // 5. Display success notification
        LivewireAlert::title('Feedback Received')
            ->success()
            ->text('Thank you! We will review your feedback shortly.')
            ->show();
    }

    /**
     * Resets the form properties to their initial state.
     *
     * This method is linked to the "Start Over" button in the view.
     *
     * @return void
     */
    public function clearData(): void
    {
        $this->reset(['subject', 'category', 'message']);
    }

    /**
     * Renders the Livewire view component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.tenants.doctor.send-feedback');
    }
}
