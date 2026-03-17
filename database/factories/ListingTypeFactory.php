<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingType>
 */
class ListingTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Test Type ' . fake()->unique()->numberBetween(1, 9999),
            'slug' => 'test-type-' . fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->sentence(),
            'requires_price' => false,
            'icon' => fake()->randomElement(['💰', '🛒', '🎁', '🔄', '🔨']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    public function sell(): static
    {
        return $this->state([
            'name' => 'Sell',
            'slug' => 'sell',
            'requires_price' => true,
        ]);
    }

    public function auction(): static
    {
        return $this->state([
            'name' => 'Auction',
            'slug' => 'auction',
            'requires_price' => true,
        ]);
    }

    public function buy(): static
    {
        return $this->state([
            'name' => 'Buy',
            'slug' => 'buy',
            'requires_price' => false,
        ]);
    }

    public function barter(): static
    {
        return $this->state([
            'name' => 'Barter',
            'slug' => 'barter',
            'requires_price' => false,
        ]);
    }

    public function gift(): static
    {
        return $this->state([
            'name' => 'Gift',
            'slug' => 'gift',
            'requires_price' => false,
        ]);
    }
}
