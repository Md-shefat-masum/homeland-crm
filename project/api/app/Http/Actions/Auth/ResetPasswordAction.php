<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordAction
{
    public function execute(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Find the reset code
        $resetCode = DB::table('password_reset_codes')
            ->where('email', $data['email'])
            ->where('code', $data['code'])
            ->first();

        // Check if code exists
        if (!$resetCode) {
            return [
                'success' => false,
                'message' => 'Invalid verification code.',
            ];
        }

        // Check if code is expired
        if (now()->greaterThan($resetCode->expires_at)) {
            // Delete expired code
            DB::table('password_reset_codes')
                ->where('email', $data['email'])
                ->where('code', $data['code'])
                ->delete();

            return [
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ];
        }

        // Find user
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        // Check if user is active
        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Account is inactive. Please contact administrator.',
            ];
        }

        // Update password
        $user->password = Hash::make($data['password']);
        $user->save();

        // Delete the used verification code
        DB::table('password_reset_codes')
            ->where('email', $data['email'])
            ->where('code', $data['code'])
            ->delete();

        // Also delete any other codes for this email (cleanup)
        DB::table('password_reset_codes')
            ->where('email', $data['email'])
            ->delete();

        return [
            'success' => true,
            'message' => 'Password has been reset successfully.',
        ];
    }
}

