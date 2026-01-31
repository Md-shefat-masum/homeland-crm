<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\Customer\GetCustomersAction;
use App\Http\Actions\Customer\GetCustomerAction;
use App\Http\Actions\Customer\SearchCustomerAction;
use App\Http\Actions\Customer\CreateCustomerAction;
use App\Http\Actions\Customer\UpdateCustomerAction;
use App\Http\Actions\Customer\DeleteCustomerAction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * List all customers with filters
     * GET /api/v1/customers
     */
    public function index(Request $request, GetCustomersAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get single customer with relationships
     * GET /api/v1/customers/{id}
     */
    public function show($id, GetCustomerAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Search customers
     * GET /api/v1/customers/search?q={query}
     */
    public function search(Request $request, SearchCustomerAction $action)
    {
        $search = $request->input('search', '');
        $query = $request->input('q', '');
        $limit = $request->input('limit', 20);
        
        $key = $query ?? $search;

        if($search){
            $key = $search;
        } else if($query){
            $key = $query;
        }

        if (empty($key)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
            ], 400);
        }

        $result = $action->execute($key, $limit);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Create new customer
     * POST /api/v1/customers
     */
    public function store(Request $request, CreateCustomerAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update customer
     * PUT /api/v1/customers/{id}
     */
    public function update(Request $request, $id, UpdateCustomerAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete customer (soft delete)
     * DELETE /api/v1/customers/{id}
     */
    public function destroy($id, DeleteCustomerAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
