<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\CampaignArticleSeeder;
use Database\Seeders\CampaignCategorySeeder;
use Database\Seeders\CampaignSeeder;
use Database\Seeders\DonationSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories, campaigns (via factory), and sample donations
        $this->call([
            ShieldSeeder::class,
            UserSeeder::class,
            CampaignCategorySeeder::class,
            CampaignSeeder::class,
            DonationSeeder::class,
            CampaignArticleSeeder::class,
        ]);

        $this->command->info('Database seeded successfully');
    }
}
