<?php

namespace App\Http\Actions\LeadSource;

use App\Models\LeadSource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateLeadSourceAction
{
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255|unique:lead_sources,title',
            'description' => 'nullable|string',
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

            $leadSource = LeadSource::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $user->id,
            ]);

            // Load relationships
            $leadSource->load(['creator', 'updater']);

            return [
                'success' => true,
                'message' => 'Lead source created successfully',
                'data' => $leadSource,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create lead source',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

