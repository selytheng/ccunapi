<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DBSeeder::class,
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super admin',
            'email' => 'admin@admin.com',
            "password" => "12345678",
            "role_id" => 1,
            "partner_id" => 1
        ]);
    }
}
