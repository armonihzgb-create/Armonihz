<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
        ['email' => 'armonihzgb@gmail.com'],
        [
            'name' => 'Armonihz Admin',
            'email' => 'armonihzgb@gmail.com',
            'password' => Hash::make('Armonihz2006@'),
            'role' => 'admin',
            'is_active' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]
        );
    }
}
