<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view category edit page', function () {
    $category = Category::factory()->create();

    $response = $this->get(route('admin.categories.edit', $category));

    $response->assertSuccessful();
    $response->assertSee($category->name);
});

test('admin can update a category', function () {
    $category = Category::factory()->create(['name' => 'Old Name']);

    $response = $this->put(route('admin.categories.update', $category), [
        'name' => 'Updated Name',
        'slug' => 'updated-name',
        'description' => 'Updated description',
        'sort_order' => 5,
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $category->refresh();
    expect($category->name)->toBe('Updated Name');
    expect($category->slug)->toBe('updated-name');
    expect($category->description)->toBe('Updated description');
    expect($category->sort_order)->toBe(5);
});

test('admin can update a category with parent', function () {
    $parent = Category::factory()->create(['name' => 'Parent']);
    $category = Category::factory()->create(['name' => 'Child']);

    $response = $this->put(route('admin.categories.update', $category), [
        'parent_id' => $parent->id,
        'name' => 'Updated Child',
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $category->refresh();
    expect($category->parent_id)->toBe($parent->id);
    expect($category->level)->toBe(2);
});

test('admin can update a category with image', function () {
    Storage::fake('public');

    $category = Category::factory()->create();

    $response = $this->put(route('admin.categories.update', $category), [
        'name' => $category->name,
        'image' => UploadedFile::fake()->image('category.jpg', 200, 200),
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $category->refresh();
    expect($category->image)->not->toBeNull();
    Storage::disk('public')->assertExists($category->image);
});

test('category update validates required name', function () {
    $category = Category::factory()->create();

    $response = $this->put(route('admin.categories.update', $category), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('category update validates unique slug', function () {
    $existing = Category::factory()->create(['slug' => 'taken-slug']);
    $category = Category::factory()->create();

    $response = $this->put(route('admin.categories.update', $category), [
        'name' => $category->name,
        'slug' => 'taken-slug',
    ]);

    $response->assertSessionHasErrors('slug');
});

test('category update allows keeping own slug', function () {
    $category = Category::factory()->create(['slug' => 'my-slug']);

    $response = $this->put(route('admin.categories.update', $category), [
        'name' => 'Updated Name',
        'slug' => 'my-slug',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionDoesntHaveErrors();
});

test('admin can delete a category', function () {
    $category = Category::factory()->create();
    $categoryId = $category->id;

    $response = $this->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    expect(Category::find($categoryId))->toBeNull();
});

test('deleting a parent category cascades to children', function () {
    $parent = Category::factory()->create(['name' => 'Parent']);
    $child = Category::factory()->create(['name' => 'Child', 'parent_id' => $parent->id, 'level' => 2]);

    $this->delete(route('admin.categories.destroy', $parent));

    expect(Category::find($parent->id))->toBeNull();
    expect(Category::find($child->id))->toBeNull();
});
