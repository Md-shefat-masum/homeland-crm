<?php

namespace App\Http\Actions\Profession;

use App\Models\Profession;

class GetProfessionsByTypeAction
{
    public function execute(string $type): array
    {
        try {
            $professions = Profession::where('type', $type)
                ->orderBy('job_title')
                ->orderBy('business_type')
                ->get();

            return [
                'success' => true,
                'data' => $professions,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch professions',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

