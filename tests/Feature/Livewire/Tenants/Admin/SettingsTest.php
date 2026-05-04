<?php

namespace Tests\Feature\Livewire\Tenants\Admin;

use App\Livewire\Tenants\Admin\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.admin.settings');
    }

    /** @test */
    public function it_updates_general_settings()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->set('hospitalName', 'New Hospital Name')
            ->set('hospitalAddress', '123 New Street')
            ->set('hospitalEmail', 'admin@newhospital.com')
            ->call('saveGeneralSettings')
            ->assertHasNoErrors()
            ->assertSee('General Settings Updated!');
    }

    /** @test */
    public function validation_rules_are_enforced_for_general_settings()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->set('hospitalName', '')
            ->set('hospitalEmail', 'not-an-email')
            ->call('saveGeneralSettings')
            ->assertHasErrors(['hospitalName' => 'required', 'hospitalEmail' => 'email']);
    }

    /** @test */
    public function it_updates_hospital_logo()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Storage::fake('s3');
        $logo = UploadedFile::fake()->image('logo.jpg');

        Livewire::test(Settings::class)
            ->set('hospitalLogo', $logo)
            ->call('saveGeneralSettings')
            ->assertHasNoErrors()
            ->assertSee('General Settings Updated!');
    }

    /** @test */
    public function it_creates_new_department()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->call('openModal', 'department', 'create')
            ->set('form.name', 'Cardiology')
            ->set('form.description', 'Heart related diseases')
            ->call('saveForm')
            ->assertHasNoErrors()
            ->assertSee('department Saved!');
    }

    /** @test */
    public function it_updates_existing_department()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        $department = \App\Models\Department::factory()->create(['name' => 'Old Department']);

        Livewire::test(Settings::class)
            ->call('openModal', 'department', 'edit', $department->id)
            ->set('form.name', 'Updated Department')
            ->call('saveForm')
            ->assertHasNoErrors()
            ->assertSee('department Saved!');
    }

    /** @test */
    public function it_deletes_department()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        $department = \App\Models\Department::factory()->create(['name' => 'Department to Delete']);

        Livewire::test(Settings::class)
            ->call('openModal', 'department', 'delete', $department->id)
            ->call('confirmDelete')
            ->assertHasNoErrors()
            ->assertSee('department Deleted!');
    }

    /** @test */
    public function it_creates_new_ward()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        $department = \App\Models\Department::factory()->create();

        Livewire::test(Settings::class)
            ->call('openModal', 'ward', 'create')
            ->set('form.name', 'General Ward')
            ->set('form.ward_number', 'W001')
            ->set('form.department_id', $department->id)
            ->call('saveForm')
            ->assertHasNoErrors()
            ->assertSee('ward Saved!');
    }

    /** @test */
    public function it_creates_new_bed_type()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->call('openModal', 'bed-type', 'create')
            ->set('form.name', 'ICU Bed')
            ->set('form.price_per_day', '500.00')
            ->set('form.description', 'Intensive Care Unit bed')
            ->call('saveForm')
            ->assertHasNoErrors()
            ->assertSee('bed-type Saved!');
    }

    /** @test */
    public function it_creates_new_supply()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->call('openModal', 'supply', 'create')
            ->set('form.name', 'Syringe')
            ->set('form.unit_of_measure', 'pcs')
            ->set('form.current_stock', 100)
            ->set('form.min_stock_level', 20)
            ->call('saveForm')
            ->assertHasNoErrors()
            ->assertSee('supply Saved!');
    }

    /** @test */
    public function it_search_departments()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        \App\Models\Department::factory()->create(['name' => 'Cardiology']);
        \App\Models\Department::factory()->create(['name' => 'Neurology']);
        \App\Models\Department::factory()->create(['name' => 'Pediatrics']);

        Livewire::test(Settings::class)
            ->set('searchDepartment', 'card')
            ->assertSee('Cardiology')
            ->assertDontSee('Neurology')
            ->assertDontSee('Pediatrics');
    }
}
