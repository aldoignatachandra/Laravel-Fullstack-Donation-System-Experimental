<?php

namespace Tests\Unit\Notifications;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\NewDonationReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class NewDonationReceivedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_created_with_donation(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 100000,
        ]);

        $notification = new NewDonationReceivedNotification($donation);

        $this->assertInstanceOf(Donation::class, $notification->donation);
        $this->assertEquals($donation->id, $notification->donation->id);
    }

    public function test_notification_uses_mail_channel(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);
        $notification = new NewDonationReceivedNotification($donation);

        $channels = $notification->via(new User);

        $this->assertContains('mail', $channels);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 75000,
            'order_id' => 'TEST-789',
        ]);
        $notification = new NewDonationReceivedNotification($donation);

        $array = $notification->toArray(new User);

        $this->assertIsArray($array);
        $this->assertArrayHasKey('donation_id', $array);
        $this->assertArrayHasKey('campaign_id', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('order_id', $array);
        $this->assertEquals($donation->id, $array['donation_id']);
        $this->assertEquals(75000, $array['amount']);
    }

    public function test_to_mail_returns_mail_message(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 100000,
        ]);
        $notification = new NewDonationReceivedNotification($donation);

        $mail = $notification->toMail(new User);

        $this->assertInstanceOf(MailMessage::class, $mail);
    }
}
