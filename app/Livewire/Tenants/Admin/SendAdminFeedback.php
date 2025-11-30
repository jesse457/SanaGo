<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\FeedBack;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads; // Trait for handling temporary and permanent file uploads

// Assigns the admin layout to this component.
#[Layout('components.layouts.admin')]
/**
 * SendAdminFeedback Livewire Component
 *
 * This component provides a form for tenant administrators to submit feedback,
 * bug reports, or feature requests regarding the application.
 */
class SendAdminFeedback extends Component
{
    // Enables file upload capabilities for the component.
    use WithFileUploads;

    // --- Form Properties ---
    // These properties are bound to the form inputs and include Livewire validation rules.

    /** @var string The subject or title of the feedback. */
    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    /** * @var string The application area related to the feedback (e.g., 'dashboard').
     * Must be one of the predefined categories.
     */
    #[Rule('required|in:dashboard,shift-management,create-shifts,revenue-report,setings,user-activities,user-management,create-new-user')]
    public string $category = 'dashboard';

   

    /** * @var string The relevant internal department (optional field).
     */
    #[Rule('nullable|max:100')]
    public string $department = '';

    /** @var string The detailed message or description of the feedback/issue. */
    #[Rule('required|min:10')]
    public string $message = '';

    // --- Component State ---

    /** @var bool Flag to track if the form has been submitted (currently unused in the logic). */
    public bool $submitted = false;



    /*
    * NOTE: The methods for handling attachments (removeAttachment, attachment processing in submit)
    * are commented out in the provided code, but the structure is retained for reference.
    */

    /**
     * Handles form submission to create a new feedback record.
     *
     * @return void
     */
    public function submit(): void
    {
        // Step 1: Validate all form inputs against the defined rules.
        $this->validate();

        // Step 2: Get the authenticated user's ID and tenant ID for context.
        /** @var int $userId */
        $userId = Auth::id();
        /** @var string|int $tenantId */
        $tenantId = Auth::user()->tenant_id;

        // Step 3: Create a new feedback record in the database.
        // The record is associated with the current tenant and user.
        FeedBack::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'subject' => $this->subject,
            'category' => $this->category,
            'department' => $this->department,
            'message' => $this->message,
            // 'attachments' => json_encode($attachmentPaths), // Path storage is commented out
            'status' => 'pending', // Set the initial status
        ]);

        // Step 4: Reset the form fields to clear inputs after successful submission.
        $this->reset(['subject', 'category', 'department', 'message']);


        // Step 5: Display a success notification to the user.
        LivewireAlert::title('Success')->success()->text('Feed Sent successfully. We will get back to you as soon as possible.')->show();
    }

    /**
     * Renders the Livewire component view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.tenants.admin.send-admin-feedback');
    }
}
