<?php

namespace Tests\Unit\Notifications;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\DonationSuccessNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class DonationSuccessNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test notification is created with donation.
     */
    public function test_notification_is_created_with_donation(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 100000,
        ]);

        $notification = new DonationSuccessNotification($donation);

        $this->assertInstanceOf(Donation::class, $notification->donation);
        $this->assertEquals($donation->id, $notification->donation->id);
    }

    /**
     * Test notification is sent via mail channel.
     */
    public function test_notification_uses_mail_channel(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);
        $notification = new DonationSuccessNotification($donation);

        $channels = $notification->via(new User);

        $this->assertContains('mail', $channels);
    }

    /**
     * Test toArray returns correct data structure.
     */
    public function test_to_array_returns_correct_structure(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 50000,
            'order_id' => 'TEST-123',
        ]);
        $notification = new DonationSuccessNotification($donation);

        $array = $notification->toArray(new User);

        $this->assertIsArray($array);
        $this->assertArrayHasKey('donation_id', $array);
        $this->assertArrayHasKey('campaign_id', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('order_id', $array);
        $this->assertEquals($donation->id, $array['donation_id']);
        $this->assertEquals($donation->amount, $array['amount']);
        $this->assertEquals('TEST-123', $array['order_id']);
    }

    /**
     * Test toMail returns MailMessage instance.
     */
    public function test_to_mail_returns_mail_message(): void
    {
        $campaign = Campaign::factory()->create(['title' => 'Test Campaign']);
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 100000,
        ]);
        $notification = new DonationSuccessNotification($donation);

        $mail = $notification->toMail(new User);

        $this->assertInstanceOf(MailMessage::class, $mail);
    }

    /**
     * Test notification implements ShouldQueue.
     */
    public function test_notification_implements_should_queue(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);
        $notification = new DonationSuccessNotification($donation);

        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $notification);
    }
}
