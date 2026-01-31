<?php

namespace App\Http\Actions\Lead;

use App\Models\Lead;

class GetLeadsAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = Lead::query();

            // Search filter (customer name, mobile, email, customer_requirement, preferred_area)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->whereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhere('customer_requirement', 'like', "%{$search}%")
                        ->orWhere('preferred_area', 'like', "%{$search}%")
                        ->orWhere('lead_source', 'like', "%{$search}%");
                });
            }

            // Filter by customer_id
            if (isset($filters['customer_id']) && $filters['customer_id'] !== null && $filters['customer_id'] !== '') {
                $query->where('customer_id', $filters['customer_id']);
            }

            // Filter by project_id
            if (isset($filters['project_id']) && $filters['project_id'] !== null && $filters['project_id'] !== '') {
                $query->where('project_id', $filters['project_id']);
            }

            // Filter by lead_source_id
            if (isset($filters['lead_source_id']) && $filters['lead_source_id'] !== null && $filters['lead_source_id'] !== '') {
                $query->where('lead_source_id', $filters['lead_source_id']);
            }

            // Filter by interested_type_id
            if (isset($filters['interested_type_id']) && $filters['interested_type_id'] !== null && $filters['interested_type_id'] !== '') {
                $query->where('interested_type_id', $filters['interested_type_id']);
            }

            // Filter by status
            if (isset($filters['status']) && $filters['status'] !== null && $filters['status'] !== '') {
                $query->where('status', $filters['status']);
            }

            // Filter by priority
            if (isset($filters['priority']) && $filters['priority'] !== null && $filters['priority'] !== '') {
                $query->where('priority', $filters['priority']);
            }

            // Filter by next_contact_date
            if (isset($filters['next_contact_date_from']) && $filters['next_contact_date_from'] !== null) {
                $query->where('next_contact_date', '>=', $filters['next_contact_date_from']);
            }
            if (isset($filters['next_contact_date_to']) && $filters['next_contact_date_to'] !== null) {
                $query->where('next_contact_date', '<=', $filters['next_contact_date_to']);
            }

            // Load relationships
            $query->with([
                'customer',
                'project',
                'customerLeadSource',
                'interestedType',
                'creator',
                'updater',
            ]);

            // Pagination
            $perPage = $filters['per_page'] ?? 10;
            $page = $filters['page'] ?? 1;

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'id';
            $descending = isset($filters['descending']) ? filter_var($filters['descending'], FILTER_VALIDATE_BOOLEAN) : true;
            $sortOrder = $descending ? 'desc' : 'asc';
            
            // Validate sort_by column to prevent SQL injection
            $allowedColumns = ['id', 'status', 'priority', 'next_contact_date', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedColumns)) {
                $sortBy = 'id';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $leads = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                ...$leads->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch leads',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

