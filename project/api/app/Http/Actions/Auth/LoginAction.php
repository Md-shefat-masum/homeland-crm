<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginAction
{
    public function execute(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'login' => 'required|string', // Can be email or mobile
            'password' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'platform' => 'nullable|string|in:android,ios,web',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Find user by email or mobile
        $user = User::where(function ($query) use ($data) {
            $query->where('email', $data['login'])
                ->orWhere('mobile', $data['login']);
        })->first();

        // Check if user exists
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        // Check if user is blocked
        if ($user->is_blocked) {
            return [
                'success' => false,
                'message' => 'User not permitted',
            ];
        }

        // Check if user is approved
        if (!$user->is_approved) {
            return [
                'success' => false,
                'message' => 'User not approved',
            ];
        }

        // Check if user is active
        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Account is inactive',
            ];
        }

        // Verify password
        if (!Hash::check($data['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        // Generate JWT token
        $token = Auth::guard('api')->login($user);

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to generate token',
            ];
        }

        // Device binding - create or update device
        $device = Device::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $data['device_id'],
            ],
            [
                'device_name' => $data['device_name'] ?? 'Unknown Device',
                'platform' => $data['platform'] ?? 'web',
                'last_seen_at' => now(),
                'is_active' => true,
                'revoked_at' => null,
                'revoked_by' => null,
            ]
        );

        // Load user relationships
        $user->load(['roles', 'userSetting', 'device']);

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60, // in seconds
                'device' => $device,
            ],
        ];
    }
}

