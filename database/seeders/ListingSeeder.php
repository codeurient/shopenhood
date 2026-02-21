<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Location;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $listingType = ListingType::where('slug', 'sell')->first();
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();

        if (! $listingType) {
            $this->command->warn('No listing type found. Please run ListingTypeSeeder first.');

            return;
        }

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');

            return;
        }

        $this->seedNormalListings($listingType, $categories);
        $this->seedBusinessListings($listingType, $categories);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Normal-user listings
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normal user listings: single price on the listing, country/city as plain strings,
     * no product variations.
     */
    private function seedNormalListings(ListingType $listingType, $categories): void
    {
        $normalUser = User::where('email', 'user@example.com')->first();

        if (! $normalUser) {
            $this->command->warn('Normal user (user@example.com) not found. Please run UserSeeder first.');

            return;
        }

        $products = [
            ['title' => 'Wireless Bluetooth Headphones', 'price' => 79.99,  'condition' => 'new',  'featured' => true],
            ['title' => 'Vintage Leather Jacket',         'price' => 129.99, 'condition' => 'used', 'featured' => false],
            ['title' => 'Coffee Maker Deluxe',            'price' => 89.99,  'condition' => 'new',  'featured' => false],
            ['title' => 'Portable Bluetooth Speaker',     'price' => 49.99,  'condition' => 'new',  'featured' => false],
            ['title' => 'Running Shoes Premium',          'price' => 119.99, 'condition' => 'new',  'featured' => false],
            ['title' => 'Yoga Mat Premium',               'price' => 39.99,  'condition' => 'new',  'featured' => false],
            ['title' => 'Desk Lamp LED Adjustable',       'price' => 45.00,  'condition' => 'new',  'featured' => false],
            ['title' => 'Backpack Travel 40L',            'price' => 79.99,  'condition' => 'new',  'featured' => false],
            ['title' => 'Wall Art Canvas Set',            'price' => 89.99,  'condition' => 'new',  'featured' => false],
            ['title' => 'Electric Guitar with Amp',       'price' => 350.00, 'condition' => 'used', 'featured' => false],
        ];

        foreach ($products as $product) {
            $discounted = $product['featured'] && (bool) rand(0, 1);

            Listing::create([
                'user_id' => $normalUser->id,
                'category_id' => $categories->random()->id,
                'listing_type_id' => $listingType->id,
                'listing_mode' => 'normal',
                'created_as_role' => 'normal_user',
                'title' => $product['title'],
                'slug' => Str::slug($product['title']).'-'.uniqid(),
                'description' => 'This is a '.$product['title'].'. '.fake()->paragraph(),
                'short_description' => fake()->sentence(10),
                'base_price' => $product['price'],
                'discount_price' => $discounted ? round($product['price'] * 0.85, 2) : null,
                'discount_start_date' => $discounted ? now() : null,
                'discount_end_date' => $discounted ? now()->addDays(7) : null,
                'currency' => 'USD',
                'status' => 'active',
                'is_visible' => true,
                'is_featured' => $product['featured'],
                'is_negotiable' => (bool) rand(0, 1),
                'condition' => $product['condition'],
                'availability_type' => 'in_stock',
                'has_delivery' => (bool) rand(0, 1),
                'has_domestic_delivery' => (bool) rand(0, 1),
                'domestic_delivery_price' => rand(0, 1) ? rand(5, 20) : null,
                'country' => 'United States',
                'city' => fake()->city(),
            ]);

            $this->command->info("Created normal listing: {$product['title']}");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Business-user listings
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Business user listings: no base_price on the listing itself — price lives on each
     * ProductVariation. Location is stored as a location_id FK, not plain strings.
     * Each listing gets 2-4 variations; the first is marked as default.
     */
    private function seedBusinessListings(ListingType $listingType, $categories): void
    {
        $businessUser = User::where('email', 'business@example.com')->first();

        if (! $businessUser) {
            $this->command->warn('Business user (business@example.com) not found. Please run UserSeeder first.');

            return;
        }

        // Prefer a city location so cards can resolve city + parent country
        $location = Location::where('type', 'city')->inRandomOrder()->first()
            ?? Location::first();

        if (! $location) {
            $this->command->warn('No locations found. Please run LocationSeeder first.');

            return;
        }

        $products = [
            [
                'title' => 'Smart Watch Series 5',
                'featured' => true,
                'condition' => 'new',
                'variations' => [
                    ['label' => 'Silver 40mm', 'price' => 299.99, 'stock' => 50],
                    ['label' => 'Gold 44mm',   'price' => 349.99, 'stock' => 30],
                    ['label' => 'Black 44mm',  'price' => 329.99, 'stock' => 20],
                ],
            ],
            [
                'title' => 'Gaming Laptop Core i7',
                'featured' => true,
                'condition' => 'new',
                'variations' => [
                    ['label' => '16GB RAM / 512GB SSD', 'price' => 1299.99, 'stock' => 10],
                    ['label' => '32GB RAM / 1TB SSD',   'price' => 1599.99, 'stock' => 5],
                ],
            ],
            [
                'title' => 'Mountain Bike 21 Speed',
                'featured' => true,
                'condition' => 'used',
                'variations' => [
                    ['label' => 'Small Frame',  'price' => 399.99, 'stock' => 3],
                    ['label' => 'Medium Frame', 'price' => 420.00, 'stock' => 5],
                    ['label' => 'Large Frame',  'price' => 450.00, 'stock' => 2],
                ],
            ],
            [
                'title' => 'Professional Camera DSLR',
                'featured' => true,
                'condition' => 'used',
                'variations' => [
                    ['label' => 'Body Only',           'price' => 799.99, 'stock' => 4],
                    ['label' => 'Body + 18-55mm Lens', 'price' => 899.99, 'stock' => 3],
                ],
            ],
            [
                'title' => 'Office Chair Ergonomic',
                'featured' => true,
                'condition' => 'new',
                'variations' => [
                    ['label' => 'Black Mesh',  'price' => 249.99, 'stock' => 20],
                    ['label' => 'Grey Fabric', 'price' => 239.99, 'stock' => 15],
                ],
            ],
            [
                'title' => 'Smartphone 128GB',
                'featured' => true,
                'condition' => 'new',
                'variations' => [
                    ['label' => '128GB Black', 'price' => 699.99, 'stock' => 25],
                    ['label' => '256GB Black', 'price' => 799.99, 'stock' => 15],
                    ['label' => '256GB White', 'price' => 799.99, 'stock' => 10],
                ],
            ],
            [
                'title' => 'Electric Scooter Foldable',
                'featured' => true,
                'condition' => 'new',
                'variations' => [
                    ['label' => '25km Range', 'price' => 449.99, 'stock' => 10],
                    ['label' => '40km Range', 'price' => 549.99, 'stock' => 6],
                ],
            ],
            [
                'title' => 'Wooden Dining Table Set',
                'featured' => false,
                'condition' => 'used',
                'variations' => [
                    ['label' => '4-Seat Set', 'price' => 399.99, 'stock' => 2],
                    ['label' => '6-Seat Set', 'price' => 499.99, 'stock' => 1],
                ],
            ],
            [
                'title' => 'Air Purifier HEPA Filter',
                'featured' => false,
                'condition' => 'new',
                'variations' => [
                    ['label' => 'Small Room (up to 25m²)', 'price' => 149.99, 'stock' => 20],
                    ['label' => 'Large Room (up to 60m²)', 'price' => 199.99, 'stock' => 12],
                ],
            ],
            [
                'title' => 'Winter Coat Heavy Duty',
                'featured' => false,
                'condition' => 'new',
                'variations' => [
                    ['label' => 'S / Black', 'price' => 149.99, 'stock' => 10],
                    ['label' => 'M / Black', 'price' => 149.99, 'stock' => 15],
                    ['label' => 'L / Navy',  'price' => 154.99, 'stock' => 8],
                    ['label' => 'XL / Navy', 'price' => 159.99, 'stock' => 5],
                ],
            ],
        ];

        foreach ($products as $product) {
            $listing = Listing::create([
                'user_id' => $businessUser->id,
                'category_id' => $categories->random()->id,
                'listing_type_id' => $listingType->id,
                'listing_mode' => 'business',
                'created_as_role' => 'business_user',
                'title' => $product['title'],
                'slug' => Str::slug($product['title']).'-'.uniqid(),
                'description' => 'Premium '.$product['title'].'. '.fake()->paragraph(),
                'short_description' => fake()->sentence(10),
                'base_price' => null,
                'currency' => 'USD',
                'status' => 'active',
                'is_visible' => true,
                'is_featured' => $product['featured'],
                'is_negotiable' => false,
                'condition' => $product['condition'],
                'availability_type' => 'in_stock',
                'has_delivery' => true,
                'has_domestic_delivery' => true,
                'domestic_delivery_price' => 5.00,
                'has_international_delivery' => true,
                'international_delivery_price' => 15.00,
                'location_id' => $location->id,
            ]);

            foreach ($product['variations'] as $index => $variation) {
                ProductVariation::create([
                    'listing_id' => $listing->id,
                    'sku' => strtoupper(Str::slug($listing->title)).'-'.strtoupper(Str::slug($variation['label'])).'-'.uniqid(),
                    'price' => $variation['price'],
                    'stock_quantity' => $variation['stock'],
                    'is_active' => true,
                    'is_default' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            $variationCount = count($product['variations']);
            $this->command->info("Created business listing: {$listing->title} ({$variationCount} variations)");
        }
    }
}
