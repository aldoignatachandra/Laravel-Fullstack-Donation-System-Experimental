<?php

namespace Tests\Unit\Notifications;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\DonationFailureNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class DonationFailureNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_created_with_donation(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_FAILED,
        ]);

        $notification = new DonationFailureNotification($donation);

        $this->assertInstanceOf(Donation::class, $notification->donation);
        $this->assertEquals($donation->id, $notification->donation->id);
    }

    public function test_notification_uses_mail_channel(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);
        $notification = new DonationFailureNotification($donation);

        $channels = $notification->via(new User);

        $this->assertContains('mail', $channels);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 50000,
            'order_id' => 'TEST-456',
            'status' => Donation::STATUS_FAILED,
        ]);
        $notification = new DonationFailureNotification($donation);

        $array = $notification->toArray(new User);

        $this->assertIsArray($array);
        $this->assertArrayHasKey('donation_id', $array);
        $this->assertArrayHasKey('campaign_id', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('order_id', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertEquals($donation->id, $array['donation_id']);
        $this->assertEquals(Donation::STATUS_FAILED, $array['status']);
    }

    public function test_to_mail_returns_mail_message(): void
    {
        $campaign = Campaign::factory()->create(['title' => 'Test Campaign']);
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => Donation::STATUS_FAILED,
        ]);
        $notification = new DonationFailureNotification($donation);

        $mail = $notification->toMail(new User);

        $this->assertInstanceOf(MailMessage::class, $mail);
    }
}
