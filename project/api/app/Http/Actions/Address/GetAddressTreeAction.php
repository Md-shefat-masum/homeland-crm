<?php

namespace App\Http\Actions\Address;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class GetAddressTreeAction
{
    public function execute(?string $type = null): array
    {
        try {
            // Recursive function to load children
            $loadChildren = function ($address) use (&$loadChildren) {
                $address->load(['children' => function ($q) {
                    $q->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name');
                }]);
                
                foreach ($address->children as $child) {
                    $loadChildren($child);
                }
            };

            $query = Address::where('is_active', true);

            if ($type) {
                $query->where('type', $type);
            }

            // Get root addresses (no parent)
            $addresses = $query->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            // Load all children recursively
            foreach ($addresses as $address) {
                $loadChildren($address);
            }

            return [
                'success' => true,
                'data' => $addresses,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch address tree',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

