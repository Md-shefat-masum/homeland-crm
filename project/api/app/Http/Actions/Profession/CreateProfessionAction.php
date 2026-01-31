<?php

namespace App\Http\Actions\Profession;

use App\Models\Profession;
use Illuminate\Support\Facades\Validator;

class CreateProfessionAction
{
    public function execute(array $data): array
    {
        $validator = Validator::make($data, [
            'type' => 'required|string|max:100',
            'business_type' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $profession = Profession::create($data);

            return [
                'success' => true,
                'message' => 'Profession created successfully',
                'data' => $profession,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create profession',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

