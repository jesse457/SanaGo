<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\LabTechnician\TestRequest;
use App\Models\User;
use App\Models\LabRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class TestRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'lab-technician']);
        Auth::login($user);

        Livewire::test(TestRequest::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.lab-technician.test-request');
    }

    /** @test */
    public function it_displays_test_request_list()
    {
        $user = User::factory()->create(['role' => 'lab-technician']);
        Auth::login($user);

        // Create test lab requests
        LabRequest::factory()->count(5)->create();

        Livewire::test(TestRequest::class)
            ->assertStatus(200)
            ->assertSee('Test Requests')
            ->assertCount('@test-request-row', 5);
    }

    /** @test */
    public function it_searches_test_requests()
    {
        $user = User::factory()->create(['role' => 'lab-technician']);
        Auth::login($user);

        $patient1 = \App\Models\Patient::factory()->create(['name' => 'John Doe']);
        $patient2 = \App\Models\Patient::factory()->create(['name' => 'Jane Smith']);
        $patient3 = \App\Models\Patient::factory()->create(['name' => 'Mike Johnson']);

        LabRequest::factory()->create(['patient_id' => $patient1->id, 'test_type' => 'Blood Test']);
        LabRequest::factory()->create(['patient_id' => $patient2->id, 'test_type' => 'X-Ray']);
        LabRequest::factory()->create(['patient_id' => $patient3->id, 'test_type' => 'MRI']);

        Livewire::test(TestRequest::class)
            ->set('search', 'john')
            ->assertStatus(200)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith')
            ->assertDontSee('Mike Johnson');
    }

    /** @test */
    public function it_filters_test_requests_by_status()
    {
        $user = User::factory()->create(['role' => 'lab-technician']);
        Auth::login($user);

        LabRequest::factory()->create(['status' => 'pending']);
        LabRequest::factory()->create(['status' => 'in-progress']);
        LabRequest::factory()->create(['status' => 'completed']);

        Livewire::test(TestRequest::class)
            ->set('statusFilter', 'pending')
            ->assertStatus(200)
            ->assertSee('pending')
            ->assertDontSee('in-progress')
            ->assertDontSee('completed');
    }
}
