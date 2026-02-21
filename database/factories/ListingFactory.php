<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'listing_type_id' => ListingType::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'status' => 'active',
            'condition' => fake()->randomElement(['new', 'used']),
            'created_as_role' => 'normal_user',
            'listing_mode' => 'normal',
        ];
    }

    /**
     * Normal user listing: has base_price, country/city strings, no product variations.
     */
    public function normalMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_mode' => 'normal',
            'created_as_role' => 'normal_user',
            'base_price' => fake()->randomFloat(2, 10, 1000),
        ]);
    }

    /**
     * Business user listing: no base_price, uses location_id, has product variations.
     */
    public function businessMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_mode' => 'business',
            'created_as_role' => 'business_user',
            'base_price' => null,
        ]);
    }
}
