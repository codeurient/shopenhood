<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed']);

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $type === 'percentage' ? fake()->numberBetween(5, 50) : fake()->numberBetween(5, 100),
            'min_purchase_amount' => fake()->optional(0.5)->randomFloat(2, 10, 500),
            'max_discount_amount' => fake()->optional(0.3)->randomFloat(2, 10, 200),
            'usage_limit' => fake()->optional(0.5)->numberBetween(10, 1000),
            'usage_count' => 0,
            'per_user_limit' => fake()->optional(0.4)->numberBetween(1, 5),
            'applicable_to' => 'all',
            'starts_at' => null,
            'expires_at' => null,
            'is_active' => true,
            'description' => fake()->optional(0.7)->sentence(),
        ];
    }

    public function percentage(): static
    {
        return $this->state(fn () => [
            'type' => 'percentage',
            'value' => fake()->numberBetween(5, 50),
        ]);
    }

    public function fixed(): static
    {
        return $this->state(fn () => [
            'type' => 'fixed',
            'value' => fake()->numberBetween(5, 100),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function forCategories(): static
    {
        return $this->state(fn () => [
            'applicable_to' => 'categories',
        ]);
    }

    public function forListings(): static
    {
        return $this->state(fn () => [
            'applicable_to' => 'listings',
        ]);
    }

    public function forUsers(): static
    {
        return $this->state(fn () => [
            'applicable_to' => 'users',
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }
}
