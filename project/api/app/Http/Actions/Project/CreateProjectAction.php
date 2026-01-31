<?php

namespace App\Http\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateProjectAction
{
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug',
            'description' => 'nullable|string',
            'address_id' => 'nullable|exists:addresses,id',
            'address_text' => 'nullable|string',
            'project_type' => 'nullable|in:apartment,land,commercial,other',
            'status' => 'required|in:planning,ongoing,completed,on_hold',
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
            $user = Auth::user();

            // Generate slug from name if not provided or empty
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
                
                // Ensure uniqueness
                $originalSlug = $data['slug'];
                $counter = 1;
                while (Project::where('slug', $data['slug'])->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $project = Project::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'address_id' => $data['address_id'] ?? null,
                'address_text' => $data['address_text'] ?? null,
                'project_type' => $data['project_type'] ?? null,
                'status' => $data['status'] ?? 'ongoing',
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $user->id,
            ]);

            // Load relationships
            $project->load(['address.parent', 'creator', 'updater']);

            return [
                'success' => true,
                'message' => 'Project created successfully',
                'data' => $project,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create project',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

