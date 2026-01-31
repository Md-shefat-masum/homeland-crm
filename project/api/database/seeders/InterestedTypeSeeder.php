<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InterestedType;
use App\Models\User;

class InterestedTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create a default user for seeding
        $user = User::first();

        if (!$user) {
            $this->command->warn('No user found. Please create a user first or run UserSeeder.');
            return;
        }

        $interestedTypes = [
            [
                'name' => 'Buy Next Month',
                'description' => 'Customer is planning to buy within the next month',
                'color' => '#4CAF50',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Buy Next 3 Months',
                'description' => 'Customer is planning to buy within the next 3 months',
                'color' => '#8BC34A',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Buy Next 6 Months',
                'description' => 'Customer is planning to buy within the next 6 months',
                'color' => '#CDDC39',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Buy Next Year',
                'description' => 'Customer is planning to buy within the next year',
                'color' => '#FFEB3B',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Area Change',
                'description' => 'Customer wants to change location/area',
                'color' => '#FF9800',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Budget Change',
                'description' => 'Customer wants to adjust their budget range',
                'color' => '#FF5722',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Property Type Change',
                'description' => 'Customer wants to change property type (apartment to land, etc.)',
                'color' => '#9C27B0',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Just Browsing',
                'description' => 'Customer is just browsing, no immediate purchase plan',
                'color' => '#9E9E9E',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Investment Purpose',
                'description' => 'Customer is interested in investment properties',
                'color' => '#2196F3',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Residential Purpose',
                'description' => 'Customer is looking for residential property',
                'color' => '#00BCD4',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Commercial Purpose',
                'description' => 'Customer is looking for commercial property',
                'color' => '#3F51B5',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Urgent',
                'description' => 'Customer has urgent requirement',
                'color' => '#F44336',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Follow Up Required',
                'description' => 'Customer needs follow-up call or meeting',
                'color' => '#FFC107',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Site Visit Scheduled',
                'description' => 'Customer has scheduled a site visit',
                'color' => '#009688',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Price Negotiation',
                'description' => 'Customer is in price negotiation phase',
                'color' => '#E91E63',
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'Documentation',
                'description' => 'Customer is in documentation/paperwork phase',
                'color' => '#795548',
                'is_active' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'Loan Processing',
                'description' => 'Customer is processing loan for purchase',
                'color' => '#607D8B',
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'name' => 'On Hold',
                'description' => 'Customer has put the purchase on hold',
                'color' => '#9E9E9E',
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'name' => 'Not Interested',
                'description' => 'Customer is not interested anymore',
                'color' => '#424242',
                'is_active' => true,
                'sort_order' => 19,
            ],
            [
                'name' => 'Converted',
                'description' => 'Lead has been converted to sale',
                'color' => '#4CAF50',
                'is_active' => true,
                'sort_order' => 20,
            ],
        ];

        foreach ($interestedTypes as $type) {
            InterestedType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'description' => $type['description'],
                    'color' => $type['color'],
                    'is_active' => $type['is_active'],
                    'sort_order' => $type['sort_order'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]
            );
        }

        $this->command->info('Interested types seeded successfully!');
    }
}
