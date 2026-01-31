<?php

namespace App\Http\Actions\CustomerGroup;

use App\Models\CustomerGroup;

class DeleteCustomerGroupAction
{
    public function execute(int $id): array
    {
        try {
            $group = CustomerGroup::findOrFail($id);

            // Check if group is used by customers
            if ($group->customers()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete customer group that is in use by customers.',
                ];
            }

            $group->delete();

            return [
                'success' => true,
                'message' => 'Customer group deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete customer group',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

