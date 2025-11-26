<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity.
     *
     * @param string $event
     * @param string $module
     * @param string $description
     * @return ActivityLog
     */
    protected function logActivity(string $event, string $module, string $description): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'module' => $module,
            'description' => $description,
        ]);
    }
}

