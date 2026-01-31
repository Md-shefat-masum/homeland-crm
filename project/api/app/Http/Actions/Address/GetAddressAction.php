<?php

namespace App\Http\Actions\Address;

use App\Models\Address;

class GetAddressAction
{
    public function execute(int $id): array
    {
        try {
            $address = Address::with(['parent', 'children', 'creator', 'updater'])
                ->findOrFail($id);

            return [
                'success' => true,
                'data' => $address,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Address not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

