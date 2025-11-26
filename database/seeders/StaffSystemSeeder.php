<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\StaffService;
use App\Models\StaffSpecialization;
use Illuminate\Support\Facades\Hash;

class StaffSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffProfiles = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@kderma.com',
                'birth_date' => '1990-03-15',
                'role' => 'nurse',
                'gender' => 'female',
                'mobile_number' => '09170000001',
                'address' => 'Makati City, Philippines',
                'specializations' => [
                    ['name' => 'facial_treatments', 'level' => 'expert', 'primary' => true],
                    ['name' => 'anti_aging', 'level' => 'advanced'],
                    ['name' => 'acne_treatment', 'level' => 'advanced'],
                ],
                'schedule' => [
                    'monday' => ['start' => '09:00', 'end' => '17:00'],
                    'tuesday' => null,
                    'wednesday' => null,
                    'thursday' => ['start' => '12:00', 'end' => '20:00'],
                    'friday' => ['start' => '09:00', 'end' => '17:00'],
                    'saturday' => ['start' => '10:00', 'end' => '16:00'],
                    'sunday' => ['start' => '10:00', 'end' => '16:00'],
                ],
                'service_categories' => ['facial', 'peel'],
            ],
            [
                'first_name' => 'Dr. Sofia',
                'last_name' => 'Martinez',
                'email' => 'sofia.martinez@kderma.com',
                'birth_date' => '1985-09-12',
                'role' => 'nurse',
                'gender' => 'female',
                'mobile_number' => '09170000002',
                'address' => 'Quezon City, Philippines',
                'specializations' => [
                    ['name' => 'medical_procedures', 'level' => 'expert', 'primary' => true],
                    ['name' => 'laser_therapy', 'level' => 'advanced'],
                    ['name' => 'consultation', 'level' => 'advanced'],
                ],
                'schedule' => [
                    'monday' => ['start' => '11:00', 'end' => '19:00'],
                    'tuesday' => ['start' => '11:00', 'end' => '19:00'],
                    'wednesday' => ['start' => '09:00', 'end' => '17:00'],
                    'thursday' => null,
                    'friday' => null,
                    'saturday' => ['start' => '09:00', 'end' => '18:00'],
                    'sunday' => ['start' => '09:00', 'end' => '18:00'],
                ],
                'service_categories' => ['laser', 'injection', 'consultation'],
            ],
            [
                'first_name' => 'Jasmine',
                'last_name' => 'Reyes',
                'email' => 'jasmine.reyes@kderma.com',
                'birth_date' => '1994-01-22',
                'role' => 'aesthetician',
                'gender' => 'female',
                'mobile_number' => '09170000003',
                'address' => 'Pasig City, Philippines',
                'specializations' => [
                    ['name' => 'body_treatments', 'level' => 'advanced', 'primary' => true],
                    ['name' => 'facial_treatments', 'level' => 'advanced'],
                ],
                'schedule' => [
                    'monday' => ['start' => '08:00', 'end' => '16:00'],
                    'tuesday' => ['start' => '08:00', 'end' => '16:00'],
                    'wednesday' => ['start' => '12:00', 'end' => '20:00'],
                    'thursday' => ['start' => '12:00', 'end' => '20:00'],
                    'friday' => ['start' => '08:00', 'end' => '16:00'],
                    'saturday' => null,
                    'sunday' => null,
                ],
                'service_categories' => ['facial', 'peel'],
            ],
            [
                'first_name' => 'Ethan',
                'last_name' => 'Castillo',
                'email' => 'ethan.castillo@kderma.com',
                'birth_date' => '1992-06-05',
                'role' => 'aesthetician',
                'gender' => 'male',
                'mobile_number' => '09170000004',
                'address' => 'Taguig City, Philippines',
                'specializations' => [
                    ['name' => 'laser_therapy', 'level' => 'advanced', 'primary' => true],
                    ['name' => 'acne_treatment', 'level' => 'intermediate'],
                ],
                'schedule' => [
                    'monday' => null,
                    'tuesday' => ['start' => '10:00', 'end' => '18:00'],
                    'wednesday' => ['start' => '10:00', 'end' => '18:00'],
                    'thursday' => ['start' => '10:00', 'end' => '18:00'],
                    'friday' => ['start' => '12:00', 'end' => '20:00'],
                    'saturday' => ['start' => '08:00', 'end' => '14:00'],
                    'sunday' => ['start' => '08:00', 'end' => '14:00'],
                ],
                'service_categories' => ['laser', 'facial'],
            ],
        ];

        $daysOfWeek = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        foreach ($staffProfiles as $profile) {
            $staffUser = User::updateOrCreate(
                ['email' => $profile['email']],
                [
                    'first_name' => $profile['first_name'],
                    'middle_name' => $profile['middle_name'] ?? null,
                    'last_name' => $profile['last_name'],
                    'gender' => $profile['gender'] ?? 'female',
                    'password' => Hash::make($profile['password'] ?? 'Password!123'),
                    'role' => $profile['role'],
                    'mobile_number' => $profile['mobile_number'] ?? '09' . rand(100000000, 999999999),
                    'address' => $profile['address'] ?? 'Metro Manila, Philippines',
                    'birth_date' => $profile['birth_date'],
                    'email_verified_at' => now(),
                ]
            );

            // Specializations
            foreach ($profile['specializations'] as $specialization) {
                StaffSpecialization::updateOrCreate(
                    [
                        'staff_id' => $staffUser->id,
                        'specialization' => $specialization['name'],
                    ],
                    [
                        'proficiency_level' => $specialization['level'],
                        'is_primary' => (bool) ($specialization['primary'] ?? false),
                    ]
                );
            }

            // Schedules
            foreach ($daysOfWeek as $day) {
                $window = $profile['schedule'][$day] ?? null;
                StaffSchedule::updateOrCreate(
                    [
                        'staff_id' => $staffUser->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => $window['start'] ?? null,
                        'end_time' => $window['end'] ?? null,
                        'is_available' => $window !== null,
                        'notes' => $window ? ($window['notes'] ?? 'Regular availability') : 'Scheduled day off',
                    ]
                );
            }

            // Assign core services based on configured categories
            $serviceCategories = $profile['service_categories'] ?? [];
            if (!empty($serviceCategories)) {
                $services = Service::whereIn('category', $serviceCategories)->get();
                foreach ($services as $index => $service) {
                    StaffService::firstOrCreate(
                        [
                            'staff_id' => $staffUser->id,
                            'service_id' => $service->id,
                        ],
                        [
                            'is_primary' => $index === 0,
                            'proficiency_level' => 4,
                            'notes' => $index === 0 ? 'Primary service from seeder' : 'Auto-assigned via seeder',
                        ]
                    );
                }
            }

            // Ensure nurses can administer Gluta
            if ($staffUser->role === 'nurse') {
                $glutaService = Service::where('name', 'Gluta')->first();
                if ($glutaService) {
                    StaffService::firstOrCreate(
                        [
                            'staff_id' => $staffUser->id,
                            'service_id' => $glutaService->id,
                        ],
                        [
                            'is_primary' => true,
                            'proficiency_level' => 5,
                            'notes' => 'Auto-assigned Gluta service for nurses',
                        ]
                    );
                }
            }
        }

        $this->command->info('Staff system seeded successfully!');
        $this->command->info('Created/updated '.count($staffProfiles).' staff members with schedules, specializations, and baseline service coverage.');
    }
}
