<?php

namespace App\Http\Actions\CustomerGroup;

use App\Models\CustomerGroup;

class GetCustomerGroupsAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = CustomerGroup::query();

            // Search filter (name, description)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by is_active
            if (isset($filters['is_active']) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            }

            // Load relationships
            $query->with(['creator', 'updater']);

            // Add customer count
            $query->withCount('customers');

            // Pagination
            $perPage = $filters['per_page'] ?? 10;
            $page = $filters['page'] ?? 1;

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'id';
            $descending = isset($filters['descending']) ? filter_var($filters['descending'], FILTER_VALIDATE_BOOLEAN) : true;
            $sortOrder = $descending ? 'desc' : 'asc';
            
            // Validate sort_by column to prevent SQL injection
            $allowedColumns = ['id', 'name', 'description', 'color', 'is_active', 'created_at', 'updated_at', 'customers_count'];
            if (!in_array($sortBy, $allowedColumns)) {
                $sortBy = 'id';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $groups = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                ...$groups->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch customer groups',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

