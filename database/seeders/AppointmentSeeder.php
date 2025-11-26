<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and services
        $clients = User::where('role', 'client')->get();
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->get();
        $services = Service::all();

        if ($clients->isEmpty() || $staff->isEmpty() || $services->isEmpty()) {
            $this->command->info('Skipping appointment seeder - missing required data (users or services)');
            return;
        }

        // Create sample appointments with proper time format
        $appointments = [
            [
                'client_id' => $clients->random()->id,
                'service_id' => $services->random()->id,
                'staff_id' => $staff->random()->id,
                'appointment_date' => now()->addDays(1),
                'appointment_time' => '09:00',
                'status' => 'confirmed',
                'total_amount' => 1500.00,
                'payment_status' => 'paid',
                'notes' => 'Regular facial treatment appointment',
            ],
            [
                'client_id' => $clients->random()->id,
                'service_id' => $services->random()->id,
                'staff_id' => $staff->random()->id,
                'appointment_date' => now()->addDays(2),
                'appointment_time' => '14:30',
                'status' => 'pending',
                'total_amount' => 2500.00,
                'payment_status' => 'unpaid',
                'notes' => 'Body treatment session',
            ],
            [
                'client_id' => $clients->random()->id,
                'service_id' => $services->random()->id,
                'staff_id' => $staff->random()->id,
                'appointment_date' => now()->subDays(1),
                'appointment_time' => '11:00',
                'status' => 'completed',
                'total_amount' => 3000.00,
                'payment_status' => 'paid',
                'notes' => 'Laser therapy session completed',
                'completed_at' => now()->subDays(1)->addHours(2),
            ],
            [
                'client_id' => $clients->random()->id,
                'service_id' => $services->random()->id,
                'staff_id' => $staff->random()->id,
                'appointment_date' => now(),
                'appointment_time' => '16:00',
                'status' => 'confirmed',
                'total_amount' => 1800.00,
                'payment_status' => 'partial',
                'notes' => 'Chemical peel treatment today',
            ],
            [
                'client_id' => $clients->random()->id,
                'service_id' => $services->random()->id,
                'staff_id' => $staff->random()->id,
                'appointment_date' => now()->addDays(7),
                'appointment_time' => '10:30',
                'status' => 'pending',
                'total_amount' => 2200.00,
                'payment_status' => 'unpaid',
                'notes' => 'Follow-up consultation',
            ],
        ];

        foreach ($appointments as $appointmentData) {
            Appointment::create($appointmentData);
        }

        $this->command->info('Created ' . count($appointments) . ' sample appointments');
    }
}
