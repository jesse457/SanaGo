<?php

namespace App\Livewire;

use App\Models\DemoRequest;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BookDemo extends Component
{
    #[Validate('required|min:3', as: 'Nom Complet')]
    public $full_name;

    #[Validate('required|email', as: 'Email Professional')]
    public $email;

    #[Validate('required|min:9', as: 'Numéro de Téléphone')]
    public $phone_number;

    #[Validate('required', as: 'Nom de la Formation Sanitaire')]
    public $facility_name;

    #[Validate('required')]
    public $facility_type = 'Private Clinic';

    #[Validate('required')]
    public $region = 'Center';

    public $job_title;

    public $has_whatsapp = true;

    public $success = false;

    public function submit()
    {
        $this->validate();

        DemoRequest::create([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'has_whatsapp' => $this->has_whatsapp,
            'facility_name' => $this->facility_name,
            'facility_type' => $this->facility_type,
            'region' => $this->region,
            'job_title' => $this->job_title,
        ]);
        LivewireAlert::title('Demo request submitted successfully!')->success()->show();
        $this->success = true;
        $this->reset(['full_name', 'email', 'phone_number', 'facility_name', 'job_title']);
    }

    public function render()
    {
        return view('livewire.book-demo');
    }
}
