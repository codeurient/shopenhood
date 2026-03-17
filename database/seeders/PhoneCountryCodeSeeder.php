<?php

namespace Database\Seeders;

use App\Models\PhoneCountryCode;
use Illuminate\Database\Seeder;

class PhoneCountryCodeSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'code' => 'AD',
                'name' => 'Andorra',
                'native_name' => 'Andorra',
                'phone_code' => '376',
                'trunk_prefix' => '00',
                'idd_prefix' => null,
            ],
            [
                'code' => 'AE',
                'name' => 'United Arab Emirates',
                'native_name' => 'الإمارات العربية المتحدة',
                'phone_code' => '971',
                'trunk_prefix' => '00',
                'idd_prefix' => '0',
            ],
        ];

        foreach ($entries as $entry) {
            PhoneCountryCode::updateOrCreate(['code' => $entry['code']], $entry);
        }
    }
}
