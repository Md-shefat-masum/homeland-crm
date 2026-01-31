<?php

namespace App\Http\Actions\Project;

use App\Models\Project;

class GetProjectsAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = Project::query();

            // Search filter (name, description, slug)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if (isset($filters['status']) && $filters['status'] !== null && $filters['status'] !== '') {
                $query->where('status', $filters['status']);
            }

            // Filter by project_type
            if (isset($filters['project_type']) && $filters['project_type'] !== null && $filters['project_type'] !== '') {
                $query->where('project_type', $filters['project_type']);
            }

            // Filter by is_active
            if (isset($filters['is_active']) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            }

            // Load relationships
            $query->with(['address.parent', 'creator', 'updater']);

            // Add leads count
            $query->withCount('leads');

            // Pagination
            $perPage = $filters['per_page'] ?? 10;
            $page = $filters['page'] ?? 1;

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'id';
            $descending = isset($filters['descending']) ? filter_var($filters['descending'], FILTER_VALIDATE_BOOLEAN) : true;
            $sortOrder = $descending ? 'desc' : 'asc';
            
            // Validate sort_by column to prevent SQL injection
            $allowedColumns = ['id', 'name', 'slug', 'description', 'project_type', 'status', 'is_active', 'created_at', 'updated_at', 'leads_count'];
            if (!in_array($sortBy, $allowedColumns)) {
                $sortBy = 'id';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $projects = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                ...$projects->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch projects',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

