<?php

namespace App\Http\Actions\Address;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateAddressAction
{
    public function execute(array $data): array
    {
        // Auto-generate code from name if not provided
        if (empty($data['code']) && !empty($data['name'])) {
            $data['code'] = $this->generateUniqueCode($data['name']);
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,division,district,upazila,union,village,area,road,other,home name',
            'parent_id' => 'nullable|exists:addresses,id',
            'code' => 'required|string|max:50|unique:addresses,code',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $user = Auth::user();
            $parentId = $data['parent_id'] ?? null;

            // Calculate depth and path
            $depth = 0;
            $path = '/';

            if ($parentId) {
                $parent = Address::findOrFail($parentId);
                $depth = $parent->depth + 1;
                $path = $parent->path . $parent->id . '/';
            }

            $address = Address::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'parent_id' => $parentId,
                'path' => $path,
                'depth' => $depth,
                'code' => $data['code'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => true,
                'created_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $address,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create address',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * Generate unique code from name
     */
    private function generateUniqueCode(string $name): string
    {
        // Generate base code from name (lowercase, remove spaces, special chars)
        $baseCode = strtolower(trim($name));
        $baseCode = preg_replace('/[^a-z0-9]/', '', $baseCode);
        $baseCode = substr($baseCode, 0, 20); // Limit length

        if (empty($baseCode)) {
            // Fallback: use first 3 chars of name
            $baseCode = strtolower(substr(trim($name), 0, 3));
        }

        // Check if code exists and find next available number
        $code = strtoupper($baseCode);
        $counter = 1;

        while (Address::where('code', $code)->exists()) {
            $counter++;
            $code = strtoupper($baseCode . $counter);
        }

        return $code;
    }
}

