<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => env('ADMIN_NAME', 'Admin Default'),
            'email' => env('ADMIN_EMAIL', 'admin@default.com'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'bhahaha1234')),
            'role' => 'admin',
        ]);
    }
}
