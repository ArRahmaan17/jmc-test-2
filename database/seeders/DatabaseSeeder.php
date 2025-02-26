<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username' => 'dev.rahmaan',
            'email' => 'test@example.com',
            'name' => 'Ardhi Rahmaan',
            'password' => 'mamanrecing',
            'role' => 'Admin',
        ]);
    }
}
