<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = public_path('json/countriesAndCities.json');

        if (! file_exists($jsonPath)) {
            $this->command->warn('countriesAndCities.json not found, skipping.');

            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data as $countryName => $cities) {
            $country = Location::updateOrCreate(
                ['name' => $countryName, 'type' => 'country'],
                ['is_active' => true]
            );

            foreach ($cities as $cityName) {
                Location::updateOrCreate(
                    ['name' => $cityName, 'type' => 'city', 'parent_id' => $country->id],
                    ['is_active' => true]
                );
            }
        }
    }
}
