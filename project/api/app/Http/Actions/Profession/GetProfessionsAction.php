<?php

namespace App\Http\Actions\Profession;

use App\Models\Profession;

class GetProfessionsAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = Profession::query();

            // Search filter (job_title, business_type, company_name, description)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('job_title', 'like', "%{$search}%")
                        ->orWhere('business_type', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by type
            if (isset($filters['type']) && !empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            // Load relationships
            $query->withCount('customers');

            // Pagination
            $perPage = $filters['per_page'] ?? 10;
            $page = $filters['page'] ?? 1;

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'id';
            $descending = isset($filters['descending']) ? filter_var($filters['descending'], FILTER_VALIDATE_BOOLEAN) : true;
            $sortOrder = $descending ? 'desc' : 'asc';
            
            // Validate sort_by column to prevent SQL injection
            $allowedColumns = ['id', 'type', 'job_title', 'business_type', 'company_name', 'description', 'created_at', 'updated_at', 'customers_count'];
            if (!in_array($sortBy, $allowedColumns)) {
                $sortBy = 'id';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $professions = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                ...$professions->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch professions',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

