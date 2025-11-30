<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.nurse')]
class SendFeedback extends Component
{
      use WithFileUploads;

    // These properties are linked to the form inputs
    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    #[Rule('required|in:issue,suggestion,general,other')]
    public string $category = 'issue';

    #[Rule('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Rule('nullable|max:100')]
    public string $department = '';

    #[Rule('required|min:10')]
    public string $message = '';



    public bool $submitted = false;

    /**
     * Initializes component state.
     */
    public function mount(): void
    {
        // Set the default priority to 'normal' on page load.
        $this->priority = 'normal';
    }

    /**
     * Removes a single attachment from the attachments array.
     *
     * @param  int  $index  The index of the attachment to remove.
     */
    // public function removeAttachment(int $index): void
    // {
    //     // Ensure the index is valid
    //     if (isset($this->attachments[$index])) {
    //         // Remove the file from the temporary storage directory
    //         Storage::disk('local')->delete($this->attachments[$index]->getRealPath());

    //         // Unset the attachment from the array
    //         unset($this->attachments[$index]);

    //         // Re-index the array to prevent issues
    //         $this->attachments = array_values($this->attachments);
    //     }
    // }

    /**
     * Handles form submission to create a new feedback record.
     */
    public function submit(): void
    {
        // Step 1: Validate all form inputs.
        $this->validate();

        // Step 2: Get the authenticated user's ID and tenant ID.
        $userId = Auth::id();

        // Step 3: Handle file uploads and store paths in an array.
        // $attachmentPaths = [];
        // foreach ($this->attachments as $attachment) {
        //     $path = $attachment->store('feedback-attachments', 'public');
        //     $attachmentPaths[] = $path;
        // }

        // Step 4: Create a new feedback record using a single creation method for efficiency.
        FeedBack::create([
            'user_id' => $userId, // This assumes a `user_id` field in your migration
            'subject' => $this->subject,
            'category' => $this->category,
            'priority' => $this->priority,
            'department' => $this->department,
            'message' => $this->message,
            // 'attachments' => json_encode($attachmentPaths), // Store file paths as a JSON string
            'status' => 'pending', // Set a default status
        ]);

        // Step 5: Reset the form for a fresh submission.
        $this->reset(['subject', 'category', 'priority', 'department', 'message']);

        // Step 6: Set the `submitted` flag and dispatch a success message.
        $this->submitted = true;
        session()->flash('success', 'Thank you! Your feedback has been sent successfully.');
    }

    /**
     * Renders the Livewire view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.tenants.nurse.send-feedback');
    }
}
