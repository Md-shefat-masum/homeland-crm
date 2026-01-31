<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SendPasswordResetCodeAction
{
    public function execute(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Find user by email
        $user = User::where('email', $data['email'])->first();

        // Don't reveal if user exists or not (security best practice)
        // But we'll still send success message even if user doesn't exist
        if (!$user) {
            return [
                'success' => true,
                'message' => 'If the email exists, a verification code has been sent.',
            ];
        }

        // Check if user is active
        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Account is inactive. Please contact administrator.',
            ];
        }

        // Generate 6-digit verification code
        $code = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Calculate expiry time (30 minutes from now)
        $expiresAt = now()->addMinutes(30);

        // Delete any existing codes for this email
        DB::table('password_reset_codes')
            ->where('email', $data['email'])
            ->delete();

        // Store the code
        DB::table('password_reset_codes')->insert([
            'email' => $data['email'],
            'code' => $code,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);

        // Send email with verification code
        try {
            Mail::send('emails.password-reset-code', [
                'code' => $code,
                'user' => $user,
                'expires_in_minutes' => 30,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Password Reset Verification Code');
            });
        } catch (\Exception $e) {
            // Log error but don't reveal it to user
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to send verification code. Please try again later.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Verification code has been sent to your email.',
            'data' => [
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ];
    }
}

