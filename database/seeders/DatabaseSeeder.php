<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin account if it doesn't exist yet
        User::firstOrCreate(
            ['email' => 'admin@capacityconnect.com'],
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );
    }
}

