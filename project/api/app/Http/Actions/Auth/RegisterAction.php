<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterAction
{
    public function execute(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'info' => 'nullable|array', // Additional info as JSON
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Generate unique 6-digit UID
        $uid = $this->generateUniqueUid();

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'uid' => $uid,
                'address' => $data['address'] ?? null,
                'photo' => 'avatar.png', // Default photo
                'info' => $data['info'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'employee', // Default role
                'is_active' => true,
                'is_approved' => false, // Requires admin approval
                'is_blocked' => false,
                'email_verified_at' => null, // Email not verified yet
            ]);

            // Create user settings with defaults
            UserSetting::create([
                'user_id' => $user->id,
                'call_recording_enabled' => false,
                'speech_to_text_enabled' => true,
                'app_lock_enabled' => false,
                'app_lock_timeout_seconds' => 0,
            ]);

            // Assign employee role (from roles table)
            $employeeRole = Role::where('name', 'employee')->first();
            if ($employeeRole) {
                $user->roles()->attach($employeeRole->id);
            }

            DB::commit();

            // Load relationships
            $user->load(['roles', 'userSetting']);

            return [
                'success' => true,
                'message' => 'Registration successful. Please wait for admin approval.',
                'data' => [
                    'user' => $user,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * Generate a unique 6-digit UID
     */
    private function generateUniqueUid(): string
    {
        $maxAttempts = 100;
        $attempt = 0;

        do {
            // Generate 6-digit number starting from 100000
            // Get the highest existing UID and increment by 1, or start from 100000
            $lastUser = User::orderBy('id', 'desc')->first();
            $lastUid = $lastUser ? (int) $lastUser->uid : 99999;
            $newUid = str_pad((string) ($lastUid + 1), 6, '0', STR_PAD_LEFT);

            // Ensure it's at least 100000
            if ((int) $newUid < 100000) {
                $newUid = '100000';
            }

            // Check if UID already exists
            $exists = User::where('uid', $newUid)->exists();

            if (!$exists) {
                return $newUid;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        // Fallback: random 6-digit number
        do {
            $randomUid = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $exists = User::where('uid', $randomUid)->exists();
        } while ($exists);

        return $randomUid;
    }
}

