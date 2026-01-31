<?php

namespace App\Http\Actions\Address;

use App\Models\Address;

class GetAddressChildrenAction
{
    public function execute(int $parentId): array
    {
        try {
            $parent = Address::findOrFail($parentId);

            $children = Address::where('parent_id', $parentId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return [
                'success' => true,
                'data' => [
                    'parent' => $parent,
                    'children' => $children,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch address children',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

