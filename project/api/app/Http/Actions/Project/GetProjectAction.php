<?php

namespace App\Http\Actions\Project;

use App\Models\Project;

class GetProjectAction
{
    public function execute(int $id): array
    {
        try {
            $project = Project::with([
                'address.parent',
                'creator',
                'updater',
                'leads' => function ($query) {
                    $query->latest()->limit(10);
                }
            ])
            ->withCount('leads')
            ->findOrFail($id);

            return [
                'success' => true,
                'data' => $project,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Project not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

