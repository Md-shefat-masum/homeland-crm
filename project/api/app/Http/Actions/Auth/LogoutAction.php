<?php

namespace App\Http\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\Device;

class LogoutAction
{
    public function execute(?string $deviceId = null): array
    {
        try {
            $user = Auth::guard('api')->user();

            // If device_id provided, revoke specific device
            if ($deviceId && $user) {
                $device = Device::where('user_id', $user->id)
                    ->where('device_id', $deviceId)
                    ->first();

                if ($device) {
                    $device->update([
                        'is_active' => false,
                        'revoked_at' => now(),
                    ]);
                }
            }

            // Logout from JWT
            Auth::guard('api')->logout();

            return [
                'success' => true,
                'message' => 'Logged out successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to logout',
                'error' => $e->getMessage(),
            ];
        }
    }
}

