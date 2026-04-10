<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDonationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Donation $donation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $campaign = $this->donation->campaign;
        $donor = $this->donation->user;

        return (new MailMessage)
            ->subject('Donasi Baru Diterima untuk Campaign: '.$campaign->title)
            ->view('emails.notifications.new-donation-received', [
                'donation' => $this->donation,
                'donor' => $donor,
                'campaign' => $campaign,
                'campaignOwner' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'donation_id' => $this->donation->id,
            'campaign_id' => $this->donation->campaign_id,
            'amount' => $this->donation->amount,
            'order_id' => $this->donation->order_id,
            'donor_name' => $this->donation->is_anonymous ? 'Anonymous' : $this->donation->user->name,
        ];
    }
}
