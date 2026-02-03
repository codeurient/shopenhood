<?php

use App\Models\Location;

test('api returns countries with their cities', function () {
    $country = Location::factory()->create(['name' => 'Azerbaijan']);
    Location::factory()->city($country->id)->create(['name' => 'Baku']);
    Location::factory()->city($country->id)->create(['name' => 'Ganja']);

    $response = $this->getJson(route('api.locations.countries-cities'));

    $response->assertSuccessful();
    $response->assertJsonStructure(['Azerbaijan']);

    $data = $response->json();
    expect($data['Azerbaijan'])->toContain('Baku');
    expect($data['Azerbaijan'])->toContain('Ganja');
});

test('api excludes inactive countries', function () {
    Location::factory()->create(['name' => 'Active Country']);
    Location::factory()->inactive()->create(['name' => 'Inactive Country']);

    $response = $this->getJson(route('api.locations.countries-cities'));

    $data = $response->json();
    expect($data)->toHaveKey('Active Country');
    expect($data)->not->toHaveKey('Inactive Country');
});

test('api excludes inactive cities', function () {
    $country = Location::factory()->create(['name' => 'TestCountry']);
    Location::factory()->city($country->id)->create(['name' => 'Active City', 'is_active' => true]);
    Location::factory()->city($country->id)->inactive()->create(['name' => 'Inactive City']);

    $response = $this->getJson(route('api.locations.countries-cities'));

    $data = $response->json();
    expect($data['TestCountry'])->toContain('Active City');
    expect($data['TestCountry'])->not->toContain('Inactive City');
});

test('api returns empty object when no locations exist', function () {
    $response = $this->getJson(route('api.locations.countries-cities'));

    $response->assertSuccessful();
    $response->assertJson([]);
});

test('api returns cities sorted by name', function () {
    $country = Location::factory()->create(['name' => 'SortCountry']);
    Location::factory()->city($country->id)->create(['name' => 'Zebra City']);
    Location::factory()->city($country->id)->create(['name' => 'Alpha City']);
    Location::factory()->city($country->id)->create(['name' => 'Middle City']);

    $response = $this->getJson(route('api.locations.countries-cities'));

    $data = $response->json();
    expect($data['SortCountry'])->toBe(['Alpha City', 'Middle City', 'Zebra City']);
});
