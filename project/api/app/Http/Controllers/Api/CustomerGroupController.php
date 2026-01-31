<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\CustomerGroup\GetCustomerGroupsAction;
use App\Http\Actions\CustomerGroup\GetCustomerGroupAction;
use App\Http\Actions\CustomerGroup\CreateCustomerGroupAction;
use App\Http\Actions\CustomerGroup\UpdateCustomerGroupAction;
use App\Http\Actions\CustomerGroup\DeleteCustomerGroupAction;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    /**
     * List all customer groups
     * GET /api/v1/customer-groups
     */
    public function index(Request $request, GetCustomerGroupsAction $action)
    {
        $filters = $request->only([
            'search',
            'is_active',
            'page',
            'per_page',
            'sort_by',
            'descending',
        ]);
        
        $result = $action->execute($filters);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get single customer group
     * GET /api/v1/customer-groups/{id}
     */
    public function show($id, GetCustomerGroupAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Create new customer group
     * POST /api/v1/customer-groups
     */
    public function store(Request $request, CreateCustomerGroupAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update customer group
     * PUT /api/v1/customer-groups/{id}
     */
    public function update(Request $request, $id, UpdateCustomerGroupAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete customer group (soft delete)
     * DELETE /api/v1/customer-groups/{id}
     */
    public function destroy($id, DeleteCustomerGroupAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
