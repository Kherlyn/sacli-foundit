<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@saclifoundit.com'],
            [
                'name' => 'SACLI Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create a test regular user
        \App\Models\User::firstOrCreate(
            ['email' => 'user@saclifoundit.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@saclifoundit.com / admin123');
        $this->command->info('Test user created: user@saclifoundit.com / user123');
    }
}
