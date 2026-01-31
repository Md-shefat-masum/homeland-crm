<?php

namespace App\Http\Actions\Profession;

use App\Models\Profession;

class GetProfessionAction
{
    public function execute(int $id): array
    {
        try {
            $profession = Profession::findOrFail($id);

            return [
                'success' => true,
                'data' => $profession,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Profession not found',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

