<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerNoteController extends Controller
{
    /**
     * List all notes for a customer
     * GET /api/v1/customers/{customerId}/notes
     */
    public function index(Request $request, $customerId)
    {
        // TODO: Call GetCustomerNotesAction
        return response()->json([
            'success' => false,
            'message' => 'Action not implemented yet',
        ], 501);
    }

    /**
     * Get single customer note
     * GET /api/v1/customers/{customerId}/notes/{id}
     */
    public function show($customerId, $id)
    {
        // TODO: Call GetCustomerNoteAction
        return response()->json([
            'success' => false,
            'message' => 'Action not implemented yet',
        ], 501);
    }

    /**
     * Create new customer note
     * POST /api/v1/customers/{customerId}/notes
     */
    public function store(Request $request, $customerId)
    {
        // TODO: Call CreateCustomerNoteAction
        return response()->json([
            'success' => false,
            'message' => 'Action not implemented yet',
        ], 501);
    }

    /**
     * Update customer note
     * PUT /api/v1/customers/{customerId}/notes/{id}
     */
    public function update(Request $request, $customerId, $id)
    {
        // TODO: Call UpdateCustomerNoteAction
        return response()->json([
            'success' => false,
            'message' => 'Action not implemented yet',
        ], 501);
    }

    /**
     * Delete customer note (soft delete)
     * DELETE /api/v1/customers/{customerId}/notes/{id}
     */
    public function destroy($customerId, $id)
    {
        // TODO: Call DeleteCustomerNoteAction
        return response()->json([
            'success' => false,
            'message' => 'Action not implemented yet',
        ], 501);
    }
}
