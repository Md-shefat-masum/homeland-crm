<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Actions\InterestedType\GetInterestedTypesAction;
use App\Http\Actions\InterestedType\GetInterestedTypeAction;
use App\Http\Actions\InterestedType\CreateInterestedTypeAction;
use App\Http\Actions\InterestedType\UpdateInterestedTypeAction;
use App\Http\Actions\InterestedType\DeleteInterestedTypeAction;

class InterestedTypeController extends Controller
{
    public function index(Request $request, GetInterestedTypesAction $getInterestedTypesAction)
    {
        $filters = $request->only(['search', 'is_active', 'sort_by', 'sort_order', 'per_page', 'page']);
        $result = $getInterestedTypesAction->execute($filters);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    public function show(int $id, GetInterestedTypeAction $getInterestedTypeAction)
    {
        $result = $getInterestedTypeAction->execute($id);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 404);
    }

    public function store(Request $request, CreateInterestedTypeAction $createInterestedTypeAction)
    {
        $data = $request->only(['name', 'description', 'color', 'is_active', 'sort_order']);
        $result = $createInterestedTypeAction->execute($data);

        if ($result['success']) {
            return response()->json($result, 201);
        }

        return response()->json($result, 422);
    }

    public function update(int $id, Request $request, UpdateInterestedTypeAction $updateInterestedTypeAction)
    {
        $data = $request->only(['name', 'description', 'color', 'is_active', 'sort_order']);
        $result = $updateInterestedTypeAction->execute($id, $data);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 422);
    }

    public function destroy(int $id, DeleteInterestedTypeAction $deleteInterestedTypeAction)
    {
        $result = $deleteInterestedTypeAction->execute($id);

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json($result, 409); // Conflict if associated with leads
    }
}

