<?php

namespace App\Http\Actions\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class GetCustomersAction
{
    public function execute(array $filters = []): array
    {
        try {
            $query = Customer::query();

            // Filter by customer_group_id
            if (isset($filters['customer_group_id'])) {
                $query->where('customer_group_id', $filters['customer_group_id']);
            }

            // Filter by project_id
            if (isset($filters['project_id'])) {
                $query->where('project_id', $filters['project_id']);
            }

            // Filter by profession_id
            if (isset($filters['profession_id'])) {
                $query->where('profession_id', $filters['profession_id']);
            }

            // Filter by current_address_id
            if (isset($filters['current_address_id'])) {
                $query->where('current_address_id', $filters['current_address_id']);
            }

            // Filter by is_active
            if (isset($filters['is_active'])) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            }

            // Search by name, mobile, email
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('alternative_mobile', 'like', "%{$search}%");
                });
            }

            // Filter by created_by
            if (isset($filters['created_by'])) {
                $query->where('created_by', $filters['created_by']);
            }

            // Date range filters
            if (isset($filters['created_from'])) {
                $query->whereDate('created_at', '>=', $filters['created_from']);
            }
            if (isset($filters['created_to'])) {
                $query->whereDate('created_at', '<=', $filters['created_to']);
            }

            // Load relationships
            $query->with([
                'customerGroup',
                'project',
                'profession',
                'currentAddress',
                'creator',
                'updater'
            ]);

            // Pagination
            $perPage = $filters['per_page'] ?? 15;
            $page = $filters['page'] ?? 1;

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $customers = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'last_page' => $customers->lastPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch customers',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

