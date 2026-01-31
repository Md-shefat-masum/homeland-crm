<?php

namespace App\Http\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class RefreshTokenAction
{
    public function execute(): array
    {
        try {
            $token = Auth::guard('api')->refresh();
            $user = Auth::guard('api')->user();

            // Update device last_seen_at
            if ($user && request()->has('device_id')) {
                $device = $user->devices()
                    ->where('device_id', request('device_id'))
                    ->first();

                if ($device) {
                    $device->update(['last_seen_at' => now()]);
                }
            }

            return [
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => Auth::guard('api')->factory()->getTTL() * 60, // in seconds
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage(),
            ];
        }
    }
}

