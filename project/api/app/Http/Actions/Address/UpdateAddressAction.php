<?php

namespace App\Http\Actions\Address;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateAddressAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:country,division,district,upazila,union,village,home name,area,road,other',
            'parent_id' => 'nullable|exists:addresses,id|different:' . $id,
            'code' => 'nullable|string|max:50|unique:addresses,code,' . $id,
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $address = Address::findOrFail($id);
            $user = Auth::user();

            // If parent_id is being changed, recalculate path and depth
            if (isset($data['parent_id']) && $data['parent_id'] != $address->parent_id) {
                $parentId = $data['parent_id'];
                $depth = 0;
                $path = '/';

                if ($parentId) {
                    $parent = Address::findOrFail($parentId);
                    $depth = $parent->depth + 1;
                    $path = $parent->path . $parent->id . '/';
                }

                $data['depth'] = $depth;
                $data['path'] = $path;
            }

            $data['updated_by'] = $user->id;
            $address->update($data);

            return [
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update address',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

