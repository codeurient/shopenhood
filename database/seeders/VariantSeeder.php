<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Size Variant
        $sizeVariant = \App\Models\Variant::create([
            'name' => 'Size',
            'slug' => 'size',
            'type' => 'select',
            'is_required' => true,
            'description' => 'Product size',
            'placeholder' => 'Select size',
            'help_text' => 'Choose the appropriate size for your product',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Size Items
        foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $index => $size) {
            \App\Models\VariantItem::create([
                'variant_id' => $sizeVariant->id,
                'value' => strtolower($size),
                'display_value' => $size,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        // Color Variant
        $colorVariant = \App\Models\Variant::create([
            'name' => 'Color',
            'slug' => 'color',
            'type' => 'select',
            'is_required' => false,
            'description' => 'Product color',
            'placeholder' => 'Select color',
            'help_text' => 'Choose the color of your product',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Color Items
        $colors = [
            'Black' => '#000000',
            'White' => '#FFFFFF',
            'Red' => '#FF0000',
            'Blue' => '#0000FF',
            'Green' => '#008000',
            'Yellow' => '#FFFF00',
            'Orange' => '#FFA500',
            'Purple' => '#800080',
            'Pink' => '#FFC0CB',
            'Gray' => '#808080',
        ];

        $sortOrder = 1;
        foreach ($colors as $color => $hex) {
            \App\Models\VariantItem::create([
                'variant_id' => $colorVariant->id,
                'value' => strtolower($color),
                'display_value' => $color,
                'color_code' => $hex,
                'sort_order' => $sortOrder++,
                'is_active' => true,
            ]);
        }

        // Material Variant
        $materialVariant = \App\Models\Variant::create([
            'name' => 'Material',
            'slug' => 'material',
            'type' => 'select',
            'is_required' => false,
            'description' => 'Product material',
            'placeholder' => 'Select material',
            'help_text' => 'Choose the material composition',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Material Items
        foreach (['Cotton', 'Polyester', 'Leather', 'Denim', 'Silk', 'Wool'] as $index => $material) {
            \App\Models\VariantItem::create([
                'variant_id' => $materialVariant->id,
                'value' => strtolower($material),
                'display_value' => $material,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        // Storage Variant (for electronics)
        $storageVariant = \App\Models\Variant::create([
            'name' => 'Storage',
            'slug' => 'storage',
            'type' => 'select',
            'is_required' => false,
            'description' => 'Storage capacity',
            'placeholder' => 'Select storage',
            'help_text' => 'Choose storage capacity',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // Storage Items
        foreach (['64GB', '128GB', '256GB', '512GB', '1TB'] as $index => $storage) {
            \App\Models\VariantItem::create([
                'variant_id' => $storageVariant->id,
                'value' => strtolower(str_replace(' ', '-', $storage)),
                'display_value' => $storage,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Variants seeded successfully!');
    }
}
