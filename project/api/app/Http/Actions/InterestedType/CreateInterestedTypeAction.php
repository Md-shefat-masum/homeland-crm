<?php

namespace App\Http\Actions\InterestedType;

use App\Models\InterestedType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateInterestedTypeAction
{
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:interested_types,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
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

            $interestedType = InterestedType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'Interested type created successfully',
                'data' => $interestedType,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create interested type',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

