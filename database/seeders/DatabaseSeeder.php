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
        // Call the seeders to create users and services
        $this->call([
            UserRoleSeeder::class,
            ServiceSeeder::class,
            StaffSystemSeeder::class,
            StaffServiceAssignmentSeeder::class,
            InventorySeeder::class,
            AppointmentSeeder::class,
        ]);

        // Create additional test users using factory
        // User::factory(10)->create();

        // Create a specific test user
        User::factory()->create([
            'first_name' => 'Test',
            'middle_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'gender' => 'other',
            'mobile_number' => '+1234567890',
            'address' => '123 Test Street, Demo City, DC 12345',
            'birth_date' => '1995-06-15',
            'role' => 'client',
        ]);
    }
}
