<?php

namespace App\Http\Actions\InterestedType;

use App\Models\InterestedType;

class GetInterestedTypeAction
{
    public function execute(int $id): array
    {
        try {
            $interestedType = InterestedType::with(['creator', 'updater', 'leads'])->findOrFail($id);

            return [
                'success' => true,
                'data' => $interestedType,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Interested type not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

