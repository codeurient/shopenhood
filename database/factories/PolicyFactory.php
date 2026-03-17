<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Policy>
 */
class PolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);

        return [
            'title' => ucwords($title),
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => $this->faker->sentence(),
            'content' => '<p>'.$this->faker->paragraph().'</p>',
            'policy_type' => $this->faker->randomElement(array_keys(\App\Models\Policy::POLICY_TYPES)),
            'version' => '1.0',
            'status' => 'active',
            'require_acceptance' => false,
            'show_in_footer' => false,
            'show_during_registration' => false,
            'show_during_checkout' => false,
            'show_during_listing' => false,
            'published_at' => now(),
        ];
    }
}
