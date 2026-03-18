<?php

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view activity logs index', function () {
    activity()->causedBy($this->admin)->log('Test action');

    $response = $this->get(route('admin.activity-logs.index'));

    $response->assertSuccessful();
    $response->assertSee('Activity Logs');
});

test('admin can clear old activity logs', function () {
    $old = Activity::create([
        'log_name' => 'default',
        'description' => 'Old log',
        'causer_type' => User::class,
        'causer_id' => $this->admin->id,
        'properties' => [],
        'created_at' => now()->subDays(400),
        'updated_at' => now()->subDays(400),
    ]);

    $recent = Activity::create([
        'log_name' => 'default',
        'description' => 'Recent log',
        'causer_type' => User::class,
        'causer_id' => $this->admin->id,
        'properties' => [],
    ]);

    $response = $this->delete(route('admin.activity-logs.clear-old'));

    $response->assertRedirect(route('admin.activity-logs.index'));
    $response->assertSessionHas('success');
    expect(Activity::find($old->id))->toBeNull();
    expect(Activity::find($recent->id))->not->toBeNull();
});

test('admin can clear all activity logs', function () {
    Activity::create([
        'log_name' => 'default',
        'description' => 'Log one',
        'causer_type' => User::class,
        'causer_id' => $this->admin->id,
        'properties' => [],
    ]);
    Activity::create([
        'log_name' => 'default',
        'description' => 'Log two',
        'causer_type' => User::class,
        'causer_id' => $this->admin->id,
        'properties' => [],
    ]);

    $response = $this->delete(route('admin.activity-logs.clear-all'));

    $response->assertRedirect(route('admin.activity-logs.index'));
    $response->assertSessionHas('success');
    expect(Activity::count())->toBe(0);
});

test('clear all logs shows count in success message', function () {
    Activity::create([
        'log_name' => 'default',
        'description' => 'A log',
        'causer_type' => User::class,
        'causer_id' => $this->admin->id,
        'properties' => [],
    ]);

    $response = $this->delete(route('admin.activity-logs.clear-all'));

    $response->assertSessionHas('success', fn ($msg) => str_contains($msg, '1'));
});
