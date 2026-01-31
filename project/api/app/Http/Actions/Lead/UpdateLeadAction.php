<?php

namespace App\Http\Actions\Lead;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\Project;
use App\Models\LeadSource;
use App\Models\InterestedType;
use App\Models\CustomerNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UpdateLeadAction
{
    public function execute(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'lead_source' => 'nullable|string|max:255',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'customer_id' => 'sometimes|required|exists:customers,id',
            'project_id' => 'nullable|exists:projects,id',
            'customer_requirement' => 'nullable|string',
            'preferred_area' => 'nullable|string|max:255',
            'next_contact_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'interested_type_id' => 'nullable|exists:interested_types,id',
            'status' => 'sometimes|in:new,contacted,qualified,converted,lost',
            'priority' => 'sometimes|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        try {
            $lead = Lead::findOrFail($id);
            $user = Auth::user();

            // Validate foreign keys exist
            if (isset($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
                if (!$customer) {
                    return [
                        'success' => false,
                        'message' => 'Customer not found',
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

            if (isset($data['lead_source_id'])) {
                $leadSource = LeadSource::find($data['lead_source_id']);
                if (!$leadSource) {
                    return [
                        'success' => false,
                        'message' => 'Lead source not found',
                    ];
                }
            }

            if (isset($data['interested_type_id'])) {
                $interestedType = InterestedType::find($data['interested_type_id']);
                if (!$interestedType) {
                    return [
                        'success' => false,
                        'message' => 'Interested type not found',
                    ];
                }
            }

            DB::beginTransaction();

            try {
                // Check if remarks is being updated/modified
                $oldRemarks = $lead->remarks ?? '';
                $newRemarks = isset($data['remarks']) ? trim($data['remarks']) : '';
                $remarksChanged = $oldRemarks !== $newRemarks;

                $data['updated_by'] = $user->id;
                $lead->update($data);

                // If remarks is updated/modified and not empty, create a new customer note entry
                if ($remarksChanged && !empty($newRemarks)) {
                    CustomerNote::create([
                        'customer_id' => $lead->customer_id,
                        'lead_id' => $lead->id,
                        'note' => $newRemarks,
                        'note_type' => 'general',
                        'is_important' => false,
                        'sync_status' => 'synced',
                        'sync_at' => now(),
                        'created_by' => $user->id,
                    ]);
                }

                DB::commit();

                // Load relationships
                $lead->load([
                    'customer',
                    'project',
                    'customerLeadSource',
                    'interestedType',
                    'creator',
                    'updater',
                ]);

                return [
                    'success' => true,
                    'message' => 'Lead updated successfully',
                    'data' => $lead->fresh(),
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update lead',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}

