<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Page::PAGES as $key => $title) {
            Page::firstOrCreate(
                ['key' => $key],
                [
                    'title' => $title,
                    'content' => '',
                    'is_published' => true,
                ]
            );
        }
    }
}
