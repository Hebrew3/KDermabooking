<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = $this->getSettings();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $settings = $request->except(['_token', 'logo']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings['logo'] = $logoPath;
        }

        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'General settings updated successfully.');
    }

    /**
     * Update business settings.
     */
    public function updateBusiness(Request $request)
    {
        $request->validate([
            'business_hours' => 'required|array',
            'appointment_duration' => 'required|integer|min:15|max:480',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'cancellation_hours' => 'required|integer|min:1|max:72',
            'auto_confirm_appointments' => 'boolean',
            'allow_online_payments' => 'boolean',
            'require_deposit' => 'boolean',
            'deposit_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $settings = $request->except('_token');

        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Business settings updated successfully.');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'appointment_reminders' => 'boolean',
            'reminder_hours_before' => 'required|integer|min:1|max:72',
            'admin_notifications' => 'boolean',
            'client_notifications' => 'boolean',
            'staff_notifications' => 'boolean',
        ]);

        $settings = $request->except('_token');

        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Update payment settings.
     */
    public function updatePayments(Request $request)
    {
        $request->validate([
            'payment_methods' => 'required|array',
            'stripe_publishable_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'paypal_client_id' => 'nullable|string',
            'paypal_client_secret' => 'nullable|string',
            'gcash_merchant_id' => 'nullable|string',
            'paymaya_public_key' => 'nullable|string',
            'paymaya_secret_key' => 'nullable|string',
            'payment_terms' => 'nullable|string',
        ]);

        $settings = $request->except('_token');

        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Update system settings.
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'boolean',
            'user_registration' => 'boolean',
            'email_verification' => 'boolean',
            'two_factor_auth' => 'boolean',
            'session_timeout' => 'required|integer|min:15|max:1440',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'log_level' => 'required|string|in:emergency,alert,critical,error,warning,notice,info,debug',
        ]);

        $settings = $request->except('_token');

        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')
                        ->with('success', 'System settings updated successfully.');
    }


    /**
     * Get all settings.
     */
    private function getSettings()
    {
        return Cache::remember('app_settings', 3600, function () {
            $settings = Setting::getAllSettings();
            
            // Default settings if not set
            $defaults = [
                'app_name' => 'K-Derma Booking System',
                'app_description' => 'Professional dermatology appointment booking system',
                'contact_email' => 'info@kderma.com',
                'contact_phone' => '+1-555-KDERMA',
                'address' => '123 Medical Center, Healthcare District',
                'timezone' => 'Asia/Manila',
                'currency' => 'PHP',
                'business_hours' => json_encode([
                    'monday' => ['open' => '09:00', 'close' => '18:00', 'enabled' => true],
                    'tuesday' => ['open' => '09:00', 'close' => '18:00', 'enabled' => true],
                    'wednesday' => ['open' => '09:00', 'close' => '18:00', 'enabled' => true],
                    'thursday' => ['open' => '09:00', 'close' => '18:00', 'enabled' => true],
                    'friday' => ['open' => '09:00', 'close' => '18:00', 'enabled' => true],
                    'saturday' => ['open' => '09:00', 'close' => '15:00', 'enabled' => true],
                    'sunday' => ['open' => '10:00', 'close' => '14:00', 'enabled' => false],
                ]),
                'appointment_duration' => 60,
                'advance_booking_days' => 30,
                'cancellation_hours' => 24,
                'auto_confirm_appointments' => false,
                'allow_online_payments' => true,
                'require_deposit' => false,
                'deposit_percentage' => 20,
                'email_notifications' => true,
                'sms_notifications' => false,
                'appointment_reminders' => true,
                'reminder_hours_before' => 24,
                'admin_notifications' => true,
                'client_notifications' => true,
                'staff_notifications' => true,
                'payment_methods' => json_encode(['cash', 'card', 'gcash']),
                'maintenance_mode' => false,
                'user_registration' => true,
                'email_verification' => true,
                'two_factor_auth' => false,
                'session_timeout' => 120,
                'max_login_attempts' => 5,
                'log_level' => 'info',
            ];
            
            return array_merge($defaults, $settings);
        });
    }

    /**
     * Set a setting value.
     */
    private function setSetting($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget('app_settings');
    }
}
