<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessProfile>
 */
class BusinessProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessName = fake()->company();

        return [
            'user_id' => User::factory(),
            'business_name' => $businessName,
            'legal_name' => $businessName.' LLC',
            'slug' => Str::slug($businessName).'-'.Str::random(6),
            'description' => fake()->paragraph(),
            'registration_number' => fake()->numerify('REG-######'),
            'tax_id' => fake()->numerify('TAX-########'),
            'industry' => fake()->randomElement(['retail', 'wholesale', 'manufacturing', 'services', 'technology']),
            'business_type' => fake()->randomElement(['sole_proprietor', 'partnership', 'llc', 'corporation']),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state_province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'business_email' => fake()->companyEmail(),
            'business_phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'default_currency' => 'USD',
            'timezone' => fake()->timezone(),
        ];
    }
}
