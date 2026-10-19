<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Budi Super Admin',
            'email' => 'superadmin@anri.go.id',
            'password' => Hash::make('password123'), 
            'role' => 'super_admin',
            'unit_kerja' => 'Pusat Data Kearsipan'
        ]);
        User::create([
            'name' => 'Rina Admin',
            'email' => 'admin@anri.go.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'unit_kerja' => 'Divisi Pengawasan'
        ]);
    }
}