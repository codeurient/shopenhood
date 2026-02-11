<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Models\Activity;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('current_role', 'admin')->first();

        if (! $admin) {
            $this->command->warn('No admin user found. Creating one...');
            $admin = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'current_role' => 'admin',
            ]);
        }

        $actions = [
            ['log_name' => 'default', 'description' => 'Admin logged in', 'properties' => ['ip' => '127.0.0.1']],
            ['log_name' => 'default', 'description' => 'Category created', 'properties' => ['name' => 'Electronics']],
            ['log_name' => 'default', 'description' => 'Listing approved', 'properties' => ['listing_id' => 1]],
            ['log_name' => 'default', 'description' => 'User updated', 'properties' => ['user_id' => 2]],
            ['log_name' => 'default', 'description' => 'Coupon created', 'properties' => ['code' => 'SUMMER2026']],
            ['log_name' => 'default', 'description' => 'Setting updated', 'properties' => ['key' => 'site_name']],
            ['log_name' => 'default', 'description' => 'Listing rejected', 'properties' => ['listing_id' => 5, 'reason' => 'Invalid content']],
            ['log_name' => 'default', 'description' => 'User suspended', 'properties' => ['user_id' => 10]],
            ['log_name' => 'default', 'description' => 'Category updated', 'properties' => ['id' => 3, 'name' => 'Home & Garden']],
            ['log_name' => 'default', 'description' => 'Coupon status toggled', 'properties' => ['code' => 'WINTER50', 'is_active' => false]],
        ];

        foreach ($actions as $index => $action) {
            Activity::create([
                'log_name' => $action['log_name'],
                'description' => $action['description'],
                'subject_type' => null,
                'subject_id' => null,
                'causer_type' => User::class,
                'causer_id' => $admin->id,
                'properties' => $action['properties'],
                'created_at' => now()->subMinutes((count($actions) - $index) * 30),
                'updated_at' => now()->subMinutes((count($actions) - $index) * 30),
            ]);
        }

        $this->command->info('Created '.count($actions).' activity log entries.');
    }
}
