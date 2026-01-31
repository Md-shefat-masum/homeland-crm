<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\Lead\GetLeadsAction;
use App\Http\Actions\Lead\GetLeadAction;
use App\Http\Actions\Lead\CreateLeadAction;
use App\Http\Actions\Lead\UpdateLeadAction;
use App\Http\Actions\Lead\DeleteLeadAction;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * List all leads
     * GET /api/v1/leads
     */
    public function index(Request $request, GetLeadsAction $action)
    {
        $filters = $request->only([
            'search',
            'customer_id',
            'project_id',
            'lead_source_id',
            'interested_type_id',
            'status',
            'priority',
            'next_contact_date_from',
            'next_contact_date_to',
            'page',
            'per_page',
            'sort_by',
            'descending',
        ]);
        
        $result = $action->execute($filters);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get single lead
     * GET /api/v1/leads/{id}
     */
    public function show($id, GetLeadAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Create new lead
     * POST /api/v1/leads
     */
    public function store(Request $request, CreateLeadAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update lead
     * PUT /api/v1/leads/{id}
     */
    public function update(Request $request, $id, UpdateLeadAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete lead (soft delete)
     * DELETE /api/v1/leads/{id}
     */
    public function destroy($id, DeleteLeadAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}

