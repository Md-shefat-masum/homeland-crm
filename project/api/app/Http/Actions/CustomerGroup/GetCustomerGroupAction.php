<?php

namespace App\Http\Actions\CustomerGroup;

use App\Models\CustomerGroup;

class GetCustomerGroupAction
{
    public function execute(int $id): array
    {
        try {
            $group = CustomerGroup::with(['creator', 'updater'])
                ->findOrFail($id);

            return [
                'success' => true,
                'data' => $group,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Customer group not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

