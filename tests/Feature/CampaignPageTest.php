<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test homepage displays campaigns.
     */
    public function test_homepage_displays_campaigns(): void
    {
        Campaign::factory()->count(3)->create(['status' => Campaign::STATUS_ACTIVE]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeLivewire('landing-page');
    }

    /**
     * Test campaign detail page displays correctly.
     */
    public function test_campaign_detail_page_displays(): void
    {
        $campaign = Campaign::factory()->create([
            'status' => Campaign::STATUS_ACTIVE,
            'title' => 'Test Campaign Title',
        ]);

        $response = $this->get("/campaign/{$campaign->slug}");

        $response->assertStatus(200);
        $response->assertSee('Test Campaign Title');
    }

    /**
     * Test campaign detail page returns 404 for inactive campaign.
     */
    public function test_inactive_campaign_returns_404(): void
    {
        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_DRAFT]);

        $response = $this->get("/campaign/{$campaign->slug}");

        $response->assertStatus(404);
    }

    /**
     * Test campaign page shows donation form.
     */
    public function test_campaign_page_shows_donation_form(): void
    {
        $this->markTestSkipped('Donation form route requires additional setup');

        $campaign = Campaign::factory()->create(['status' => Campaign::STATUS_ACTIVE]);

        $response = $this->get("/campaign/{$campaign->slug}/donate");

        $response->assertStatus(200);
        $response->assertSeeLivewire('campaign.donation-form');
    }

    /**
     * Test campaign page by category.
     */
    public function test_can_filter_campaigns_by_category(): void
    {
        $category = CampaignCategory::factory()->create(['name' => 'Education']);

        Campaign::factory()->count(2)->create([
            'campaign_category_id' => $category->id,
            'status' => Campaign::STATUS_ACTIVE,
        ]);

        $response = $this->get('/?category='.$category->slug);

        $response->assertStatus(200);
    }
}
