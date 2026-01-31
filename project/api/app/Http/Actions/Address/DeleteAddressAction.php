<?php

namespace App\Http\Actions\Address;

use App\Models\Address;

class DeleteAddressAction
{
    public function execute(int $id): array
    {
        try {
            $address = Address::findOrFail($id);

            // Check if address has children
            if ($address->children()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete address with children. Please delete children first.',
                ];
            }

            // Check if address is used by customers or projects
            if ($address->customers()->count() > 0 || $address->projects()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete address that is in use.',
                ];
            }

            $address->delete();

            return [
                'success' => true,
                'message' => 'Address deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete address',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

