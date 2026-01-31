<?php

namespace App\Http\Actions\LeadSource;

use App\Models\LeadSource;

class DeleteLeadSourceAction
{
    public function execute(int $id): array
    {
        try {
            $leadSource = LeadSource::findOrFail($id);

            // Check if lead source is used by leads
            if ($leadSource->leads()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete lead source that is in use by leads.',
                ];
            }

            $leadSource->delete();

            return [
                'success' => true,
                'message' => 'Lead source deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete lead source',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

