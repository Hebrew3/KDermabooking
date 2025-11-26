<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@kderma.com'],
            [
                'first_name' => 'Admin',
                'middle_name' => null,
                'last_name' => 'User',
                'gender' => 'other',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'mobile_number' => '+1-800-ADMIN-01',
                'address' => '1 Admin Plaza, Management District, K-Derma City, KD 10001',
                'birth_date' => '1980-01-01',
                'email_verified_at' => now(),
            ]
        );

        // Create Nurse User
        User::firstOrCreate(
            ['email' => 'nurse@kderma.com'],
            [
                'first_name' => 'Nurse',
                'middle_name' => null,
                'last_name' => 'Member',
                'gender' => 'other',
                'password' => Hash::make('password'),
                'role' => 'nurse',
                'mobile_number' => '+1-555-NURS-001',
                'address' => '123 Clinic Street, Medical District, KD 20001',
                'birth_date' => '1990-05-10',
                'email_verified_at' => now(),
            ]
        );

        // Create Aesthetician User
        User::firstOrCreate(
            ['email' => 'aesthetician@kderma.com'],
            [
                'first_name' => 'Aesthetician',
                'middle_name' => null,
                'last_name' => 'Member',
                'gender' => 'other',
                'password' => Hash::make('password'),
                'role' => 'aesthetician',
                'mobile_number' => '+1-555-AEST-001',
                'address' => '123 Clinic Street, Medical District, KD 20001',
                'birth_date' => '1990-05-10',
                'email_verified_at' => now(),
            ]
        );

        // Create Client Users
        User::firstOrCreate(
            ['email' => 'client@kderma.com'],
            [
                'first_name' => 'Client',
                'middle_name' => null,
                'last_name' => 'User',
                'gender' => 'other',
                'password' => Hash::make('password'),
                'role' => 'client',
                'mobile_number' => '+1-555-CLIE-001',
                'address' => '100 Main Street, Residential Area, KD 30001',
                'birth_date' => '1992-07-20',
                'email_verified_at' => now(),
            ]
        );
    }
}
