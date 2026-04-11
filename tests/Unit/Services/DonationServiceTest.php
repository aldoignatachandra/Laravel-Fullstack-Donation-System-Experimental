<?php

namespace Tests\Unit\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationServiceTest extends TestCase
{
    use RefreshDatabase;

    private DonationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DonationService;
    }

    /**
     * Test handleCallback updates donation status to paid on successful payment.
     */
    public function test_handle_callback_updates_donation_to_paid(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_PENDING,
            'order_id' => 'TEST-ORDER-123',
            'amount' => 100000,
            'paid_at' => null,
        ]);

        $payload = [
            'order_id' => 'TEST-ORDER-123',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ];

        $result = $this->service->handleCallback($payload);

        // Service returns Donation object on success
        $this->assertInstanceOf(Donation::class, $result);
        $this->assertEquals(Donation::STATUS_PAID, $result->status);
        $this->assertNotNull($result->paid_at);
    }

    /**
     * Test handleCallback handles capture status for credit card.
     */
    public function test_handle_callback_handles_capture_status(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_PENDING,
            'order_id' => 'TEST-ORDER-456',
            'paid_at' => null,
        ]);

        $payload = [
            'order_id' => 'TEST-ORDER-456',
            'transaction_status' => 'capture',
            'payment_type' => 'credit_card',
        ];

        $result = $this->service->handleCallback($payload);

        $this->assertInstanceOf(Donation::class, $result);
        $this->assertEquals(Donation::STATUS_PAID, $result->status);
        $this->assertNotNull($result->paid_at);
    }

    /**
     * Test handleCallback handles failed payment.
     */
    public function test_handle_callback_handles_failed_payment(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_PENDING,
            'order_id' => 'TEST-ORDER-789',
        ]);

        $payload = [
            'order_id' => 'TEST-ORDER-789',
            'transaction_status' => 'failure',
            'payment_type' => 'bank_transfer',
        ];

        $result = $this->service->handleCallback($payload);

        $this->assertInstanceOf(Donation::class, $result);
        $this->assertEquals(Donation::STATUS_FAILED, $result->status);
    }

    /**
     * Test handleCallback returns null for non-existent order.
     */
    public function test_handle_callback_returns_null_for_invalid_order(): void
    {
        $payload = [
            'order_id' => 'NON-EXISTENT-ORDER',
            'transaction_status' => 'settlement',
        ];

        $result = $this->service->handleCallback($payload);

        $this->assertNull($result);
    }

    /**
     * Test donation constants are defined correctly.
     */
    public function test_donation_service_constants(): void
    {
        $this->assertEquals(10000, DonationService::MIN_DONATION_AMOUNT);
        $this->assertEquals(100000000, DonationService::MAX_DONATION_AMOUNT);
        $this->assertEquals(100, DonationService::MAX_DAILY_DONATIONS_PER_USER);
        $this->assertEquals(5000000, DonationService::MAX_DAILY_AMOUNT_PER_USER);
    }
}
