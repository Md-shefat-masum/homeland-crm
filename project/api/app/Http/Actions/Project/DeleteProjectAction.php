<?php

namespace App\Http\Actions\Project;

use App\Models\Project;

class DeleteProjectAction
{
    public function execute(int $id): array
    {
        try {
            $project = Project::findOrFail($id);

            // Check if project is used by leads
            if ($project->leads()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete project that is in use by leads.',
                ];
            }

            $project->delete();

            return [
                'success' => true,
                'message' => 'Project deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete project',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

