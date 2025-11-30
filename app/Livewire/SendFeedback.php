<?php

namespace App\Livewire;

use App\Models\Feedback;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class SendFeedback extends Component
{
    use WithFileUploads;

    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    #[Rule('required|in:issue,suggestion,general,other,complaint')]
    public string $category = 'issue';

    #[Rule('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Rule('nullable|max:100')]
    public string $department = '';

    #[Rule('required|min:10')]
    public string $message = '';

    // Livewire 3 handles temporary uploads automatically
    #[Rule('nullable|array|max:5')]
    public $attachments = [];

    public bool $submitted = false;

    /**
     * The mount method to prepare data on component load.
     */
    public function mount()
    {
        // No dynamic data is needed for this simple form,
        // but this method is kept for future expansion.
    }

    /**
     * Submits the feedback form.
     */
    public function submit()
    {
        // Validate all form inputs
        $this->validate();

        // Get the authenticated tenant (hospital)
        $tenant = Tenant::find(Auth::user()->tenant_id);

        if (! $tenant) {
            session()->flash('error', 'Could not find your hospital profile. Please log in again.');

            return;
        }

        // Create a new Feedback record using the `create` method,
        // which is a more concise and secure way to handle mass assignment.
        $feedback = Feedback::create([
            'tenant_id' => $tenant->id,
            'subject' => $this->subject,
            'category' => $this->category,
            'priority' => $this->priority,
            'department' => $this->department,
            'message' => $this->message,
        ]);

        // Note: File uploads are a bit more complex. You would typically save them
        // to a storage disk and then store the path in the database.
        // For this example, we'll assume a file system path field on the Feedback model.
        // if (!empty($this->attachments)) {
        //     foreach ($this->attachments as $attachment) {
        //         $path = $attachment->store('feedback-attachments');
        //         // Save $path to your database
        //     }
        // }

        // Dispatch a notification or event to the landlord
        $this->dispatch('feedback-submitted', $feedback->id);

        // Set the state to "submitted" for a success message
        $this->submitted = true;

        // Reset the form fields for a new submission
        $this->reset(['subject', 'category', 'priority', 'department', 'message', 'attachments']);

        // Dispatch a toast notification
        $this->dispatch('notify', type: 'success', message: 'Thank you! Your feedback has been sent.');
    }

    /**
     * Renders the Livewire component view.
     */
    public function render()
    {
        return view('livewire.send-feedback');
    }
}
