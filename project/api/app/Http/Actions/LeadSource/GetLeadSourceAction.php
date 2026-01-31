<?php

namespace App\Http\Actions\LeadSource;

use App\Models\LeadSource;

class GetLeadSourceAction
{
    public function execute(int $id): array
    {
        try {
            $leadSource = LeadSource::with([
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
                'data' => $leadSource,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lead source not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

