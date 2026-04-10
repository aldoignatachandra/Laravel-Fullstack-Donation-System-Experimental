<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignArticle;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() === 0) {
            User::factory(3)->create();
        }

        if (Campaign::count() === 0) {
            $this->call(CampaignSeeder::class);
        }

        $authorIds = User::query()->pluck('id');

        Campaign::query()->each(function (Campaign $campaign) use ($authorIds) {
            $count = rand(2, 5);

            CampaignArticle::factory()
                ->count($count)
                ->state(function () use ($campaign, $authorIds) {
                    return [
                        'campaign_id' => $campaign->id,
                        'author_id' => $authorIds->random(),
                    ];
                })
                ->create();
        });
    }
}

