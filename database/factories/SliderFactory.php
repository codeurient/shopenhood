<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slider>
 */
class SliderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'subtitle' => fake()->sentence(6),
            'image' => 'sliders/placeholder.jpg',
            'link' => fake()->url(),
            'type' => fake()->randomElement(['main_slider', 'banner_small']),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => fake()->boolean(80),
        ];
    }

    public function mainSlider()
    {
        return $this->state(['type' => 'main_slider']);
    }

    public function smallBanner()
    {
        return $this->state(['type' => 'banner_small']);
    }
}
