<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\Address\GetAddressTreeAction;
use App\Http\Actions\Address\GetAddressesByTypeAction;
use App\Http\Actions\Address\GetAddressChildrenAction;
use App\Http\Actions\Address\GetAddressAction;
use App\Http\Actions\Address\GetAllAddressesAction;
use App\Http\Actions\Address\CreateAddressAction;
use App\Http\Actions\Address\UpdateAddressAction;
use App\Http\Actions\Address\DeleteAddressAction;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * List all addresses with pagination, filters, and sorting
     * GET /api/v1/addresses
     */
    public function index(Request $request, GetAllAddressesAction $action)
    {
        $filters = $request->only([
            'search',
            'type',
            'is_active',
            'page',
            'per_page',
            'sort_by',
            'descending',
        ]);
        
        // Debug: Use dump() instead of dd() - it doesn't stop execution
        // Or uncomment below to see filters in API response
        // return response()->json(['debug' => true, 'filters' => $filters, 'all_request' => $request->all(), 'query_params' => $request->query()], 200);
        
        $result = $action->execute($filters);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get address tree (hierarchical structure)
     * GET /api/v1/addresses/tree
     */
    public function tree(Request $request, GetAddressTreeAction $action)
    {
        $result = $action->execute($request->input('type'));
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get addresses by type
     * GET /api/v1/addresses/type/{type}
     */
    public function getByType(Request $request, $type, GetAddressesByTypeAction $action)
    {
        $result = $action->execute($type, $request->input('parent_id'), $request->input('search'));
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get children of a parent address
     * GET /api/v1/addresses/{id}/children
     */
    public function getChildren($id, GetAddressChildrenAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get single address
     * GET /api/v1/addresses/{id}
     */
    public function show($id, GetAddressAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Create new address
     * POST /api/v1/addresses
     */
    public function store(Request $request, CreateAddressAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update address
     * PUT /api/v1/addresses/{id}
     */
    public function update(Request $request, $id, UpdateAddressAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete address (soft delete)
     * DELETE /api/v1/addresses/{id}
     */
    public function destroy($id, DeleteAddressAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
