<?php

namespace App\Http\Actions\Customer;

use App\Models\Customer;

class SearchCustomerAction
{
    public function execute(string $query, int $limit = 20): array
    {
        try {
            $customers = Customer::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('mobile', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('alternative_mobile', 'like', "%{$query}%");
            })
                ->where('is_active', true)
                ->with(['customerGroup', 'project', 'profession', 'currentAddress'])
                ->limit($limit)
                ->get();

            return [
                'success' => true,
                'data' => $customers,
                'count' => $customers->count(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to search customers',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

