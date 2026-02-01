<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Color', 'Size', 'Storage', 'Material', 'Weight', 'Style']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => 'select',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
