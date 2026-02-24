<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 5, 200);
        $qty = fake()->numberBetween(1, 5);
        $subtotal = round($unitPrice * $qty, 2);

        return [
            'purchase_id' => null,
            'order_number' => 'ORD-'.date('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 8)),
            'buyer_id' => \App\Models\User::factory(),
            'seller_id' => \App\Models\User::factory(),
            'listing_id' => \App\Models\Listing::factory(),
            'variation_id' => null,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'shipping_cost' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $subtotal,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'delivery_option_name' => 'Pickup / Arrange with Seller',
            'delivery_cost_paid_by' => 'seller',
        ];
    }
}
