<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignCategory>
 */
class CampaignCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Pendidikan', 'Kesehatan', 'Bencana Alam', 'Sosial', 'Lingkungan']),
            'description' => fake()->sentence(),
        ];
    }
}
