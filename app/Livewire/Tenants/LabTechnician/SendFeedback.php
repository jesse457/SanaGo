<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Layout('components.layouts.lab-technician')]
class SendFeedback extends Component
{
    // These properties are linked to the form inputs
    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    #[Rule('required|in:dashboard,test-request,enter-result,manage-lab-test,lab-results,create-new-lab-test')]
    public string $category = 'dashboard';

    public function clearData(): void
    {
        $this->reset(['subject', 'category',  'message']);
    }

    /**
     * Handles form submission to create a new feedback record.
     */
    public function submit(): RedirectResponse
    {
        // Step 1: Validate all form inputs.
        $this->validate();

        // Step 2: Get the authenticated user's ID and tenant ID.
        $userId = Auth::id();

        try {
            // Step 4: Create a new feedback record using a single creation method for efficiency.
            FeedBack::create([
                'user_id' => $userId, // This assumes a `user_id` field in your migration
                'subject' => $this->subject,
                'category' => $this->category,
                'message' => $this->message,
                // 'attachments' => json_encode($attachmentPaths), // Store file paths as a JSON string
                'status' => 'pending', // Set a default status
            ]);

            // Step 5: Reset the form for a fresh submission.
            $this->reset(['subject', 'category', 'message']);

            LivewireAlert::title('Success')->text('Thank you! Your feedback has been sent successfully.')->success()->show();

            return redirect()->route('lab-technician.lab-results');
            // code...
        } catch (\Exception $e) {
            Log::error('An Error Occured when saving Feedback', ['Error' => $e->getMessage(), 'Trace' => $e->getTrace()]);
        }
    }

    /**
     * Renders the Livewire view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.tenants.lab-technician.send-feedback');
    }
}
