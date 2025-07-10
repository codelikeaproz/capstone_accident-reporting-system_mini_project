<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Log user activity
     *
     * @param string $action
     * @param User|null $user
     * @param array $details
     * @return ActivityLog|null
     */
    public static function logActivity(string $action, ?User $user = null, array $details = []): ?ActivityLog
    {
        try {
            $request = request();
            $ipAddress = $request ? $request->ip() : null;
            $userAgent = $request ? $request->header('User-Agent') : null;

            $log = ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'model_type' => $user ? get_class($user) : (isset($details['model_type']) ? $details['model_type'] : 'System'),
                'model_id' => $user?->id ?? ($details['model_id'] ?? 0),
                'old_values' => $details['old_values'] ?? null,
                'new_values' => $details['new_values'] ?? null,
                'description' => $details['description'] ?? json_encode($details),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);

            Log::info("Activity logged successfully", [
                'action' => $action,
                'user_id' => $user?->id,
                'ip_address' => $ipAddress
            ]);

            return $log;
        } catch (\Exception $e) {
            Log::error("Failed to log activity: " . $e->getMessage(), [
                'action' => $action,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Log authentication activities
     *
     * @param string $action
     * @param User|null $user
     * @param array $details
     * @return ActivityLog|null
     */
    public static function logAuthActivity(string $action, ?User $user = null, array $details = []): ?ActivityLog
    {
        try {
            $request = request();
            $ipAddress = $request ? $request->ip() : null;
            $userAgent = $request ? $request->header('User-Agent') : null;

            if ($user) {
                $details['user_name'] = $user->first_name . ' ' . $user->last_name;
                $details['user_email'] = $user->email;
                $details['user_role'] = $user->role ?? 'mdrrmo_staff';
            }

            $log = ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'model_type' => 'Authentication',
                'model_id' => $user?->id ?? 0,
                'old_values' => $details['old_values'] ?? null,
                'new_values' => $details['new_values'] ?? null,
                'description' => $details['description'] ?? json_encode($details),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);

            Log::info("Authentication activity logged successfully", [
                'action' => $action,
                'user_id' => $user?->id,
                'ip_address' => $ipAddress
            ]);

            return $log;
        } catch (\Exception $e) {
            Log::error("Failed to log authentication activity: " . $e->getMessage(), [
                'action' => $action,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Log service operation with specific details
     *
     * @param string $action
     * @param array $details
     * @return ActivityLog|null
     */
    protected function logServiceOperation(string $action, array $details = []): ?ActivityLog
    {
        return static::logActivity($action, $this->getCurrentUser(), array_merge([
            'service' => static::class,
            'timestamp' => now()->toISOString()
        ], $details));
    }

    /**
     * Get current authenticated user
     * This method should be implemented by classes using this trait
     *
     * @return User|null
     */
    abstract protected function getCurrentUser(): ?User;
}