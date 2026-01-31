<?php

namespace App\Http\Actions\Address;

use App\Models\Address;

class GetAddressesByTypeAction
{
    public function execute(string $type, ?int $parentId = null, ?string $search = null): array
    {
        try {
            $query = Address::where('type', $type)
                ->where('is_active', true);

            if ($parentId) {
                $query->where('parent_id', $parentId);
            } else {
                $query->whereNull('parent_id');
            }

            // Search filter
            if ($search && !empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Load parent relationship
            $query->with(['parent']);

            $addresses = $query->orderBy('sort_order')
                ->orderBy('name')
                ->limit(100) // Limit results for better performance
                ->get();

            return [
                'success' => true,
                'data' => $addresses,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch addresses',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

