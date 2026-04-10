<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
