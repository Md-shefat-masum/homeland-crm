<?php

namespace App\Http\Actions\Profession;

use App\Models\Profession;

class DeleteProfessionAction
{
    public function execute(int $id): array
    {
        try {
            $profession = Profession::findOrFail($id);

            // Check if profession is used by customers
            if ($profession->customers()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete profession that is in use by customers.',
                ];
            }

            $profession->delete();

            return [
                'success' => true,
                'message' => 'Profession deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete profession',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

