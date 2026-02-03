<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'type' => 'country',
            'code' => strtoupper(fake()->lexify('??')),
            'is_active' => true,
        ];
    }

    public function city(?int $parentId = null): static
    {
        return $this->state(fn () => [
            'name' => fake()->unique()->city(),
            'type' => 'city',
            'parent_id' => $parentId,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
