<?php

use App\Models\BlogPost;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view blog posts index', function () {
    BlogPost::factory()->create(['title' => 'Hello World']);

    $response = $this->get(route('admin.blog-posts.index'));

    $response->assertSuccessful();
    $response->assertSee('Blog Posts');
    $response->assertSee('Hello World');
});

test('admin can filter blog posts by search', function () {
    BlogPost::factory()->create(['title' => 'Laravel Tips']);
    BlogPost::factory()->create(['title' => 'Vue Guide']);

    $response = $this->get(route('admin.blog-posts.index', ['search' => 'Laravel']));

    $response->assertSuccessful();
    $response->assertSee('Laravel Tips');
    $response->assertDontSee('Vue Guide');
});

test('admin can view create blog post form', function () {
    $response = $this->get(route('admin.blog-posts.create'));

    $response->assertSuccessful();
    $response->assertSee('Create Blog Post');
});

test('admin can create a blog post as draft', function () {
    $response = $this->post(route('admin.blog-posts.store'), [
        'title' => 'My First Post',
        'content' => 'Some content here.',
    ]);

    $response->assertRedirect(route('admin.blog-posts.index'));
    $response->assertSessionHas('success');

    $post = BlogPost::where('title', 'My First Post')->first();
    expect($post)->not->toBeNull();
    expect($post->is_published)->toBeFalse();
    expect($post->published_at)->toBeNull();
    expect($post->slug)->toBe('my-first-post');
});

test('admin can create a published blog post', function () {
    $response = $this->post(route('admin.blog-posts.store'), [
        'title' => 'Published Post',
        'content' => 'Content here.',
        'is_published' => '1',
    ]);

    $response->assertRedirect(route('admin.blog-posts.index'));

    $post = BlogPost::where('title', 'Published Post')->first();
    expect($post->is_published)->toBeTrue();
    expect($post->published_at)->not->toBeNull();
});

test('admin cannot create blog post without required fields', function () {
    $response = $this->post(route('admin.blog-posts.store'), []);

    $response->assertSessionHasErrors(['title', 'content']);
});

test('admin can view edit blog post form', function () {
    $post = BlogPost::factory()->create(['title' => 'Edit Me']);

    $response = $this->get(route('admin.blog-posts.edit', $post));

    $response->assertSuccessful();
    $response->assertSee('Edit Me');
});

test('admin can update a blog post', function () {
    $post = BlogPost::factory()->create(['title' => 'Old Title', 'content' => 'Old content']);

    $response = $this->put(route('admin.blog-posts.update', $post), [
        'title' => 'New Title',
        'content' => 'New content',
    ]);

    $response->assertRedirect(route('admin.blog-posts.edit', $post));
    $response->assertSessionHas('success');
    expect($post->fresh()->title)->toBe('New Title');
});

test('publishing a post sets published_at', function () {
    $post = BlogPost::factory()->create(['is_published' => false, 'published_at' => null]);

    $this->put(route('admin.blog-posts.update', $post), [
        'title' => $post->title,
        'content' => $post->content,
        'is_published' => '1',
    ]);

    expect($post->fresh()->is_published)->toBeTrue();
    expect($post->fresh()->published_at)->not->toBeNull();
});

test('unpublishing a post clears published_at', function () {
    $post = BlogPost::factory()->published()->create();

    $this->put(route('admin.blog-posts.update', $post), [
        'title' => $post->title,
        'content' => $post->content,
    ]);

    expect($post->fresh()->is_published)->toBeFalse();
    expect($post->fresh()->published_at)->toBeNull();
});

test('admin can delete a blog post', function () {
    $post = BlogPost::factory()->create();

    $response = $this->delete(route('admin.blog-posts.destroy', $post));

    $response->assertRedirect(route('admin.blog-posts.index'));
    $response->assertSessionHas('success');
    expect(BlogPost::find($post->id))->toBeNull();
});
