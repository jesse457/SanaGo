<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.pharmacist')]
class SubmitFeedBack extends Component
{
    // These properties are linked to the form inputs
    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    #[Rule('required|in:dashboard,dispense-medication,manage-drugs,create-new-drugs,profile')]
    public string $category = 'dashboard';

    #[Rule('nullable|max:100')]
    public string $department = '';

    #[Rule('required|min:10')]
    public string $message = '';

    public bool $submitted = false;

    /**
     * Handles form submission to create a new feedback record.
     */
    public function submit()
    {
        $this->validate();
        try {
            // Step 2: Get the authenticated user's ID and tenant ID.
            $userId = Auth::id();

            // Step 4: Create a new feedback record using a single creation method for efficiency.
            FeedBack::create([
                'user_id' => $userId, // This assumes a `user_id` field in your migration
                'subject' => $this->subject,
                'category' => $this->category,
                'department' => $this->department,
                'message' => $this->message,
                'status' => 'pending', // Set a default status
            ]);

            // Step 5: Reset the form for a fresh submission.
            $this->reset(['subject', 'category', 'department', 'message']);

            LivewireAlert::title(__('pharmacist.create_drugs_component.alert_success'))->success()->text(__('pharmacist.submit_feedback_page.feedback_submitted_successfully'))->show();

            // 9. Redirect to the user management page.
            return redirect()->route('pharmacist.feedbacks');
        } catch (\Exception $e) {
            Log::error('The was a error with '.$e->getMessage());
        }
    }

    /**
     * Renders the Livewire view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.tenants.pharmacist.submit-feed-back');
    }
}
