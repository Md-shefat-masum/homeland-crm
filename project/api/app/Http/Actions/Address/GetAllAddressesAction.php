<?php

namespace App\Http\Actions\Address;

use App\Models\Address;
use Illuminate\Support\Facades\DB;

class GetAllAddressesAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = Address::query();

            // Search filter (name, code)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Filter by type
            if (isset($filters['type']) && !empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            // Filter by is_active
            if (isset($filters['is_active']) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            }

            // Load relationships
            $query->with(['parent']);

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
            $allowedColumns = ['id', 'name', 'code', 'type', 'is_active', 'created_at', 'updated_at', 'customers_count'];
            if (!in_array($sortBy, $allowedColumns)) {
                $sortBy = 'id';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $addresses = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                ...$addresses->toArray(),
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

