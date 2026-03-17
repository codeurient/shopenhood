<?php

use App\Models\Policy;
use App\Models\PolicyVersion;

it('shows the legal center index page', function () {
    Policy::factory()->create([
        'title' => 'Terms of Service',
        'slug' => 'terms-of-service',
        'policy_type' => 'core',
        'status' => 'active',
        'show_in_footer' => true,
    ]);

    $response = $this->get(route('legal.index'));

    $response->assertOk();
    $response->assertSee('Legal Center');
    $response->assertSee('Terms of Service');
});

it('does not show inactive policies on the legal center index', function () {
    Policy::factory()->create([
        'title' => 'Inactive Policy',
        'slug' => 'inactive-policy',
        'policy_type' => 'core',
        'status' => 'inactive',
        'show_in_footer' => true,
    ]);

    $response = $this->get(route('legal.index'));

    $response->assertOk();
    $response->assertDontSee('Inactive Policy');
});

it('only shows footer policies on the legal center index', function () {
    Policy::factory()->create([
        'title' => 'Footer Policy',
        'slug' => 'footer-policy',
        'policy_type' => 'core',
        'status' => 'active',
        'show_in_footer' => true,
    ]);
    Policy::factory()->create([
        'title' => 'Non Footer Policy',
        'slug' => 'non-footer-policy',
        'policy_type' => 'core',
        'status' => 'active',
        'show_in_footer' => false,
    ]);

    $response = $this->get(route('legal.index'));

    $response->assertOk();
    $response->assertSee('Footer Policy');
    $response->assertDontSee('Non Footer Policy');
});

it('shows a single active policy page', function () {
    $policy = Policy::factory()->create([
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy-test',
        'content' => '<p>Our privacy policy content.</p>',
        'policy_type' => 'core',
        'version' => '1.0',
        'status' => 'active',
        'show_in_footer' => true,
    ]);

    $response = $this->get(route('legal.show', $policy));

    $response->assertOk();
    $response->assertSee('Privacy Policy');
    $response->assertSee('Our privacy policy content.', false);
    $response->assertSee('1.0');
});

it('returns 404 for inactive policies', function () {
    $policy = Policy::factory()->create([
        'title' => 'Hidden Policy',
        'slug' => 'hidden-policy',
        'policy_type' => 'core',
        'status' => 'inactive',
        'show_in_footer' => false,
    ]);

    $this->get(route('legal.show', $policy))->assertNotFound();
});

it('returns 404 for non-existent policy slugs', function () {
    $this->get('/legal/this-does-not-exist')->assertNotFound();
});

it('shows version history on a policy page', function () {
    $policy = Policy::factory()->create([
        'title' => 'Refund Policy',
        'slug' => 'refund-policy-test',
        'content' => '<p>Refund rules.</p>',
        'policy_type' => 'transaction',
        'version' => '2.0',
        'status' => 'active',
        'show_in_footer' => true,
    ]);

    PolicyVersion::create([
        'policy_id' => $policy->id,
        'version' => '1.0',
        'content' => '<p>Old refund rules.</p>',
        'created_at' => now()->subMonth(),
    ]);

    $response = $this->get(route('legal.show', $policy));

    $response->assertOk();
    $response->assertSee('2.0');
    $response->assertSee('Version History');
    $response->assertSee('1.0');
});
