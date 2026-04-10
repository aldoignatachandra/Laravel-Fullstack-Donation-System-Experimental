<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have base data
        if (CampaignCategory::count() === 0) {
            $this->call(CampaignCategorySeeder::class);
        }
        if (User::count() === 0) {
            User::factory(5)->create();
        }

        $categoryIds = CampaignCategory::query()->pluck('id');
        $userIds = User::query()->pluck('id');

        // Use factory with realistic data; bind to existing categories and users
        Campaign::factory()
            ->count(24)
            ->state(function () use ($categoryIds, $userIds) {
                return [
                    'campaign_category_id' => $categoryIds->random(),
                    'user_id' => $userIds->random(),
                ];
            })
            ->create();
    }
}
