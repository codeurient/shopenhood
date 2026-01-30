<?php

namespace Database\Seeders;

use App\Models\ListingType;
use Illuminate\Database\Seeder;

class ListingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listingTypes = [
            [
                'name' => 'Sell',
                'slug' => 'sell',
                'description' => 'List an item for sale at a fixed or negotiable price',
                'requires_price' => true,
                'icon' => '💰',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Buy',
                'slug' => 'buy',
                'description' => 'Post a request to buy a specific item',
                'requires_price' => true,
                'icon' => '🛒',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Gift',
                'slug' => 'gift',
                'description' => 'Give away items for free',
                'requires_price' => false,
                'icon' => '🎁',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Barter',
                'slug' => 'barter',
                'description' => 'Exchange items without money',
                'requires_price' => false,
                'icon' => '🔄',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Auction',
                'slug' => 'auction',
                'description' => 'Sell items through bidding',
                'requires_price' => true,
                'icon' => '🔨',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($listingTypes as $type) {
            ListingType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
