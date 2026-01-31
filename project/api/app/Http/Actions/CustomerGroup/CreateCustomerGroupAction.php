<?php

namespace App\Http\Actions\CustomerGroup;

use App\Models\CustomerGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateCustomerGroupAction
{
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:customer_groups,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $user = Auth::user();

            $group = CustomerGroup::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? null,
                'is_active' => true,
                'created_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'Customer group created successfully',
                'data' => $group,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create customer group',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

