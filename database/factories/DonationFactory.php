<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => \App\Models\Campaign::factory(),
            'user_id' => \App\Models\User::factory(),
            'amount' => fake()->randomFloat(2, 10000, 1000000),
            'payment_method' => 'midtrans',
            'status' => fake()->randomElement([0, 1, 2, 3]), // pending, paid, failed, cancelled
            'is_anonymous' => fake()->boolean(20), // 20% chance of being anonymous
            'message' => fake()->optional(0.7)->sentence(),
            'order_id' => 'DON-'.fake()->unique()->numerify('########'),
            'payment_type' => fake()->randomElement(['credit_card', 'bank_transfer', 'e_wallet']),
            'paid_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
            'paid_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
            'paid_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
            'paid_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 3,
            'paid_at' => null,
        ]);
    }
}
