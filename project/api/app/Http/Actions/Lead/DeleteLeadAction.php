<?php

namespace App\Http\Actions\Lead;

use App\Models\Lead;

class DeleteLeadAction
{
    public function execute(int $id): array
    {
        try {
            $lead = Lead::findOrFail($id);
            $lead->delete();

            return [
                'success' => true,
                'message' => 'Lead deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete lead',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

