<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get necessary data (any users except admins)
        $users = User::where('current_role', '!=', 'admin')->get();

        // If no regular users, get any user
        if ($users->isEmpty()) {
            $users = User::limit(10)->get();
        }
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();
        $listingType = ListingType::where('slug', 'sell')->first();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');

            return;
        }

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');

            return;
        }

        if (! $listingType) {
            $this->command->warn('No listing type found. Please run ListingTypeSeeder first.');

            return;
        }

        // Create 20 sample listings
        $sampleProducts = [
            ['title' => 'Wireless Bluetooth Headphones', 'price' => 79.99, 'condition' => 'new', 'featured' => true],
            ['title' => 'Vintage Leather Jacket', 'price' => 129.99, 'condition' => 'used', 'featured' => true],
            ['title' => 'Smart Watch Series 5', 'price' => 299.99, 'condition' => 'new', 'featured' => true],
            ['title' => 'Coffee Maker Deluxe', 'price' => 89.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Mountain Bike 21 Speed', 'price' => 450.00, 'condition' => 'used', 'featured' => true],
            ['title' => 'Gaming Laptop Core i7', 'price' => 1299.99, 'condition' => 'new', 'featured' => true],
            ['title' => 'Wooden Dining Table Set', 'price' => 399.99, 'condition' => 'used', 'featured' => false],
            ['title' => 'Portable Bluetooth Speaker', 'price' => 49.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Professional Camera DSLR', 'price' => 899.99, 'condition' => 'used', 'featured' => true],
            ['title' => 'Electric Guitar with Amp', 'price' => 350.00, 'condition' => 'used', 'featured' => false],
            ['title' => 'Running Shoes Premium', 'price' => 119.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Office Chair Ergonomic', 'price' => 249.99, 'condition' => 'new', 'featured' => true],
            ['title' => 'Smartphone 128GB', 'price' => 699.99, 'condition' => 'new', 'featured' => true],
            ['title' => 'Winter Coat Heavy Duty', 'price' => 159.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Yoga Mat Premium', 'price' => 39.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Desk Lamp LED Adjustable', 'price' => 45.00, 'condition' => 'new', 'featured' => false],
            ['title' => 'Backpack Travel 40L', 'price' => 79.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Air Purifier HEPA Filter', 'price' => 199.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Wall Art Canvas Set', 'price' => 89.99, 'condition' => 'new', 'featured' => false],
            ['title' => 'Electric Scooter Foldable', 'price' => 499.99, 'condition' => 'new', 'featured' => true],
        ];

        foreach ($sampleProducts as $index => $product) {
            $user = $users->random();
            $category = $categories->random();

            $listing = Listing::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'listing_type_id' => $listingType->id,
                'title' => $product['title'],
                'slug' => \Illuminate\Support\Str::slug($product['title']).'-'.uniqid(),
                'description' => 'This is a high-quality '.$product['title'].'. '.fake()->paragraph(),
                'short_description' => fake()->sentence(10),
                'base_price' => $product['price'],
                'discount_price' => $product['featured'] && rand(0, 1) ? $product['price'] * 0.85 : null,
                'discount_start_date' => $product['featured'] && rand(0, 1) ? now() : null,
                'discount_end_date' => $product['featured'] && rand(0, 1) ? now()->addDays(7) : null,
                'currency' => 'USD',
                'status' => 'active',
                'is_visible' => true,
                'is_featured' => $product['featured'],
                'is_negotiable' => rand(0, 1),
                'condition' => $product['condition'],
                'is_wholesale' => rand(0, 3) === 0,
                'wholesale_min_order_qty' => rand(0, 3) === 0 ? rand(10, 100) : null,
                'availability_type' => 'in_stock',
                'has_delivery' => rand(0, 1),
                'has_domestic_delivery' => rand(0, 1),
                'domestic_delivery_price' => rand(0, 1) ? rand(5, 20) : null,
                'country' => 'United States',
                'city' => fake()->city(),
                'created_as_role' => 'normal_user',
            ]);

            $this->command->info("Created listing: {$listing->title}");
        }

        $this->command->info('Listings seeded successfully!');
    }
}
