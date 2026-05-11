<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log($action, $module = null, $old = null, $new = null)
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'action' => $action,
                'module' => $module,
                'method' => Request::method(),
                'url' => Request::fullUrl(),
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar log: ' . $e->getMessage());
        }
    }
}
