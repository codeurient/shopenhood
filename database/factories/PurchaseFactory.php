<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10, 500);
        $shipping = fake()->randomFloat(2, 0, 20);

        return [
            'purchase_number' => Purchase::generatePurchaseNumber(),
            'buyer_id' => User::factory(),
            'address_snapshot' => [
                'label' => 'Home',
                'recipient_name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'email' => fake()->safeEmail(),
                'country' => fake()->country(),
                'city' => fake()->city(),
                'district' => null,
                'street' => fake()->streetAddress(),
                'building' => null,
                'apartment' => null,
                'postal_code' => fake()->postcode(),
                'additional_notes' => null,
                'full_address' => fake()->address(),
            ],
            'payment_method' => 'cash_on_delivery',
            'notes' => fake()->optional()->sentence(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'discount_amount' => 0,
            'total_amount' => round($subtotal + $shipping, 2),
            'currency' => 'USD',
            'status' => 'pending',
        ];
    }

    public function forBuyer(int $buyerId): static
    {
        return $this->state(fn (array $attributes) => [
            'buyer_id' => $buyerId,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'cancelled']);
    }
}
