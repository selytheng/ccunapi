<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DBSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'partner'],
        ]);
        DB::table('partners')->insert([
            ['id' => 1, 'name' => 'ITC'],
        ]);
        DB::table('partner_contacts')->insert([
            [
                'id' => 1,
                'partner_id' => 1,
                'phone_number' => json_encode(['(855) 12 818 830', '(855) 11 685 685']),
                'email' => json_encode(['info@itc.edu.kh']),
                'location_link' => 'https://maps.app.goo.gl/LU7LBnHQnQVf3x5JA',
                'address' => 'Room 220B, Building B, PO Box 86, Russian Conf. Blvd., Phnom Penh, Cambodia',
                'website' => 'www.itc.edu.kh',
                'moodle_link' => 'moodle.itc.edu.kh',
            ],
        ]);
        DB::table('years')->insert([
            ['id' => 1, 'name' => 'Year 1'],
            ['id' => 2, 'name' => 'Year 2'],
            ['id' => 3, 'name' => 'Year 3'],
            ['id' => 4, 'name' => 'Year 4'],
            ['id' => 5, 'name' => 'Year 5'],
        ]);
    }
}
