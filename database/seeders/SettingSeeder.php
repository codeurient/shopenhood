<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'listing.default_duration_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'listing',
                'is_public' => false,
            ],
            [
                'key' => 'listing.soft_delete_retention_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'listing',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
