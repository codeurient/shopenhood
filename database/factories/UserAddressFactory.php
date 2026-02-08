<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Other']),
            'is_default' => false,
            'recipient_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'district' => fake()->optional()->word(),
            'street' => fake()->streetAddress(),
            'building' => fake()->optional()->buildingNumber(),
            'apartment' => fake()->optional()->randomNumber(3),
            'postal_code' => fake()->optional()->postcode(),
            'additional_notes' => fake()->optional()->sentence(),
        ];
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function home(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Home',
        ]);
    }

    public function work(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Work',
        ]);
    }
}
