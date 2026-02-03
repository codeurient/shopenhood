<?php

use App\Models\Location;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

// ==========================================
// COUNTRY CRUD
// ==========================================

test('admin can view locations index', function () {
    $country = Location::factory()->create();

    $response = $this->get(route('admin.locations.index'));

    $response->assertSuccessful();
    $response->assertSee($country->name);
});

test('admin can search countries', function () {
    Location::factory()->create(['name' => 'Azerbaijan']);
    Location::factory()->create(['name' => 'Turkey']);

    $response = $this->get(route('admin.locations.index', ['search' => 'Azer']));

    $response->assertSuccessful();
    $response->assertSee('Azerbaijan');
    $response->assertDontSee('Turkey');
});

test('admin can filter countries by active status', function () {
    Location::factory()->create(['name' => 'Active Country', 'is_active' => true]);
    Location::factory()->inactive()->create(['name' => 'Inactive Country']);

    $response = $this->get(route('admin.locations.index', ['is_active' => '0']));

    $response->assertSuccessful();
    $response->assertSee('Inactive Country');
    $response->assertDontSee('Active Country');
});

test('admin can view country create page', function () {
    $response = $this->get(route('admin.locations.create'));

    $response->assertSuccessful();
});

test('admin can store a country', function () {
    $response = $this->post(route('admin.locations.store'), [
        'name' => 'Germany',
        'code' => 'DE',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.locations.index'));
    $response->assertSessionHas('success');

    $country = Location::where('name', 'Germany')->first();
    expect($country)->not->toBeNull();
    expect($country->type)->toBe('country');
    expect($country->code)->toBe('DE');
    expect($country->is_active)->toBeTrue();
});

test('store country requires name', function () {
    $response = $this->post(route('admin.locations.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('store country rejects duplicate name', function () {
    Location::factory()->create(['name' => 'France']);

    $response = $this->post(route('admin.locations.store'), [
        'name' => 'France',
    ]);

    $response->assertSessionHasErrors('name');
});

test('admin can view country edit page', function () {
    $country = Location::factory()->create();

    $response = $this->get(route('admin.locations.edit', $country));

    $response->assertSuccessful();
    $response->assertSee($country->name);
});

test('admin can update a country', function () {
    $country = Location::factory()->create(['name' => 'Old Name']);

    $response = $this->put(route('admin.locations.update', $country), [
        'name' => 'New Name',
        'code' => 'NN',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.locations.index'));
    $response->assertSessionHas('success');

    $country->refresh();
    expect($country->name)->toBe('New Name');
    expect($country->code)->toBe('NN');
});

test('update country rejects duplicate name', function () {
    Location::factory()->create(['name' => 'Existing']);
    $country = Location::factory()->create(['name' => 'Original']);

    $response = $this->put(route('admin.locations.update', $country), [
        'name' => 'Existing',
    ]);

    $response->assertSessionHasErrors('name');
});

test('update country allows keeping own name', function () {
    $country = Location::factory()->create(['name' => 'My Country']);

    $response = $this->put(route('admin.locations.update', $country), [
        'name' => 'My Country',
    ]);

    $response->assertRedirect(route('admin.locations.index'));
    $response->assertSessionDoesntHaveErrors();
});

test('admin can delete a country', function () {
    $country = Location::factory()->create();
    $countryId = $country->id;

    $response = $this->delete(route('admin.locations.destroy', $country));

    $response->assertRedirect(route('admin.locations.index'));
    $response->assertSessionHas('success');

    expect(Location::find($countryId))->toBeNull();
});

test('deleting a country cascades to its cities', function () {
    $country = Location::factory()->create();
    $city = Location::factory()->city($country->id)->create();

    $this->delete(route('admin.locations.destroy', $country));

    expect(Location::find($country->id))->toBeNull();
    expect(Location::find($city->id))->toBeNull();
});

test('admin can toggle country status', function () {
    $country = Location::factory()->create(['is_active' => true]);

    $response = $this->patch(route('admin.locations.toggle-status', $country));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $country->refresh();
    expect($country->is_active)->toBeFalse();
});

// ==========================================
// CITY CRUD (nested under country)
// ==========================================

test('admin can view cities index', function () {
    $country = Location::factory()->create();
    $city = Location::factory()->city($country->id)->create();

    $response = $this->get(route('admin.locations.cities.index', $country));

    $response->assertSuccessful();
    $response->assertSee($city->name);
});

test('admin can view city create page', function () {
    $country = Location::factory()->create();

    $response = $this->get(route('admin.locations.cities.create', $country));

    $response->assertSuccessful();
});

test('admin can store a city', function () {
    $country = Location::factory()->create();

    $response = $this->post(route('admin.locations.cities.store', $country), [
        'name' => 'Baku',
        'code' => 'BAK',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.locations.cities.index', $country));
    $response->assertSessionHas('success');

    $city = Location::where('name', 'Baku')->where('type', 'city')->first();
    expect($city)->not->toBeNull();
    expect($city->parent_id)->toBe($country->id);
    expect($city->code)->toBe('BAK');
});

test('store city requires name', function () {
    $country = Location::factory()->create();

    $response = $this->post(route('admin.locations.cities.store', $country), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('store city rejects duplicate name within same country', function () {
    $country = Location::factory()->create();
    Location::factory()->city($country->id)->create(['name' => 'Baku']);

    $response = $this->post(route('admin.locations.cities.store', $country), [
        'name' => 'Baku',
    ]);

    $response->assertSessionHasErrors('name');
});

test('store city allows same name in different country', function () {
    $country1 = Location::factory()->create();
    $country2 = Location::factory()->create();
    Location::factory()->city($country1->id)->create(['name' => 'Springfield']);

    $response = $this->post(route('admin.locations.cities.store', $country2), [
        'name' => 'Springfield',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.locations.cities.index', $country2));
    $response->assertSessionDoesntHaveErrors();
});

test('admin can view city edit page', function () {
    $country = Location::factory()->create();
    $city = Location::factory()->city($country->id)->create();

    $response = $this->get(route('admin.locations.cities.edit', [$country, $city->id]));

    $response->assertSuccessful();
    $response->assertSee($city->name);
});

test('admin can update a city', function () {
    $country = Location::factory()->create();
    $city = Location::factory()->city($country->id)->create(['name' => 'Old City']);

    $response = $this->put(route('admin.locations.cities.update', [$country, $city->id]), [
        'name' => 'New City',
        'code' => 'NC',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.locations.cities.index', $country));
    $response->assertSessionHas('success');

    $city->refresh();
    expect($city->name)->toBe('New City');
    expect($city->code)->toBe('NC');
});

test('update city rejects duplicate name within same country', function () {
    $country = Location::factory()->create();
    Location::factory()->city($country->id)->create(['name' => 'Existing City']);
    $city = Location::factory()->city($country->id)->create(['name' => 'Original City']);

    $response = $this->put(route('admin.locations.cities.update', [$country, $city->id]), [
        'name' => 'Existing City',
    ]);

    $response->assertSessionHasErrors('name');
});

test('admin can delete a city', function () {
    $country = Location::factory()->create();
    $city = Location::factory()->city($country->id)->create();
    $cityId = $city->id;

    $response = $this->delete(route('admin.locations.cities.destroy', [$country, $city->id]));

    $response->assertRedirect(route('admin.locations.cities.index', $country));
    $response->assertSessionHas('success');

    expect(Location::find($cityId))->toBeNull();
});

test('admin can toggle city status', function () {
    $country = Location::factory()->create();
    $city = Location::factory()->city($country->id)->create(['is_active' => true]);

    $response = $this->patch(route('admin.locations.cities.toggle-status', [$country, $city->id]));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $city->refresh();
    expect($city->is_active)->toBeFalse();
});

test('city edit rejects city that does not belong to country', function () {
    $country1 = Location::factory()->create();
    $country2 = Location::factory()->create();
    $city = Location::factory()->city($country2->id)->create();

    $response = $this->get(route('admin.locations.cities.edit', [$country1, $city->id]));

    $response->assertNotFound();
});
