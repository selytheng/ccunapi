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
            ['id' => 2, 'name' => 'RUPP'],
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
