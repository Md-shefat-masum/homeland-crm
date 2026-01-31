<?php

namespace App\Http\Actions\Customer;

use App\Models\Customer;

class DeleteCustomerAction
{
    public function execute(int $id): array
    {
        try {
            $customer = Customer::findOrFail($id);

            // Check if customer has related data
            $hasLeads = $customer->leads()->count() > 0;
            $hasNotes = $customer->customerNotes()->count() > 0;
            $hasFollowUps = $customer->followUps()->count() > 0;
            $hasCallLogs = $customer->callLogs()->count() > 0;
            $hasAssignments = $customer->customerAssignments()->count() > 0;

            if ($hasLeads || $hasNotes || $hasFollowUps || $hasCallLogs || $hasAssignments) {
                // Soft delete (already using SoftDeletes trait)
                $customer->delete();

                return [
                    'success' => true,
                    'message' => 'Customer soft deleted successfully (has related data)',
                ];
            }

            // Hard delete if no related data
            $customer->forceDelete();

            return [
                'success' => true,
                'message' => 'Customer deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete customer',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

