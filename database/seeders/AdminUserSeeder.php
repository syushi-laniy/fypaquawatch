<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@aquawatch.test'],
            [
                'name' => 'Admin Aquawatch',
                'password' => Hash::make('Aquawatch@123'),
                'role' => 'admin',
            ]
        );
    }
}
