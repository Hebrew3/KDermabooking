<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\StaffService;

class StaffServiceAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all staff members (nurse and aesthetician)
        $staffMembers = User::whereIn('role', ['nurse', 'aesthetician'])->get();
        
        // Get all active services
        $services = Service::active()->get();

        foreach ($staffMembers as $staff) {
            $staffSpecializations = $staff->getSpecializations();
            $assignedCount = 0;
            
            foreach ($services as $service) {
                // Check if staff can perform this service
                if ($service->canBePerformedBy($staff)) {
                    if (
                        rand(1, 100) <= 60 &&
                        $assignedCount < 8 &&
                        !StaffService::where('staff_id', $staff->id)->where('service_id', $service->id)->exists()
                    ) {
                        $isPrimary = $assignedCount < 2; // First 2 services are primary
                        
                        // Calculate custom price (30% chance of having a custom price)
                        $customPrice = null;
                        if (rand(1, 100) <= 30) {
                            try {
                                // Get base price as float and calculate custom price (80-120% of base)
                                $basePrice = (float) $service->price;
                                
                                // Validate base price is reasonable
                                if ($basePrice <= 0 || $basePrice > 99999999.99) {
                                    $this->command->warn("Skipping custom price for service '{$service->name}' - invalid base price: {$basePrice}");
                                    $customPrice = null;
                                } else {
                                    $multiplier = rand(80, 120) / 100;
                                    $calculatedPrice = round($basePrice * $multiplier, 2);
                                    
                                    // Ensure the price is within the decimal(10,2) limit (max 99,999,999.99)
                                    $customPrice = min($calculatedPrice, 99999999.99);
                                    $customPrice = max($customPrice, 0); // Ensure non-negative
                                }
                            } catch (\Exception $e) {
                                $this->command->warn("Error calculating custom price for service '{$service->name}': " . $e->getMessage());
                                $customPrice = null;
                            }
                        }
                        
                        try {
                            StaffService::create([
                                'staff_id' => $staff->id,
                                'service_id' => $service->id,
                                'is_primary' => $isPrimary,
                                'custom_price' => $customPrice,
                                'proficiency_level' => rand(2, 5), // Random proficiency 2-5
                                'notes' => $isPrimary ? 'Primary service - high expertise' : null,
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            $isDuplicate = str_contains($e->getMessage(), 'staff_services_staff_id_service_id_unique');
                            $isPriceIssue = str_contains($e->getMessage(), 'custom_price') || str_contains($e->getMessage(), 'Numeric value out of range');

                            if ($isDuplicate) {
                                $this->command->warn("Skipped duplicate service {$service->name} for {$staff->name}");
                                continue;
                            }

                            if ($isPriceIssue) {
                                $this->command->warn("Invalid custom price for {$service->name} ({$customPrice}). Retrying without custom price.");
                                StaffService::updateOrCreate(
                                    [
                                        'staff_id' => $staff->id,
                                        'service_id' => $service->id,
                                    ],
                                    [
                                        'is_primary' => $isPrimary,
                                        'custom_price' => null,
                                        'proficiency_level' => rand(2, 5),
                                        'notes' => $isPrimary ? 'Primary service - high expertise' : null,
                                    ]
                                );
                            } else {
                                throw $e;
                            }
                        }
                        
                        $assignedCount++;
                    }
                }
            }
            
            $this->command->info("Assigned {$assignedCount} services to {$staff->first_name} {$staff->last_name}");
        }
    }
}
