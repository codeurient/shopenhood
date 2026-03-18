<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $keys = array_keys(Page::PAGES);

        return [
            'key' => fake()->unique()->randomElement($keys),
            'title' => fake()->words(3, true),
            'content' => fake()->paragraphs(3, true),
            'meta_description' => fake()->sentence(),
            'is_published' => true,
        ];
    }
}
