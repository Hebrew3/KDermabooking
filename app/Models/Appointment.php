<?php

namespace App\Models;

use App\Mail\AppointmentStatusUpdatedMail;
use App\Helpers\TimeHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'service_id',
        'staff_id',
        'appointment_number',
        'appointment_date',
        'appointment_time',
        'status',
        'total_amount',
        'payment_status',
        'notes',
        'client_notes',
        'staff_notes',
        'reminder_sent',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'client_rating',
        'client_feedback',
        'admin_feedback_reply',
        'admin_feedback_replied_at',
        'walkin_customer_name',
        'walkin_customer_email',
        'walkin_customer_phone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'string',
        'total_amount' => 'decimal:2',
        'reminder_sent' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'client_rating' => 'integer',
        'admin_feedback_replied_at' => 'datetime',
    ];

    /**
     * Determine if the appointment has client feedback.
     */
    public function hasClientFeedback(): bool
    {
        return !is_null($this->client_rating) || !empty($this->client_feedback);
    }

    /**
     * Determine if an admin has replied to client feedback.
     */
    public function hasAdminFeedbackReply(): bool
    {
        return !empty($this->admin_feedback_reply);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (empty($appointment->appointment_number)) {
                $appointment->appointment_number = self::generateAppointmentNumber();
            }
        });

        static::updating(function ($appointment) {
            if ($appointment->isDirty('status')) {
                $originalStatus = $appointment->getOriginal('status');
                $newStatus = $appointment->status;
                $client = $appointment->client;

                \Log::info("Appointment status changing", [
                    'appointment_id' => $appointment->id,
                    'original_status' => $originalStatus,
                    'new_status' => $newStatus
                ]);

                // Handle inventory deduction/restoration
                if ($newStatus === 'completed' && $originalStatus !== 'completed') {
                    // Deduct inventory when appointment is completed
                    \Log::info("Triggering inventory deduction for appointment {$appointment->id}");
                    $appointment->deductTreatmentProductsInventory();
                } elseif ($originalStatus === 'completed' && $newStatus !== 'completed') {
                    // Restore inventory if appointment was completed but is now being changed to another status
                    \Log::info("Triggering inventory restoration for appointment {$appointment->id}");
                    $appointment->restoreTreatmentProductsInventory();
                }

                // Send email notification (non-blocking - don't fail appointment update if email fails)
                if ($client && $client->email) {
                    try {
                        Mail::to($client->email)->send(new AppointmentStatusUpdatedMail($appointment, $originalStatus));
                        \Log::info("Appointment status update email sent successfully to {$client->email} for appointment {$appointment->id}");
                    } catch (\Exception $e) {
                        // Log email error but don't prevent appointment update
                        \Log::error("Failed to send appointment status update email to {$client->email} for appointment {$appointment->id}: " . $e->getMessage(), [
                            'exception' => $e,
                            'appointment_id' => $appointment->id,
                            'client_email' => $client->email
                        ]);
                    }
                }
            }
        });
        
        // Removed duplicate deduction in updated event to prevent multiple deductions
    }

    /**
     * Generate a unique appointment number.
     */
    public static function generateAppointmentNumber(): string
    {
        $prefix = 'KD';
        $date = TimeHelper::now()->format('Ymd');
        
        // Get the last appointment number for today
        $lastAppointment = self::where('appointment_number', 'like', $prefix . $date . '%')
            ->orderBy('appointment_number', 'desc')
            ->first();

        if ($lastAppointment) {
            $lastNumber = (int) substr($lastAppointment->appointment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the client that owns the appointment.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Check if this is a walk-in customer appointment.
     */
    public function isWalkIn(): bool
    {
        return is_null($this->client_id);
    }

    /**
     * Get customer name (registered client or walk-in).
     */
    public function getCustomerNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->name;
        }
        return $this->walkin_customer_name ?? 'Walk-in Customer';
    }

    /**
     * Get customer email (registered client or walk-in).
     */
    public function getCustomerEmailAttribute(): ?string
    {
        if ($this->client) {
            return $this->client->email;
        }
        return $this->walkin_customer_email;
    }

    /**
     * Get customer phone (registered client or walk-in).
     */
    public function getCustomerPhoneAttribute(): ?string
    {
        if ($this->client) {
            return $this->client->mobile_number;
        }
        return $this->walkin_customer_phone;
    }

    /**
     * Get the service for this appointment (primary service - for backward compatibility).
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all services for this appointment (many-to-many).
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services')
                    ->withPivot('price')
                    ->withTimestamps();
    }

    /**
     * Get the staff member assigned to this appointment.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the formatted appointment date.
     */
    public function getFormattedDateAttribute(): string
    {
        try {
            $dateValue = $this->getAttribute('appointment_date');
            $appointmentDate = TimeHelper::parseDate($dateValue);
            
            if (!$appointmentDate) {
                return 'Date not set';
            }
            
            return $appointmentDate->format('F d, Y'); // e.g., "December 01, 2025"
        } catch (\Exception $e) {
            \Log::error('Error formatting appointment date', [
                'appointment_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Invalid date';
        }
    }

    /**
     * Get the formatted appointment time.
     */
    public function getFormattedTimeAttribute(): string
    {
        try {
            if (empty($this->appointment_time)) {
                return 'Time not set';
            }

            // Use TimeHelper to parse time
            $time = TimeHelper::parseTime($this->appointment_time);

            if ($time) {
                return $time->format('g:i A'); // e.g., "10:00 AM"
            } else {
                // Fallback: try to format as string
                // Normalize time format (remove seconds if present)
                $normalizedTime = preg_replace('/:\d{2}$/', '', $this->appointment_time);
                try {
                    $parsedTime = Carbon::createFromFormat('H:i', $normalizedTime);
                    return $parsedTime->format('g:i A');
                } catch (\Exception $e) {
                    return $this->appointment_time; // Return as-is if parsing fails
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error formatting appointment time', [
                'appointment_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Invalid time';
        }
    }

    /**
     * Get the formatted appointment date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        try {
            // Get the raw value and ensure it's a Carbon instance
            $dateValue = $this->getAttribute('appointment_date');
            
            // Use TimeHelper for consistent timezone handling
            $appointmentDate = TimeHelper::parseDate($dateValue);
            
            if (!$appointmentDate) {
                return 'Date not set';
            }
            
            // Handle various time formats safely
            if (empty($this->appointment_time)) {
                return $appointmentDate->format('M d, Y') . ' at TBD';
            }

            // Use TimeHelper to parse time
            $time = TimeHelper::parseTime($this->appointment_time);

            if ($time) {
                return $appointmentDate->format('M d, Y') . ' at ' . $time->format('g:i A');
            } else {
                // Fallback: try to parse as string
                return $appointmentDate->format('M d, Y') . ' at ' . $this->appointment_time;
            }
        } catch (\Exception $e) {
                \Log::error('Error formatting appointment date/time', [
                    'appointment_id' => $this->id,
                'error' => $e->getMessage()
                ]);
                return 'Invalid date';
        }
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        $totalAmount = $this->getAttribute('total_amount');
        $amount = $totalAmount === null ? 0.0 : (float) $totalAmount;
        return '₱' . number_format($amount, 2);
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the payment status badge color.
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'bg-red-100 text-red-800',
            'partial' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if appointment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        if (!in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }
        
        $dateValue = $this->getAttribute('appointment_date');
        if (!$dateValue) {
            return false;
        }
        
        // Use TimeHelper for consistent timezone handling
        $appointmentDate = TimeHelper::parseDate($dateValue);
        if (!$appointmentDate) {
            return false;
        }
        
        // Combine with time if available
        if (!empty($this->appointment_time)) {
            $appointmentDateTime = TimeHelper::combineDateTime($appointmentDate, $this->appointment_time);
            if ($appointmentDateTime) {
                return $appointmentDateTime->isFuture();
            }
        }
        
        return $appointmentDate->isFuture();
    }

    /**
     * Check if appointment can be rescheduled.
     */
    public function canBeRescheduled(): bool
    {
        if (!in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }
        
        $dateValue = $this->getAttribute('appointment_date');
        if (!$dateValue) {
            return false;
        }
        
        // Use TimeHelper for consistent timezone handling
        $appointmentDate = TimeHelper::parseDate($dateValue);
        if (!$appointmentDate) {
            return false;
        }
        
        // Combine with time if available
        if (!empty($this->appointment_time)) {
            $appointmentDateTime = TimeHelper::combineDateTime($appointmentDate, $this->appointment_time);
            if ($appointmentDateTime) {
                return $appointmentDateTime->isFuture();
            }
        }
        
        return $appointmentDate->isFuture();
    }

    /**
     * Check if appointment is 15 minutes late or more.
     */
    public function isLate(): bool
    {
        // Only check for pending appointments
        if ($this->status !== 'pending') {
            return false;
        }

        $appointmentDateTime = $this->getAppointmentDateTime();
        if (!$appointmentDateTime) {
            return false;
        }

        // Use TimeHelper for consistent timezone handling
        $now = TimeHelper::now();
        
        // Check if appointment is today
        if (!$appointmentDateTime->isToday()) {
            return false;
        }

        // Check if appointment is 15 minutes or more late
        $minutesLate = $now->diffInMinutes($appointmentDateTime, false);
        return $minutesLate <= -15;
    }

    /**
     * Get the number of minutes the appointment is late.
     * Returns 0 if not late, or negative number if late.
     */
    public function getMinutesLate(): int
    {
        if (!$this->isLate()) {
            return 0;
        }

        $appointmentDateTime = $this->getAppointmentDateTime();
        if (!$appointmentDateTime) {
            return 0;
        }

        // Use TimeHelper for consistent timezone handling
        $now = TimeHelper::now();
        $minutesLate = $now->diffInMinutes($appointmentDateTime, false);
        return abs($minutesLate);
    }

    /**
     * Get the appointment datetime as Carbon instance.
     */
    private function getAppointmentDateTime(): ?Carbon
    {
        try {
            $dateValue = $this->getAttribute('appointment_date');
            if (!$dateValue) {
                return null;
            }

            // Use TimeHelper for consistent timezone handling
            $appointmentDate = TimeHelper::parseDate($dateValue);
            if (!$appointmentDate) {
                return null;
            }
            
            if (empty($this->appointment_time)) {
                return null;
            }

            // Use TimeHelper to combine date and time
            return TimeHelper::combineDateTime($appointmentDate, $this->appointment_time);
        } catch (\Exception $e) {
            \Log::error("Error parsing appointment datetime", [
                'appointment_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Scope a query to only include appointments for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    /**
     * Scope a query to only include appointments with specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', TimeHelper::todayString());
    }

    /**
     * Scope a query to only include past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', TimeHelper::todayString());
    }

    /**
     * Scope a query to filter by client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope a query to filter by staff.
     */
    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Deduct treatment products inventory when service is completed.
     * Ensures only one deduction per appointment to prevent duplicates.
     */
    public function deductTreatmentProductsInventory(): void
    {
        try {
            // Check if inventory has already been deducted for this appointment
            $existingLogs = \App\Models\InventoryUsageLog::where('appointment_id', $this->id)->count();
            if ($existingLogs > 0) {
                \Log::warning("Inventory already deducted for appointment {$this->id}. Skipping duplicate deduction. Existing logs: {$existingLogs}");
                return;
            }

            // Reload the appointment with services relationship to ensure it's fresh
            $this->load('services.treatmentProducts');
            
            // Get all services for this appointment (both many-to-many and single service)
            $services = $this->services;
            
            // If no services in many-to-many relationship, check single service_id (backward compatibility)
            if ($services->isEmpty() && $this->service_id) {
                // Reload the service relationship
                $this->load('service.treatmentProducts');
                $singleService = $this->service;
                if ($singleService) {
                    $services = collect([$singleService]);
                }
            }

            if ($services->isEmpty()) {
                // Try to get services count using the relationship query
                $servicesCount = $this->services()->count();
                \Log::warning("No services found for appointment {$this->id} to deduct inventory from.", [
                    'appointment_id' => $this->id,
                    'service_id' => $this->service_id,
                    'services_relationship_count' => $servicesCount,
                    'services_collection_count' => $this->services->count()
                ]);
                
                // If services relationship has records but collection is empty, try to reload
                if ($servicesCount > 0) {
                    \Log::info("Services exist in database but not loaded. Attempting to reload...");
                    $this->refresh();
                    $this->load('services.treatmentProducts');
                    $services = $this->services;
                    
                    if ($services->isEmpty()) {
                        \Log::error("Still no services after reload for appointment {$this->id}");
                        return;
                    }
                } else {
                    return;
                }
            }

            \Log::info("Processing inventory deduction for appointment {$this->id}", [
                'services_count' => $services->count(),
                'service_ids' => $services->pluck('id')->toArray()
            ]);

            // Track which products have been deducted to prevent duplicates
            $deductedProducts = [];

            foreach ($services as $service) {
                if (!$service) {
                    continue;
                }

                // Reload service with treatment products to ensure fresh data
                $service->load('treatmentProducts');
                
                // Get treatment products for this service
                $treatmentProducts = $service->treatmentProducts;

                \Log::info("Checking service {$service->id} ({$service->name})", [
                    'treatment_products_count' => $treatmentProducts->count(),
                    'product_ids' => $treatmentProducts->pluck('id')->toArray()
                ]);

                if ($treatmentProducts->isEmpty()) {
                    \Log::info("Service {$service->id} ({$service->name}) has no treatment products configured.");
                    continue;
                }

                foreach ($treatmentProducts as $product) {
                    // Check if this product has already been deducted in this appointment
                    $productKey = $product->id . '_' . $service->id;
                    if (isset($deductedProducts[$product->id])) {
                        \Log::info("Product {$product->id} ({$product->name}) already deducted for appointment {$this->id}. Skipping duplicate.");
                        continue;
                    }

                    // Deduct from inventory
                    $inventoryItem = \App\Models\InventoryItem::find($product->id);
                    
                    if (!$inventoryItem) {
                        \Log::warning("Inventory item not found for product ID {$product->id} in service {$service->id}.");
                        continue;
                    }

                    // Check if using mL-based tracking
                    if ($inventoryItem->usesMlTracking()) {
                        // Use mL-based deduction
                        $volumeMl = null;
                        
                        // Priority 1: Use volume_used_per_service if set (this deducts directly from content_per_unit)
                        if (isset($product->pivot->volume_used_per_service) && $product->pivot->volume_used_per_service !== null && $product->pivot->volume_used_per_service !== '' && $product->pivot->volume_used_per_service > 0) {
                            $rawVolume = (float) $product->pivot->volume_used_per_service;
                            // Ensure positive value (use absolute value to prevent negative deductions)
                            $volumeMl = abs($rawVolume);
                            
                            \Log::info("Using volume_used_per_service for product {$inventoryItem->name}: raw={$rawVolume}, final={$volumeMl} mL (deducting from content_per_unit)");
                        } 
                        // Priority 2: Calculate from quantity using content_per_unit if volume_used_per_service is not set or is 0
                        else {
                            $quantity = (float) ($product->pivot->quantity ?? 1);
                            if ($quantity > 0) {
                                // Use content_per_unit if available (convert to mL if needed)
                                if ($inventoryItem->content_per_unit && $inventoryItem->content_per_unit > 0) {
                                    $contentPerUnit = (float) $inventoryItem->content_per_unit;
                                    $contentUnit = $inventoryItem->content_unit ?? 'mL';
                                    
                                    // Convert content_per_unit to mL based on content_unit
                                    $contentPerUnitInMl = $this->convertToMl($contentPerUnit, $contentUnit);
                                    
                                    // Calculate mL from quantity: quantity * content_per_unit (in mL)
                                    $volumeMl = abs($quantity * $contentPerUnitInMl);
                                    \Log::info("Calculated volume from quantity using content_per_unit for product {$inventoryItem->name}: quantity={$quantity}, content_per_unit={$contentPerUnit} {$contentUnit} ({$contentPerUnitInMl} mL), volumeMl={$volumeMl}");
                                } 
                                // Fallback to volume_per_container if content_per_unit is not available
                                elseif ($inventoryItem->volume_per_container > 0) {
                                $volumeMl = abs($quantity * $inventoryItem->volume_per_container);
                                    \Log::info("Calculated volume from quantity using volume_per_container for product {$inventoryItem->name}: quantity={$quantity}, volume_per_container={$inventoryItem->volume_per_container}, volumeMl={$volumeMl}");
                                } 
                                // Last fallback: use quantity as mL directly
                                else {
                                    $volumeMl = abs($quantity);
                                    \Log::info("Using quantity as mL directly for product {$inventoryItem->name} (no content_per_unit or volume_per_container): quantity={$quantity}, volumeMl={$volumeMl}");
                                }
                            }
                        }
                        
                        if ($volumeMl && $volumeMl > 0) {
                            // Double-check: ensure volumeMl is positive
                            $volumeMl = abs($volumeMl);
                            
                            // Store stock before deduction
                            $stockBefore = $inventoryItem->total_volume_ml;
                            
                            \Log::info("Attempting to deduct {$volumeMl} mL from {$inventoryItem->name} (ID: {$inventoryItem->id}). Current total_volume_ml: {$inventoryItem->total_volume_ml}");
                            
                            $success = $inventoryItem->deductVolumeMl($volumeMl);
                            
                            // Reload to get updated values
                            $inventoryItem->refresh();
                            
                            if ($success) {
                                \Log::info("Successfully deducted {$volumeMl} mL of {$inventoryItem->name} (ID: {$inventoryItem->id}) for appointment {$this->id}. Remaining: {$inventoryItem->total_volume_ml} mL ({$inventoryItem->full_containers} containers)");
                                
                                // Mark product as deducted to prevent duplicates
                                $deductedProducts[$inventoryItem->id] = true;
                                
                                // Log usage to analytics
                                \App\Models\InventoryUsageLog::create([
                                    'appointment_id' => $this->id,
                                    'inventory_item_id' => $inventoryItem->id,
                                    'service_id' => $service->id,
                                    'item_name' => $inventoryItem->name,
                                    'item_sku' => $inventoryItem->sku,
                                    'usage_type' => 'service',
                                    'quantity_deducted' => 0,
                                    'volume_ml_deducted' => $volumeMl,
                                    'stock_before' => $stockBefore,
                                    'stock_after' => $inventoryItem->total_volume_ml,
                                    'unit' => 'mL',
                                    'is_ml_tracking' => true,
                                    'notes' => "Deducted from service: {$service->name}",
                                ]);
                            } else {
                                \Log::warning("Insufficient volume for product {$inventoryItem->name} (ID: {$inventoryItem->id}). Required: {$volumeMl} mL, Available: {$inventoryItem->total_volume_ml} mL. No deduction made.");
                            }
                        } else {
                            \Log::warning("Cannot determine volume to deduct for product {$inventoryItem->name} (ID: {$inventoryItem->id}). volume_used_per_service={$product->pivot->volume_used_per_service}, quantity={$product->pivot->quantity}");
                        }
                    } else {
                        // For quantity-based deduction by Unit (Packaging Type)
                        // Use the quantity field directly from the service's linked products
                        $quantityToDeduct = (float) ($product->pivot->quantity ?? 1);
                        
                        // Ensure positive value
                        $quantityToDeduct = abs($quantityToDeduct);
                        
                        if ($quantityToDeduct <= 0) {
                            \Log::warning("Invalid quantity to deduct for product {$inventoryItem->name} (ID: {$inventoryItem->id}). Quantity: {$quantityToDeduct}. Skipping deduction.");
                            continue;
                        }

                        // Round up to whole units (by packaging type)
                        // Example: 0.5 bottle becomes 1 bottle, 1.2 boxes becomes 2 boxes
                        $quantityToDeductWholeUnits = (int) ceil($quantityToDeduct);
                        
                        \Log::info("Deducting quantity for product {$inventoryItem->name}: quantity_from_service={$quantityToDeduct}, rounded_to_whole_units={$quantityToDeductWholeUnits} {$inventoryItem->unit}");

                        // Store stock before deduction
                        $stockBefore = $inventoryItem->current_stock;

                        \Log::info("Attempting to deduct {$quantityToDeductWholeUnits} {$inventoryItem->unit} (packaging type) of {$inventoryItem->name} (ID: {$inventoryItem->id}) for appointment {$this->id}. Current stock: {$stockBefore}");

                        // Check if we have enough whole units
                        if ($inventoryItem->current_stock >= $quantityToDeductWholeUnits) {
                            // Deduct whole units by packaging type
                            $inventoryItem->decrement('current_stock', $quantityToDeductWholeUnits);
                            
                            \Log::info("Successfully deducted {$quantityToDeductWholeUnits} {$inventoryItem->unit} of {$inventoryItem->name} (ID: {$inventoryItem->id}) for appointment {$this->id}. Remaining stock: {$inventoryItem->current_stock}");
                            
                            // Reload to get updated values
                            $inventoryItem->refresh();
                            
                            // Mark product as deducted to prevent duplicates
                            $deductedProducts[$inventoryItem->id] = true;
                            
                            // Log usage to analytics (store whole units deducted)
                            \App\Models\InventoryUsageLog::create([
                                'appointment_id' => $this->id,
                                'inventory_item_id' => $inventoryItem->id,
                                'service_id' => $service->id,
                                'item_name' => $inventoryItem->name,
                                'item_sku' => $inventoryItem->sku,
                                'usage_type' => 'service',
                                'quantity_deducted' => $quantityToDeductWholeUnits, // Store whole units deducted
                                'volume_ml_deducted' => 0,
                                'stock_before' => $stockBefore,
                                'stock_after' => $inventoryItem->current_stock,
                                'unit' => $inventoryItem->unit, // Packaging type (bottle, box, pack, etc.)
                                'is_ml_tracking' => false,
                                'notes' => "Deducted {$quantityToDeductWholeUnits} {$inventoryItem->unit} from service: {$service->name}",
                            ]);
                        } else {
                            // Log warning if insufficient stock but still deduct what's available
                            $availableStock = $inventoryItem->current_stock;
                            if ($availableStock > 0) {
                                $inventoryItem->decrement('current_stock', $availableStock);
                                \Log::warning("Insufficient stock for product {$inventoryItem->name} (ID: {$inventoryItem->id}). Required: {$quantityToDeductWholeUnits} {$inventoryItem->unit}, Available: {$availableStock} {$inventoryItem->unit}. Deducted available stock only.");
                                
                                // Reload to get updated values
                                $inventoryItem->refresh();
                                
                                // Mark product as deducted to prevent duplicates
                                $deductedProducts[$inventoryItem->id] = true;
                                
                                // Log usage to analytics (partial deduction)
                                \App\Models\InventoryUsageLog::create([
                                    'appointment_id' => $this->id,
                                    'inventory_item_id' => $inventoryItem->id,
                                    'service_id' => $service->id,
                                    'item_name' => $inventoryItem->name,
                                    'item_sku' => $inventoryItem->sku,
                                    'usage_type' => 'service',
                                    'quantity_deducted' => $availableStock,
                                    'volume_ml_deducted' => 0,
                                    'stock_before' => $stockBefore,
                                    'stock_after' => $inventoryItem->current_stock,
                                    'unit' => $inventoryItem->unit,
                                    'is_ml_tracking' => false,
                                    'notes' => "Partial deduction from service: {$service->name} (Required: {$quantityToDeductWholeUnits} {$inventoryItem->unit}, Available: {$availableStock} {$inventoryItem->unit})",
                                ]);
                            } else {
                                \Log::error("Out of stock for product {$inventoryItem->name} (ID: {$inventoryItem->id}). Required: {$quantityToDeductWholeUnits} {$inventoryItem->unit}, Available: 0. No deduction made.");
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error deducting treatment products inventory for appointment {$this->id}: " . $e->getMessage(), [
                'appointment_id' => $this->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Convert a value to mL based on the unit.
     * 
     * @param float $value The value to convert
     * @param string $unit The unit of the value (mL, L, g, kg, oz, fl oz, etc.)
     * @return float The value converted to mL
     */
    private function convertToMl(float $value, string $unit): float
    {
        $unit = strtolower(trim($unit));
        
        switch ($unit) {
            case 'ml':
            case 'milliliter':
            case 'millilitre':
                return $value;
            
            case 'l':
            case 'liter':
            case 'litre':
                return $value * 1000; // 1 L = 1000 mL
            
            case 'fl oz':
            case 'fluid ounce':
            case 'fluid oz':
                return $value * 29.5735; // 1 fl oz ≈ 29.5735 mL
            
            case 'oz':
            case 'ounce':
                // For weight (oz), we can't directly convert to mL without density
                // Assume it's fluid ounce if not specified
                return $value * 29.5735;
            
            case 'g':
            case 'gram':
            case 'grams':
                // For weight, we can't directly convert to mL without density
                // For water: 1g = 1mL, but for other substances it varies
                // We'll assume 1g = 1mL as a reasonable default for most liquids
                return $value;
            
            case 'kg':
            case 'kilogram':
            case 'kilograms':
                // For weight, assume 1kg = 1000mL (for water-like density)
                return $value * 1000;
            
            case 'pc':
            case 'pcs':
            case 'piece':
            case 'pieces':
                // For pieces, we can't convert to mL
                // Return 0 to indicate conversion not possible
                return 0;
            
            default:
                // Unknown unit, assume it's already in mL
                \Log::warning("Unknown unit '{$unit}' for content conversion. Assuming mL.");
                return $value;
        }
    }

    /**
     * Restore treatment products inventory when appointment status changes from completed.
     */
    public function restoreTreatmentProductsInventory(): void
    {
        try {
            // Reload the appointment with services relationship to ensure it's fresh
            $this->load('services.treatmentProducts');
            
            // Get all services for this appointment (both many-to-many and single service)
            $services = $this->services;
            
            // If no services in many-to-many relationship, check single service_id (backward compatibility)
            if ($services->isEmpty() && $this->service_id) {
                // Reload the service relationship
                $this->load('service.treatmentProducts');
                $singleService = $this->service;
                if ($singleService) {
                    $services = collect([$singleService]);
                }
            }

            if ($services->isEmpty()) {
                \Log::warning("No services found for appointment {$this->id} to restore inventory from.");
                return;
            }

            \Log::info("Processing inventory restoration for appointment {$this->id}", [
                'services_count' => $services->count()
            ]);

            // Ensure treatment products are loaded for all services
            foreach ($services as $service) {
                if (!$service) {
                    continue;
                }

                // Reload service with treatment products to ensure fresh data
                $service->load('treatmentProducts');
                
                // Get treatment products for this service
                $treatmentProducts = $service->treatmentProducts;

                if ($treatmentProducts->isEmpty()) {
                    continue;
                }

                foreach ($treatmentProducts as $product) {
                    // Restore to inventory
                    $inventoryItem = \App\Models\InventoryItem::find($product->id);
                    
                    if (!$inventoryItem) {
                        \Log::warning("Inventory item not found for product ID {$product->id} when restoring inventory for appointment {$this->id}.");
                        continue;
                    }

                    // Check if using mL-based tracking
                    if ($inventoryItem->usesMlTracking()) {
                        // Use mL-based restoration
                        $volumeMl = null;
                        
                        // Priority 1: Use volume_used_per_service if set
                        if (isset($product->pivot->volume_used_per_service) && $product->pivot->volume_used_per_service !== null && $product->pivot->volume_used_per_service !== '') {
                            $rawVolume = (float) $product->pivot->volume_used_per_service;
                            // Ensure positive value
                            $volumeMl = abs($rawVolume);
                        } 
                        // Priority 2: Calculate from quantity if volume_used_per_service is not set
                        else {
                            $quantity = (int) ($product->pivot->quantity ?? 1);
                            if ($quantity > 0 && $inventoryItem->volume_per_container > 0) {
                                // Calculate mL from quantity: assume each quantity unit uses the full volume_per_container
                                $volumeMl = abs($quantity * $inventoryItem->volume_per_container);
                            }
                        }
                        
                        if ($volumeMl && $volumeMl > 0) {
                            // Double-check: ensure volumeMl is positive
                            $volumeMl = abs($volumeMl);
                            $inventoryItem->restoreVolumeMl($volumeMl);
                            $inventoryItem->refresh();
                            \Log::info("Restored {$volumeMl} mL of {$inventoryItem->name} (ID: {$inventoryItem->id}) for appointment {$this->id}. New total: {$inventoryItem->total_volume_ml} mL ({$inventoryItem->full_containers} containers)");
                        } else {
                            \Log::warning("Cannot determine volume to restore for product {$inventoryItem->name} (ID: {$inventoryItem->id}). volume_used_per_service and quantity are both invalid.");
                        }
                    } else {
                        // For quantity-based restoration: always restore 1 unit (fixed)
                        $quantityToRestore = 1;

                        $inventoryItem->increment('current_stock', $quantityToRestore);
                        \Log::info("Restored {$quantityToRestore} {$inventoryItem->unit} of {$inventoryItem->name} (ID: {$inventoryItem->id}) for appointment {$this->id}. New stock: {$inventoryItem->current_stock}");
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error restoring treatment products inventory for appointment {$this->id}: " . $e->getMessage(), [
                'appointment_id' => $this->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
