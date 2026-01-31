<?php

namespace App\Http\Actions\Customer;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Profession;
use App\Models\Project;
use App\Models\Address;
use App\Models\CustomerNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UpdateCustomerAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'mobile' => 'sometimes|required|string|max:20|unique:customers,mobile,' . $id,
            'email' => 'nullable|email|max:255|unique:customers,email,' . $id,
            'alternative_mobile' => 'nullable|string|max:20',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'project_id' => 'nullable|exists:projects,id',
            'profession_id' => 'nullable|exists:professions,id',
            'current_address_id' => 'nullable|exists:addresses,id',
            'current_address_text' => 'nullable|string',
            'nearest_market' => 'nullable|string|max:255',
            'preferred_area' => 'nullable|string|max:255',
            'target_real_estate' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $customer = Customer::findOrFail($id);
            $user = Auth::user();

            // Validate foreign keys exist
            if (isset($data['customer_group_id'])) {
                $group = CustomerGroup::find($data['customer_group_id']);
                if (!$group) {
                    return [
                        'success' => false,
                        'message' => 'Customer group not found',
                    ];
                }
            }

            if (isset($data['project_id'])) {
                $project = Project::find($data['project_id']);
                if (!$project) {
                    return [
                        'success' => false,
                        'message' => 'Project not found',
                    ];
                }
            }

            if (isset($data['profession_id'])) {
                $profession = Profession::find($data['profession_id']);
                if (!$profession) {
                    return [
                        'success' => false,
                        'message' => 'Profession not found',
                    ];
                }
            }

            if (isset($data['current_address_id'])) {
                $address = Address::find($data['current_address_id']);
                if (!$address) {
                    return [
                        'success' => false,
                        'message' => 'Address not found',
                    ];
                }
            }

            // Use database transaction to ensure both customer and note are updated
            DB::beginTransaction();
            
            try {
                // Save latest note to customer->notes field
                $latestNote = isset($data['notes']) ? trim($data['notes']) : null;
                
                // Update customer with latest note
                $data['updated_by'] = $user->id;
                $customer->update($data);

                // If new note is provided and not empty, create a customer note entry
                if (!empty($latestNote) && $latestNote !== '') {
                    CustomerNote::create([
                        'customer_id' => $customer->id,
                        'lead_id' => null,
                        'note' => $latestNote,
                        'note_type' => 'general',
                        'is_important' => false,
                        'sync_status' => 'synced',
                        'sync_at' => now(),
                        'created_by' => $user->id,
                    ]);
                }

                DB::commit();

                // Load relationships
                $customer->load(['customerGroup', 'project', 'profession', 'currentAddress', 'creator', 'updater']);

                return [
                    'success' => true,
                    'message' => 'Customer updated successfully',
                    'data' => $customer->fresh(),
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update customer',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

