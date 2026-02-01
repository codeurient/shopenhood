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
            'created_as_role' => 'admin',
        ];
    }
}
