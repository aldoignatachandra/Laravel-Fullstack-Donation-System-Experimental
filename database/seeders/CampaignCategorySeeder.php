<?php

namespace Database\Seeders;

use App\Models\CampaignCategory;
use Illuminate\Database\Seeder;

class CampaignCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pendidikan',
                'description' => 'Kampanye untuk membantu pendidikan anak-anak yang membutuhkan',
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Kampanye untuk membantu biaya pengobatan dan kesehatan',
            ],
            [
                'name' => 'Bencana Alam',
                'description' => 'Kampanye untuk membantu korban bencana alam',
            ],
            [
                'name' => 'Sosial',
                'description' => 'Kampanye untuk membantu masalah sosial masyarakat',
            ],
            [
                'name' => 'Infrastruktur',
                'description' => 'Kampanye untuk pembangunan infrastruktur yang dibutuhkan',
            ],
        ];

        foreach ($categories as $category) {
            CampaignCategory::create($category);
        }
    }
}
