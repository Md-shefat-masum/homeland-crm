<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\Profession\GetProfessionsAction;
use App\Http\Actions\Profession\GetProfessionsByTypeAction;
use App\Http\Actions\Profession\GetProfessionAction;
use App\Http\Actions\Profession\CreateProfessionAction;
use App\Http\Actions\Profession\UpdateProfessionAction;
use App\Http\Actions\Profession\DeleteProfessionAction;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    /**
     * List all professions
     * GET /api/v1/professions
     */
    public function index(Request $request, GetProfessionsAction $action)
    {
        $filters = $request->only([
            'search',
            'type',
            'page',
            'per_page',
            'sort_by',
            'descending',
        ]);
        
        $result = $action->execute($filters);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get professions by type
     * GET /api/v1/professions/type/{type}
     */
    public function getByType($type, GetProfessionsByTypeAction $action)
    {
        $result = $action->execute($type);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get single profession
     * GET /api/v1/professions/{id}
     */
    public function show($id, GetProfessionAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Create new profession
     * POST /api/v1/professions
     */
    public function store(Request $request, CreateProfessionAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update profession
     * PUT /api/v1/professions/{id}
     */
    public function update(Request $request, $id, UpdateProfessionAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete profession
     * DELETE /api/v1/professions/{id}
     */
    public function destroy($id, DeleteProfessionAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
