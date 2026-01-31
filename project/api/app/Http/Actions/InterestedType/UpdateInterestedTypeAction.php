<?php

namespace App\Http\Actions\InterestedType;

use App\Models\InterestedType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateInterestedTypeAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255|unique:interested_types,name,' . $id,
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
            $interestedType = InterestedType::findOrFail($id);
            $user = Auth::user();

            $data['updated_by'] = $user->id;
            $interestedType->update($data);

            return [
                'success' => true,
                'message' => 'Interested type updated successfully',
                'data' => $interestedType->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update interested type',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

