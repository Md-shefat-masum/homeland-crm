<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\Project\GetProjectsAction;
use App\Http\Actions\Project\GetProjectAction;
use App\Http\Actions\Project\CreateProjectAction;
use App\Http\Actions\Project\UpdateProjectAction;
use App\Http\Actions\Project\DeleteProjectAction;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * List all projects
     * GET /api/v1/crm-projects
     */
    public function index(Request $request, GetProjectsAction $action)
    {
        $filters = $request->only([
            'search',
            'status',
            'project_type',
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
     * Get single project
     * GET /api/v1/crm-projects/{id}
     */
    public function show($id, GetProjectAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Create new project
     * POST /api/v1/crm-projects
     */
    public function store(Request $request, CreateProjectAction $action)
    {
        $result = $action->execute($request->all());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Update project
     * PUT /api/v1/crm-projects/{id}
     */
    public function update(Request $request, $id, UpdateProjectAction $action)
    {
        $result = $action->execute($id, $request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete project (soft delete)
     * DELETE /api/v1/crm-projects/{id}
     */
    public function destroy($id, DeleteProjectAction $action)
    {
        $result = $action->execute($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}

