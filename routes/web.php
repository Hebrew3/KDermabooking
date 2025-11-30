<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ForecastingController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Client\ServiceController as ClientServiceController;
use App\Http\Controllers\Auth\RoleBasedLoginController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('index.landing');

// Debug route to check Google OAuth configuration
Route::get('/debug-google-config', function () {
    // Calculate redirect URI the same way the controller does
    $redirectUri = env('GOOGLE_REDIRECT_URI');
    
    if (!$redirectUri) {
        $appUrl = rtrim(env('APP_URL', config('app.url')), '/');
        $port = request()->getPort();
        if ($port && $port != 80 && $port != 443) {
            $parsedUrl = parse_url($appUrl);
            if (!isset($parsedUrl['port'])) {
                $appUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ':' . $port;
            }
        }
        $redirectUri = $appUrl . '/auth/google/callback';
    } else {
        $redirectUri = rtrim($redirectUri, '/');
    }
    
    return response()->json([
        'google_client_id' => config('services.google.client_id') ? 'Set (' . substr(config('services.google.client_id'), 0, 20) . '...)' : 'Not Set',
        'google_client_secret' => config('services.google.client_secret') ? 'Set' : 'Not Set',
        'google_redirect_from_config' => config('services.google.redirect'),
        'google_redirect_calculated' => $redirectUri,
        'app_url' => config('app.url'),
        'request_url' => request()->fullUrl(),
        'request_port' => request()->getPort(),
        'env_app_url' => env('APP_URL'),
        'env_google_redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'instructions' => [
            'step_1' => 'Go to Google Cloud Console: https://console.cloud.google.com/',
            'step_2' => 'Navigate to APIs & Services > Credentials',
            'step_3' => 'Click on your OAuth 2.0 Client ID',
            'step_4' => 'Under "Authorized redirect URIs", add the following EXACT URI:',
            'required_redirect_uri' => $redirectUri,
            'step_5' => 'Click Save',
            'step_6' => 'Wait a few minutes for changes to propagate',
            'step_7' => 'Try signing in with Google again'
        ]
    ], JSON_PRETTY_PRINT);
})->name('debug.google');

// Debug route to check user avatar
Route::get('/debug-user-avatar', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'google_id' => $user->google_id,
        'avatar' => $user->avatar,
        'profile_picture' => $user->profile_picture,
        'profile_picture_url' => $user->profile_picture_url,
        'avatar_url' => $user->avatar_url,
    ]);
})->middleware('auth')->name('debug.avatar');

// Debug route to test image loading
Route::get('/test-avatar', function () {
    $user = auth()->user();
    if (!$user) {
        return 'Please log in first';
    }
    
    return view('test-avatar', compact('user'));
})->middleware('auth')->name('test.avatar');

// Contact form route
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'sendMessage'])->name('contact.send');

// Public chatbot endpoint (for homepage widget - no authentication required)
Route::post('/chatbot/public/send', [\App\Http\Controllers\Client\ChatbotController::class, 'sendPublicMessage'])->name('chatbot.public.send');



// Public appointment booking routes (using main layout, not client dashboard)
Route::middleware(['auth', 'redirect.role'])->group(function () {
    Route::get('/book-appointment', [ClientAppointmentController::class, 'create'])->name('appointments.book');
    Route::post('/book-appointment', [ClientAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [ClientAppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/my-appointments', [ClientAppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('/appointments/{appointment}/cancel', [ClientAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/feedback', [ClientAppointmentController::class, 'storeFeedback'])->name('appointments.feedback');
    Route::get('/available-time-slots', [ClientAppointmentController::class, 'getAvailableTimeSlots'])->name('appointments.available-slots');
    Route::get('/available-staff', [ClientAppointmentController::class, 'getAvailableStaff'])->name('appointments.available-staff');
});

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    Route::get('/chat/appointment/{appointmentId}', [ChatController::class, 'getConversation'])->name('chat.appointment');
    Route::get('/chat/{conversationId}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversationId}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{conversationId}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::post('/chat/{conversationId}/typing', [ChatController::class, 'handleTyping'])->name('chat.typing');
});

// Broadcasting authentication
Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    try {
        return \Illuminate\Support\Facades\Broadcast::auth($request);
    } catch (\Exception $e) {
        \Log::error('Broadcasting auth error: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 403);
    }
})->middleware(['auth']);


// Main dashboard route - redirects based on role
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isClient()) {
        // Redirect clients to landing page with authenticated features
        return redirect()->route('index.landing')->with('success', 'Welcome back, ' . $user->first_name . '!');
    } elseif ($user->isStaffMember()) {
        return redirect()->route('staff.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Role-based dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/analytics-data', [AdminDashboardController::class, 'getAnalyticsData'])->name('dashboard.analytics-data');
        Route::get('/dashboard/export/monthly-revenue', [AdminDashboardController::class, 'exportMonthlyRevenue'])->name('dashboard.export.monthly-revenue');
        Route::get('/dashboard/export/staff-scheduling', [AdminDashboardController::class, 'exportStaffScheduling'])->name('dashboard.export.staff-scheduling');
        Route::get('/dashboard/export/services-analytics', [AdminDashboardController::class, 'exportServicesAnalytics'])->name('dashboard.export.services-analytics');
        Route::get('/dashboard/export/product-sales', [AdminDashboardController::class, 'exportProductSales'])->name('dashboard.export.product-sales');
        Route::get('/dashboard/export/top-services', [AdminDashboardController::class, 'exportTopServicesByRevenue'])->name('dashboard.export.top-services');
        Route::get('/dashboard/export/top-clients', [AdminDashboardController::class, 'exportTopClients'])->name('dashboard.export.top-clients');

        // User Management - Full CRUD Resource Routes
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users-export', [UserController::class, 'export'])->name('users.export');

        // Service Management - Full CRUD Resource Routes
        Route::resource('services', ServiceController::class);
        Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        Route::patch('/services/{service}/toggle-featured', [ServiceController::class, 'toggleFeatured'])->name('services.toggle-featured');
        Route::get('/services-export', [ServiceController::class, 'export'])->name('services.export');

        // Appointment Management - Full CRUD Resource Routes
        Route::resource('appointments', AdminAppointmentController::class);
        Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
        Route::post('/appointments/{appointment}/feedback-reply', [AdminAppointmentController::class, 'replyToFeedback'])->name('appointments.feedback-reply');
        Route::get('/appointments-calendar', [AdminAppointmentController::class, 'calendar'])->name('appointments.calendar');
        Route::get('/available-staff', [ClientAppointmentController::class, 'getAvailableStaff'])->name('appointments.available-staff');

        // Inventory Management - Full CRUD Resource Routes
        Route::resource('inventory', InventoryController::class)->parameters(['inventory' => 'inventoryItem']);
        Route::patch('/inventory/{inventoryItem}/toggle-status', [InventoryController::class, 'toggleStatus'])->name('inventory.toggle-status');
        Route::patch('/inventory/{inventoryItem}/stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
        Route::get('/inventory-low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
        Route::get('/inventory-expiring', [InventoryController::class, 'expiring'])->name('inventory.expiring');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/inventory-activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'inventoryActivityLog'])->name('inventory-activity-log.index');

        // POS (Point of Sale) Management
        Route::get('/pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products', [\App\Http\Controllers\Admin\PosController::class, 'getProducts'])->name('pos.products');
        Route::post('/pos/process-sale', [\App\Http\Controllers\Admin\PosController::class, 'processSale'])->name('pos.process-sale');
        Route::post('/pos/import', [\App\Http\Controllers\Admin\PosController::class, 'import'])->name('pos.import');
        Route::get('/pos/receipt/{sale}', [\App\Http\Controllers\Admin\PosController::class, 'getReceipt'])->name('pos.receipt');
        Route::get('/pos/receipt/{sale}/print', [\App\Http\Controllers\Admin\PosController::class, 'printReceipt'])->name('pos.print-receipt');

        // Sales Management & Analytics
        Route::get('/sales', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}', [\App\Http\Controllers\Admin\SalesController::class, 'show'])->name('sales.show');
        Route::get('/sales-export', [\App\Http\Controllers\Admin\SalesController::class, 'export'])->name('sales.export');

        // Analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
        Route::post('/analytics/import', [AnalyticsController::class, 'import'])->name('analytics.import');

        // Forecasting
        Route::get('/forecasting', [ForecastingController::class, 'index'])->name('forecasting');
        Route::get('/forecasting/export', [ForecastingController::class, 'export'])->name('forecasting.export');
        Route::get('/forecasting/service', [ForecastingController::class, 'getServiceForecast'])->name('forecasting.service');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.update-general');
        Route::post('/settings/business', [SettingsController::class, 'updateBusiness'])->name('settings.update-business');
        Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.update-notifications');
        Route::post('/settings/payments', [SettingsController::class, 'updatePayments'])->name('settings.update-payments');
        Route::post('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.update-system');

        // Staff Unavailability Management
        Route::resource('staff-unavailability', \App\Http\Controllers\Admin\StaffUnavailabilityController::class);
        Route::post('/staff-unavailability/{notification}/respond', [\App\Http\Controllers\Admin\StaffUnavailabilityController::class, 'handleClientResponse'])->name('staff-unavailability.respond');

        // Staff Schedule Management
        Route::get('/staff-schedule', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'index'])->name('staff-schedule.index');
        Route::get('/staff-schedule/{staff}/edit', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'edit'])->name('staff-schedule.edit');
        Route::put('/staff-schedule/{staff}', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'update'])->name('staff-schedule.update');
        Route::post('/staff-schedule/weekend-coverage', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'updateWeekendCoverage'])->name('staff-schedule.weekend-coverage');
        Route::get('/staff-availability', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'getAvailability'])->name('staff-schedule.availability');
        Route::get('/staff-time-slots', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'timeSlotAvailability'])->name('staff-schedule.time-slots');

        // Staff Service Assignment Management
        Route::resource('staff-services', \App\Http\Controllers\Admin\StaffServiceController::class);
        Route::get('/staff-services-available', [\App\Http\Controllers\Admin\StaffServiceController::class, 'getAvailableServices'])->name('staff-services.available');

        // Staff Leave Approval
        Route::get('/staff-leave', [\App\Http\Controllers\Admin\StaffLeaveController::class, 'index'])->name('staff-leave.index');
        Route::get('/staff-leave/pending-count', [\App\Http\Controllers\Admin\StaffLeaveController::class, 'pendingCount'])->name('staff-leave.pending-count');
        Route::post('/staff-leave/{leave}/approve', [\App\Http\Controllers\Admin\StaffLeaveController::class, 'approve'])->name('staff-leave.approve');
        Route::post('/staff-leave/{leave}/reject', [\App\Http\Controllers\Admin\StaffLeaveController::class, 'reject'])->name('staff-leave.reject');
    });

    // Staff routes
    Route::prefix('staff')->name('staff.')->middleware(['auth', 'verified', 'staff'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');

        // Schedule Management
        Route::get('/schedule', [\App\Http\Controllers\Staff\ScheduleController::class, 'index'])->name('schedule');
        Route::post('/schedule', [\App\Http\Controllers\Staff\ScheduleController::class, 'update'])->name('schedule.update');
        Route::post('/schedule/unavailability', [\App\Http\Controllers\Staff\ScheduleController::class, 'addUnavailability'])->name('schedule.add-unavailability');
        Route::delete('/schedule/unavailability/{id}', [\App\Http\Controllers\Staff\ScheduleController::class, 'removeUnavailability'])->name('schedule.remove-unavailability');
        Route::get('/schedule/test-availability', [\App\Http\Controllers\Staff\ScheduleController::class, 'testAvailability'])->name('schedule.test-availability');

        // Leave requests
        Route::get('/leave', [\App\Http\Controllers\Staff\LeaveController::class, 'index'])->name('leave.index');
        Route::post('/leave', [\App\Http\Controllers\Staff\LeaveController::class, 'store'])->name('leave.store');

        // Client Management
        Route::get('/clients', [\App\Http\Controllers\Staff\ClientController::class, 'index'])->name('clients');
        Route::get('/clients/{client}', [\App\Http\Controllers\Staff\ClientController::class, 'show'])->name('clients.show');

        // Services
        Route::get('/services', [\App\Http\Controllers\Staff\ServiceController::class, 'index'])->name('services');
        Route::get('/services/{service}', [\App\Http\Controllers\Staff\ServiceController::class, 'show'])->name('services.show');
        Route::get('/my-services', [\App\Http\Controllers\Staff\ServiceController::class, 'myServices'])->name('my-services');

        Route::get('/messages', function () {
            return view('staff.messages');
        })->name('messages');

        Route::get('/help', function () {
            return view('staff.help');
        })->name('help');

        // Staff Profile Management
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'updateStaff'])->name('profile.update');

        // Debug route to check schedule data
        Route::get('/debug-schedule', function () {
            $staff = auth()->user();
            $schedules = \App\Models\StaffSchedule::where('staff_id', $staff->id)->get();
            
            return response()->json([
                'staff_id' => $staff->id,
                'schedules_count' => $schedules->count(),
                'schedules' => $schedules->toArray(),
                'table_exists' => \Schema::hasTable('staff_schedules'),
                'columns' => \Schema::getColumnListing('staff_schedules')
            ]);
        })->name('debug.schedule');
    });

    // Client routes
    Route::prefix('client')->name('client.')->middleware(['auth', 'verified', 'client'])->group(function () {
        // Client Appointment Management
        Route::resource('appointments', ClientAppointmentController::class)->except(['destroy']);
        Route::patch('/appointments/{appointment}/cancel', [ClientAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::post('/appointments/{appointment}/feedback', [ClientAppointmentController::class, 'storeFeedback'])->name('appointments.feedback');
        Route::get('/available-time-slots', [ClientAppointmentController::class, 'getAvailableTimeSlots'])->name('appointments.available-slots');
        Route::get('/available-staff', [ClientAppointmentController::class, 'getAvailableStaff'])->name('appointments.available-staff');

        // Client Services
        Route::get('/services', [ClientServiceController::class, 'index'])->name('services');
        Route::get('/services/{service}', [ClientServiceController::class, 'show'])->name('services.show');

        // Client Notifications
        Route::get('/notifications', [\App\Http\Controllers\Client\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Client\NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/respond', [\App\Http\Controllers\Client\NotificationController::class, 'respondToUnavailability'])->name('notifications.respond');
        Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications-count', [\App\Http\Controllers\Client\NotificationController::class, 'getUnreadCount'])->name('notifications.count');

        // Chatbot
        Route::get('/chatbot', [\App\Http\Controllers\Client\ChatbotController::class, 'index'])->name('chatbot.index');
        Route::post('/chatbot/send', [\App\Http\Controllers\Client\ChatbotController::class, 'sendMessage'])->name('chatbot.send');
        Route::get('/chatbot/{conversationId}/messages', [\App\Http\Controllers\Client\ChatbotController::class, 'getMessages'])->name('chatbot.messages');

        Route::get('/payments', function () {
            return view('client.payments');
        })->name('payments');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'updateClient'])->name('profile.update');

        Route::get('/messages', function () {
            return view('client.messages');
        })->name('messages');

        Route::get('/help', function () {
            return view('client.help');
        })->name('help');
    });
});

Route::middleware(['auth', 'redirect.role'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
