<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\CampaignArticle>
 */
class CampaignArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->randomElement([
            'Laporan Perkembangan Terbaru',
            'Penyaluran Bantuan Tahap Pertama',
            'Kabar dari Lapangan',
            'Dokumentasi Progres Pekerjaan',
            'Rencana Kegiatan Minggu Ini',
            'Ringkasan Penggunaan Dana',
        ]) . ' ' . fake()->date('F Y');

        $paragraphs = array_merge(
            [
                'Alhamdulillah, berikut kami sampaikan kabar terbaru terkait perkembangan kampanye ini. Terima kasih atas dukungan dan doa dari para donatur sekalian.',
            ],
            fake()->paragraphs(fake()->numberBetween(3, 6))
        );

        return [
            'campaign_id' => Campaign::factory(),
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 999999),
            'content' => implode("\n\n", $paragraphs),
        ];
    }
}

