<?php

namespace App\Http\Actions\InterestedType;

use App\Models\InterestedType;

class DeleteInterestedTypeAction
{
    public function execute(int $id): array
    {
        try {
            $interestedType = InterestedType::findOrFail($id);

            // Check if there are any associated leads
            if ($interestedType->leads()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete interested type: It is associated with existing leads.',
                ];
            }

            $interestedType->delete();

            return [
                'success' => true,
                'message' => 'Interested type deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete interested type',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

