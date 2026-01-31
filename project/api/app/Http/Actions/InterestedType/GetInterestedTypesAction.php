<?php

namespace App\Http\Actions\InterestedType;

use App\Models\InterestedType;

class GetInterestedTypesAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = InterestedType::query();

            // Apply filters
            if (isset($filters['search']) && $filters['search'] !== '') {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            }

            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $query->where('is_active', (bool) $filters['is_active']);
            }

            // Eager load relationships and count leads
            $query->with(['creator', 'updater'])->withCount('leads');

            // Apply sorting
            $sort_by = $filters['sort_by'] ?? 'sort_order';
            $sort_order = $filters['sort_order'] ?? 'asc';
            $query->orderBy($sort_by, $sort_order);

            // Apply pagination
            $per_page = $filters['per_page'] ?? 10;
            $interestedTypes = $query->paginate($per_page);

            return [
                'success' => true,
                'data' => $interestedTypes->items(),
                'pagination' => [
                    'current_page' => $interestedTypes->currentPage(),
                    'last_page' => $interestedTypes->lastPage(),
                    'per_page' => $interestedTypes->perPage(),
                    'total' => $interestedTypes->total(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch interested types',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

