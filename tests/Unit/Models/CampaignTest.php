<?php

namespace Tests\Unit\Models;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test campaign belongs to category.
     */
    public function test_campaign_belongs_to_category(): void
    {
        $category = CampaignCategory::factory()->create();
        $campaign = Campaign::factory()->create(['campaign_category_id' => $category->id]);

        $this->assertInstanceOf(CampaignCategory::class, $campaign->category);
        $this->assertEquals($category->id, $campaign->category->id);
    }

    /**
     * Test campaign belongs to user.
     */
    public function test_campaign_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $campaign->user);
        $this->assertEquals($user->id, $campaign->user->id);
    }

    /**
     * Test campaign has many donations.
     */
    public function test_campaign_has_many_donations(): void
    {
        $campaign = Campaign::factory()->create();
        Donation::factory()->count(3)->create(['campaign_id' => $campaign->id]);

        $this->assertCount(3, $campaign->donations);
        $this->assertInstanceOf(Donation::class, $campaign->donations->first());
    }

    /**
     * Test campaign status constants.
     */
    public function test_campaign_status_constants(): void
    {
        $this->assertEquals(0, Campaign::STATUS_DRAFT);
        $this->assertEquals(1, Campaign::STATUS_ACTIVE);
        $this->assertEquals(2, Campaign::STATUS_PAUSED);
        $this->assertEquals(3, Campaign::STATUS_COMPLETED);
        $this->assertEquals(4, Campaign::STATUS_CANCELLED);
    }

    /**
     * Test campaign calculates total donations correctly.
     */
    public function test_campaign_calculates_total_donations(): void
    {
        $campaign = Campaign::factory()->create();

        // Create paid donations
        Donation::factory()->count(2)->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_PAID,
            'amount' => 50000,
        ]);

        // Create pending donation (should not count)
        Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_PENDING,
            'amount' => 30000,
        ]);

        $this->assertEquals(100000, $campaign->total_donations);
    }
}
