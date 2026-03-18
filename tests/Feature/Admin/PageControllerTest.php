<?php

use App\Models\Page;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view pages index', function () {
    Page::factory()->create(['key' => 'about', 'title' => 'About Us']);

    $response = $this->get(route('admin.pages.index'));

    $response->assertSuccessful();
    $response->assertSee('Pages');
    $response->assertSee('About Us');
});

test('admin can view page edit form', function () {
    $page = Page::factory()->create(['key' => 'help', 'title' => 'Help']);

    $response = $this->get(route('admin.pages.edit', $page));

    $response->assertSuccessful();
    $response->assertSee('Help');
});

test('admin can update page content', function () {
    $page = Page::factory()->create(['key' => 'about', 'title' => 'About Us', 'content' => 'Old content']);

    $response = $this->put(route('admin.pages.update', $page), [
        'title' => 'About Us',
        'content' => 'Updated about content.',
    ]);

    $response->assertRedirect(route('admin.pages.edit', $page));
    $response->assertSessionHas('success');
    expect($page->fresh()->content)->toBe('Updated about content.');
});

test('admin can update page title', function () {
    $page = Page::factory()->create(['key' => 'contact', 'title' => 'Contact']);

    $this->put(route('admin.pages.update', $page), [
        'title' => 'Contact Us',
        'content' => '',
    ]);

    expect($page->fresh()->title)->toBe('Contact Us');
});

test('admin can toggle page visibility', function () {
    $page = Page::factory()->create(['is_published' => true]);

    $this->put(route('admin.pages.update', $page), [
        'title' => $page->title,
        'content' => $page->content,
    ]);

    expect($page->fresh()->is_published)->toBeFalse();
});

test('admin cannot update page without title', function () {
    $page = Page::factory()->create();

    $response = $this->put(route('admin.pages.update', $page), [
        'title' => '',
        'content' => 'Some content',
    ]);

    $response->assertSessionHasErrors(['title']);
});
