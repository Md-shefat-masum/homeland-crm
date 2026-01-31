<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\LeadSource\GetLeadSourcesAction;
use App\Http\Actions\LeadSource\GetLeadSourceAction;
use App\Http\Actions\LeadSource\CreateLeadSourceAction;
use App\Http\Actions\LeadSource\UpdateLeadSourceAction;
use App\Http\Actions\LeadSource\DeleteLeadSourceAction;
use Illuminate\Http\Request;

class LeadSourceController extends Controller
{
    /**
     * List all lead sources
     * GET /api/v1/lead-sources
     */
    public function index(Request $request, GetLeadSourcesAction $action)
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
     * Get single lead source
     * GET /api/v1/lead-sources/{id}
     */
    public function show($id, GetLeadSourceAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Create new lead source
     * POST /api/v1/lead-sources
     */
    public function store(Request $request, CreateLeadSourceAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update lead source
     * PUT /api/v1/lead-sources/{id}
     */
    public function update(Request $request, $id, UpdateLeadSourceAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete lead source (soft delete)
     * DELETE /api/v1/lead-sources/{id}
     */
    public function destroy($id, DeleteLeadSourceAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}

