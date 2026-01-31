<?php

namespace App\Http\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateProjectAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:projects,slug,' . $id,
            'description' => 'nullable|string',
            'address_id' => 'nullable|exists:addresses,id',
            'address_text' => 'nullable|string',
            'project_type' => 'nullable|in:apartment,land,commercial,other',
            'status' => 'sometimes|required|in:planning,ongoing,completed,on_hold',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $project = Project::findOrFail($id);
            $user = Auth::user();

            $data['updated_by'] = $user->id;
            $project->update($data);

            // Load relationships
            $project->load(['address.parent', 'creator', 'updater']);

            return [
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => $project->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update project',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

