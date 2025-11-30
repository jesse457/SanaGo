<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\FeedBack;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class ReceptionistFeedBack extends Component
{
    // These properties are linked to the form inputs
    #[Rule('required|min:3|max:150')]
    public string $subject = '';

    #[Rule('required|in:dashboard,appointments,book-appointments,patients,register-patient,patient-admission,view-patient-admission,admit-patient')]
    public string $category = 'dashboard';

    #[Rule('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Rule('nullable|max:100')]
    public string $department = '';

    #[Rule('required|min:10')]
    public string $message = '';

    public bool $submitted = false;

    // Use a mount method to get dynamic data if needed
    public function mount()
    {
        // Example: You could pre-fill the department if the user is in a specific role
        // For now, it's left open for the user to specify.
    }

    public function submit()
    {
        // Validate the form data
        $this->validate();

        $feedback = FeedBack::create([
            'subject' => $this->subject,
            'category' => $this->category,
            'priority' => $this->priority,
            'department' => $this->department,
            'message' => $this->message,
            'user_id' => Auth::id(),
        ]);

        LivewireAlert::title('Success')
            ->text('Thank you! Your feedback has been sent.')
            ->success()
            ->show();

        // Reset the form fields for a new submission
        $this->reset(['subject', 'category', 'priority', 'department', 'message']);
 }

    public function render()
    {
        return view('livewire.tenants.receptionist.receptionist-feed-back');
    }
}
