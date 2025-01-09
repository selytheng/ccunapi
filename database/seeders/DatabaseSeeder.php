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
            RoleSeeder::class,
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super admin',
            'email' => 'admin@admin.com',
            "password" => "admin@2984923478",
            "role_id" => 1
        ]);
        User::factory()->create([
            'name' => 'ITC',
            'email' => 'ITC@email.com',
            "password" => "12345678",
            "role_id" => 2
        ]);
    }
}
