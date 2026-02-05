<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Level 1 categories (parents)
        $electronics = Category::create([
            'parent_id' => null,
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
            'icon' => '',
            'image' => 'electronics.jpg',
            'level' => 1,
            'path' => null,
            'sort_order' => 1,
            'is_active' => true,
            'meta_title' => 'Electronics',
            'meta_description' => 'Buy electronic products online',
        ]);

        $fashion = Category::create([
            'parent_id' => null,
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Clothing and accessories',
            'icon' => '',
            'image' => 'fashion.jpg',
            'level' => 1,
            'path' => null,
            'sort_order' => 2,
            'is_active' => true,
            'meta_title' => 'Fashion',
            'meta_description' => 'Latest fashion trends',
        ]);

        // Level 2 categories
        $phones = Category::create([
            'parent_id' => $electronics->id,
            'name' => 'Mobile Phones',
            'slug' => 'mobile-phones',
            'description' => 'Smartphones and mobile devices',
            'icon' => '',
            'image' => 'phones.jpg',
            'level' => 2,
            'path' => $electronics->id,
            'sort_order' => 1,
            'is_active' => true,
            'meta_title' => 'Mobile Phones',
            'meta_description' => 'Smartphones from top brands',
        ]);

        $laptops = Category::create([
            'parent_id' => $electronics->id,
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Laptops and notebooks',
            'icon' => '',
            'image' => 'laptops.jpg',
            'level' => 2,
            'path' => $electronics->id,
            'sort_order' => 2,
            'is_active' => true,
            'meta_title' => 'Laptops',
            'meta_description' => 'Powerful laptops for work and gaming',
        ]);

        // Level 3 categories
        Category::create([
            'parent_id' => $phones->id,
            'name' => 'Android Phones',
            'slug' => 'android-phones',
            'description' => 'Android based smartphones',
            'icon' => '',
            'image' => 'android.jpg',
            'level' => 3,
            'path' => $electronics->id.'/'.$phones->id,
            'sort_order' => 1,
            'is_active' => true,
            'meta_title' => 'Android Phones',
            'meta_description' => 'Best Android smartphones',
        ]);

        Category::create([
            'parent_id' => $phones->id,
            'name' => 'iPhones',
            'slug' => 'iphones',
            'description' => 'Apple iPhones',
            'icon' => '',
            'image' => 'iphone.jpg',
            'level' => 3,
            'path' => $electronics->id.'/'.$phones->id,
            'sort_order' => 2,
            'is_active' => true,
            'meta_title' => 'iPhones',
            'meta_description' => 'Apple iPhones collection',
        ]);
    }
}
