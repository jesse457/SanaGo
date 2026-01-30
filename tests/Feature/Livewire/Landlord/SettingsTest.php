<?php

namespace Tests\Feature\Livewire\Landlord;

use App\Livewire\LandLord\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.land-lord.settings');
    }

    /** @test */
    public function it_updates_general_settings()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->set('siteName', 'New SanaGo')
            ->set('supportEmail', 'support@newexample.com')
            ->set('contactPhone', '+1234567890')
            ->call('saveGeneralSettings')
            ->assertHasNoErrors()
            ->assertSee('Settings updated successfully');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->set('siteName', '')
            ->set('supportEmail', 'not-an-email')
            ->call('saveGeneralSettings')
            ->assertHasErrors(['siteName' => 'required', 'supportEmail' => 'email']);
    }

    /** @test */
    public function it_manages_payment_settings()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->set('stripeApiKey', 'sk_test_12345')
            ->set('stripeSecretKey', 'sk_secret_12345')
            ->call('savePaymentSettings')
            ->assertHasNoErrors()
            ->assertSee('Payment settings updated successfully');
    }

    /** @test */
    public function it_manages_email_settings()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(Settings::class)
            ->set('smtpHost', 'smtp.gmail.com')
            ->set('smtpPort', '587')
            ->set('smtpUsername', 'admin@example.com')
            ->set('smtpPassword', 'password123')
            ->call('saveEmailSettings')
            ->assertHasNoErrors()
            ->assertSee('Email settings updated successfully');
    }
}
