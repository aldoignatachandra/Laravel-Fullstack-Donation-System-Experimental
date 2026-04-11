<?php

namespace Tests\Unit\Models;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test donation belongs to campaign.
     */
    public function test_donation_belongs_to_campaign(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);

        $this->assertInstanceOf(Campaign::class, $donation->campaign);
        $this->assertEquals($campaign->id, $donation->campaign->id);
    }

    /**
     * Test donation belongs to user.
     */
    public function test_donation_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $donation = Donation::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $donation->user);
        $this->assertEquals($user->id, $donation->user->id);
    }

    /**
     * Test donation status constants.
     */
    public function test_donation_status_constants(): void
    {
        $this->assertEquals(0, Donation::STATUS_PENDING);
        $this->assertEquals(1, Donation::STATUS_PAID);
        $this->assertEquals(2, Donation::STATUS_FAILED);
        $this->assertEquals(3, Donation::STATUS_CANCELLED);
    }

    /**
     * Test donation casts amount to decimal.
     */
    public function test_donation_amount_casts_to_decimal(): void
    {
        $donation = Donation::factory()->create(['amount' => 100000.50]);

        // Note: Amount may be returned as string from DB, check value is correct
        $this->assertEquals(100000.50, (float) $donation->amount);
    }

    /**
     * Test donation casts is_anonymous to boolean.
     */
    public function test_donation_is_anonymous_casts_to_boolean(): void
    {
        $donation = Donation::factory()->create(['is_anonymous' => 1]);

        $this->assertIsBool($donation->is_anonymous);
        $this->assertTrue($donation->is_anonymous);
    }

    /**
     * Test donation casts paid_at to datetime.
     */
    public function test_donation_paid_at_casts_to_datetime(): void
    {
        $donation = Donation::factory()->create([
            'status' => Donation::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->assertInstanceOf(\DateTime::class, $donation->paid_at);
    }

    /**
     * Test donation generates order_id.
     */
    public function test_donation_generates_order_id(): void
    {
        $donation = Donation::factory()->create();

        $this->assertNotNull($donation->order_id);
        $this->assertMatchesRegularExpression('/^(DON-|ORD-)/', $donation->order_id);
    }
}
