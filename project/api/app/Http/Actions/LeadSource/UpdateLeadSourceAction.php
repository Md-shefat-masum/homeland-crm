<?php

namespace App\Http\Actions\LeadSource;

use App\Models\LeadSource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateLeadSourceAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'title' => 'sometimes|required|string|max:255|unique:lead_sources,title,' . $id,
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
            $leadSource = LeadSource::findOrFail($id);
            $user = Auth::user();

            $data['updated_by'] = $user->id;
            $leadSource->update($data);

            // Load relationships
            $leadSource->load(['creator', 'updater']);

            return [
                'success' => true,
                'message' => 'Lead source updated successfully',
                'data' => $leadSource->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update lead source',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

