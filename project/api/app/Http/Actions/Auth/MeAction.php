<?php

namespace App\Http\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class MeAction
{
    public function execute(): array
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated',
                ];
            }

            // Load relationships
            $user->load(['roles', 'userSetting', 'devices']);

            return [
                'success' => true,
                'data' => [
                    'user' => $user,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get user data',
                'error' => $e->getMessage(),
            ];
        }
    }
}

