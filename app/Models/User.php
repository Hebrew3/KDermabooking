<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Appointment;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'email',
        'password',
        'role',
        'mobile_number',
        'address',
        'profile_picture',
        'birth_date',
        'email_verified_at',
        'email_verification_code',
        'verification_code_expires_at',
        'remember_token',
        'google_id',
        'avatar',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return trim($name);
    }

    /**
     * Get the user's full name without middle name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the user's profile picture URL.
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        // If user has a Google avatar, use that
        if ($this->avatar && $this->google_id) {
            // Ensure Google avatar URL is properly formatted and accessible
            $avatarUrl = $this->avatar;
            
            // Remove size parameter and add a larger size for better quality
            if (strpos($avatarUrl, 's96-c') !== false) {
                $avatarUrl = str_replace('s96-c', 's200-c', $avatarUrl);
            }
            
            return $avatarUrl;
        }
        
        // Otherwise, use local profile picture
        if (!$this->profile_picture) {
            return null;
        }

        return asset('storage/' . $this->profile_picture);
    }

    /**
     * Get the best available avatar URL (Google or local).
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getProfilePictureUrlAttribute();
    }

    /**
     * Get appointments where this user is the client.
     */
    public function clientAppointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    /**
     * Get appointments where this user is the staff member.
     */
    public function staffAppointments()
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    /**
     * Get appointments (alias for clientAppointments for backward compatibility).
     */
    public function appointments()
    {
        return $this->clientAppointments();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is nurse.
     */
    public function isNurse(): bool
    {
        return $this->role === 'nurse';
    }

    /**
     * Check if user is aesthetician.
     */
    public function isAesthetician(): bool
    {
        return $this->role === 'aesthetician';
    }

    /**
     * Check if user is any type of staff member (nurse or aesthetician).
     */
    public function isStaffMember(): bool
    {
        return in_array($this->role, ['nurse', 'aesthetician']);
    }

    /**
     * Check if user is client.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Get staff schedules for this user.
     */
    public function staffSchedules()
    {
        return $this->hasMany(StaffSchedule::class, 'staff_id');
    }

    /**
     * Get staff specializations for this user.
     */
    public function staffSpecializations()
    {
        return $this->hasMany(StaffSpecialization::class, 'staff_id');
    }

    /**
     * Get staff unavailabilities for this user.
     */
    public function staffUnavailabilities()
    {
        return $this->hasMany(StaffUnavailability::class, 'staff_id');
    }

    /**
     * Get appointment notifications for this user.
     */
    public function appointmentNotifications()
    {
        return $this->hasMany(AppointmentNotification::class, 'client_id');
    }

    /**
     * Check if staff is available on a specific date and time.
     * Note: This method checks schedule and unavailabilities only.
     * For appointment conflicts, check separately in the controller.
     */
    public function isAvailableAt($date, $time): bool
    {
        if (!$this->isStaffMember()) {
            return false;
        }

        $dayOfWeek = strtolower(date('l', strtotime($date)));

        // Check regular weekly schedule - use case-insensitive matching
        $schedule = $this->staffSchedules()
            ->whereRaw('LOWER(day_of_week) = ?', [strtolower($dayOfWeek)])
            ->where('is_available', true)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->first();
            
        if (!$schedule || !$schedule->isAvailableAtTime($time)) {
            return false;
        }

        // Check for unavailabilities
        $unavailability = $this->staffUnavailabilities()
            ->forDate($date)
            ->where(function($query) use ($time) {
                $query->whereNull('start_time') // All day unavailability
                      ->orWhere(function($q) use ($time) {
                          $q->where('start_time', '<=', $time)
                            ->where('end_time', '>=', $time);
                      });
            })
            ->exists();

        return !$unavailability;
    }

    /**
     * Get primary specialization for staff.
     */
    public function getPrimarySpecialization()
    {
        return $this->staffSpecializations()->primary()->first();
    }

    /**
     * Get all specializations for staff.
     */
    public function getSpecializations()
    {
        return $this->staffSpecializations()->pluck('specialization')->toArray();
    }

    /**
     * Check if staff has a specific specialization.
     */
    public function hasSpecialization($specialization): bool
    {
        return $this->staffSpecializations()
            ->where('specialization', $specialization)
            ->exists();
    }

    /**
     * Get services assigned to this staff member.
     */
    public function assignedServices()
    {
        return $this->belongsToMany(Service::class, 'staff_services', 'staff_id', 'service_id')
                    ->withPivot(['is_primary', 'custom_price', 'proficiency_level', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Get primary services for this staff member.
     */
    public function primaryServices()
    {
        return $this->assignedServices()->wherePivot('is_primary', true);
    }

    /**
     * Get services this staff can perform based on specialization.
     */
    public function getQualifiedServices()
    {
        return Service::forStaff($this)->active()->get();
    }

    /**
     * Check if staff is assigned to a specific service.
     */
    public function isAssignedToService($serviceId): bool
    {
        return $this->assignedServices()->where('service_id', $serviceId)->exists();
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
