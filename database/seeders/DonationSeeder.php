<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all campaigns and users
        $campaigns = Campaign::all();
        $users = User::all();

        // If no users exist, create some default users
        if ($users->isEmpty()) {
            $users = User::factory(10)->create();
        }

        $paymentMethods = ['bank_transfer', 'e-wallet', 'credit_card', 'cash'];
        $paymentTypes = ['manual', 'automatic'];
        $statuses = [0, 1, 2]; // 0 = pending, 1 = success, 2 = failed

        foreach ($campaigns as $campaign) {
            // Create 5 donations for each campaign
            for ($i = 0; $i < 5; $i++) {
                $user = $users->random();
                $amount = rand(10000, 1000000); // Random amount between 10k - 1M
                $status = $statuses[array_rand($statuses)];
                $isAnonymous = rand(0, 1);
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $paymentType = $paymentTypes[array_rand($paymentTypes)];

                $donation = Donation::create([
                    'campaign_id' => $campaign->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'status' => $status,
                    'is_anonymous' => $isAnonymous,
                    'message' => $this->getRandomMessage(),
                    'order_id' => 'ORD-'.time().'-'.rand(1000, 9999),
                    'payment_type' => $paymentType,
                    'paid_at' => $status == 1 ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }
    }

    /**
     * Get random donation message
     */
    private function getRandomMessage(): ?string
    {
        $messages = [
            'Semoga bermanfaat untuk yang membutuhkan',
            'Doa terbaik untuk kampanye ini',
            'Semoga Allah SWT membalas kebaikan kita semua',
            'Semoga menjadi amal jariyah',
            'Semoga membantu meringankan beban mereka',
            'Doa untuk kesuksesan kampanye ini',
            'Semoga menjadi berkah untuk semua',
            'Semoga Allah SWT meridhoi niat baik kita',
            'Semoga menjadi ladang pahala',
            'Doa untuk kebaikan semua pihak',
            null, // Sometimes no message
            null,
        ];

        return $messages[array_rand($messages)];
    }
}
