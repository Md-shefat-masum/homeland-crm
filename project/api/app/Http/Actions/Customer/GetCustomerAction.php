<?php

namespace App\Http\Actions\Customer;

use App\Models\Customer;

class GetCustomerAction
{
    public function execute(int $id): array
    {
        try {
            $customer = Customer::with([
                'customerGroup',
                'project',
                'profession',
                'currentAddress',
                'creator',
                'updater',
                'leads',
                'customerNotes.creator',
                'followUps',
                'callLogs',
                'customerAssignments',
            ])->findOrFail($id);

            return [
                'success' => true,
                'data' => $customer,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Customer not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

