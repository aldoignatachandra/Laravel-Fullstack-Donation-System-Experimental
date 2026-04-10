<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a more realistic Indonesian-style campaign title
        $topics = [
            'Bantuan Korban Banjir',
            'Operasi Jantung untuk Bayi',
            'Pembangunan Masjid Desa',
            'Beasiswa Mahasiswa Berprestasi',
            'Bantuan Pengobatan Kanker',
            'Perbaikan Sekolah Rusak',
            'Air Bersih untuk Desa',
            'Bantuan Anak Yatim Piatu',
            'Pemulihan Pasca Gempa',
            'Penghijauan dan Reboisasi'
        ];

        $city = fake()->city();
        $topic = fake()->randomElement($topics);
        $title = $topic . ' di ' . $city;

        // Longer, multi-paragraph description that better represents real campaigns
        $paragraphs = array_merge(
            [
                'Mari bersama-sama membantu sesama melalui kampanye ini. Dana yang terkumpul akan digunakan secara transparan untuk kebutuhan utama penerima manfaat, sesuai dengan rencana penyaluran yang telah disusun dan diverifikasi.',
            ],
            fake()->paragraphs(fake()->numberBetween(4, 7))
        );
        $description = implode("\n\n", $paragraphs);

        // Use null image to fall back to a bundled placeholder (see getImageUrlAttribute)
        return [
            'campaign_category_id' => \App\Models\CampaignCategory::factory(),
            'user_id' => \App\Models\User::factory(),
            'image' => null,
            'title' => $title,
            'description' => $description,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'target_amount' => fake()->numberBetween(10000000, 500000000), // Rp 10jt - 500jt
            'start_date' => fake()->dateTimeBetween('-2 months', 'now'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+8 months'),
            'status' => \App\Models\Campaign::STATUS_ACTIVE,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
