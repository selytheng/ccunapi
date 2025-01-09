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
            'name' => 'LyTheng Se',
            'email' => 'kh.lytheng@gmail.com',
            "password"=> "test@123",
            "role_id"=> 1
        ]); 
        User::factory()->create([
            'name' => 'User69',
            'email' => 'user69@email.com',
            "password"=> "12345678",
            "role_id"=> 2
        ]); 
    }
}
