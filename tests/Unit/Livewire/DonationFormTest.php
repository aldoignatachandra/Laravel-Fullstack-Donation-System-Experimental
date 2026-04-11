<?php

namespace Tests\Unit\Livewire;

use App\Livewire\Campaign\DonationForm;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DonationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_ACTIVE]);

        Livewire::test(DonationForm::class, ['slug' => $campaign->slug])
            ->assertStatus(200);
    }

    public function test_component_loads_campaign_by_slug(): void
    {
        $campaign = Campaign::factory()->create([
            'status' => Campaign::STATUS_ACTIVE,
            'title' => 'Test Campaign Title',
        ]);

        $component = Livewire::test(DonationForm::class, ['slug' => $campaign->slug]);

        $this->assertEquals($campaign->id, $component->get('campaign')->id);
        $this->assertEquals('Test Campaign Title', $component->get('campaign')->title);
    }

    public function test_can_select_preset_amount(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_ACTIVE]);

        Livewire::test(DonationForm::class, ['slug' => $campaign->slug])
            ->call('selectAmount', 50000)
            ->assertSet('selectedAmount', 50000);
    }

    public function test_can_set_custom_amount(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_ACTIVE]);

        Livewire::test(DonationForm::class, ['slug' => $campaign->slug])
            ->set('customAmount', '75000')
            ->call('setCustomAmount')
            ->assertSet('selectedAmount', 75000);
    }

    public function test_shows_404_for_inactive_campaign(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_DRAFT]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(DonationForm::class, ['slug' => $campaign->slug]);
    }

    public function test_can_toggle_anonymous(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_ACTIVE]);

        Livewire::test(DonationForm::class, ['slug' => $campaign->slug])
            ->set('isAnonymous', true)
            ->assertSet('isAnonymous', true);
    }

    public function test_can_enter_message(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_ACTIVE]);

        Livewire::test(DonationForm::class, ['slug' => $campaign->slug])
            ->set('message', 'Semoga bermanfaat')
            ->assertSet('message', 'Semoga bermanfaat');
    }
}
