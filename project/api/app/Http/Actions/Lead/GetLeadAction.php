<?php

namespace App\Http\Actions\Lead;

use App\Models\Lead;

class GetLeadAction
{
    public function execute(int $id): array
    {
        try {
            $lead = Lead::with([
                'customer',
                'project',
                'customerLeadSource',
                'interestedType',
                'pricing',
                'notes.creator',
                'followUps',
                'callLogs',
                'creator',
                'updater',
            ])->findOrFail($id);

            return [
                'success' => true,
                'data' => $lead,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lead not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

