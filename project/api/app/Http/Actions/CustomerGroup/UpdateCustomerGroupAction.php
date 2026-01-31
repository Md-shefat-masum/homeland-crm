<?php

namespace App\Http\Actions\CustomerGroup;

use App\Models\CustomerGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateCustomerGroupAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255|unique:customer_groups,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
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
            $group = CustomerGroup::findOrFail($id);
            $user = Auth::user();

            $data['updated_by'] = $user->id;
            $group->update($data);

            return [
                'success' => true,
                'message' => 'Customer group updated successfully',
                'data' => $group->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update customer group',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

