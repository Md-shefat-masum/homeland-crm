<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerGroup;
use App\Models\User;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate customer_groups table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customer_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get admin user for created_by
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }
        $adminId = $admin ? $admin->id : 1;

        // Create customer groups
        $groups = [
            [
                'name' => 'VIP Customers',
                'description' => 'High-value customers with premium service',
                'color' => '#FFD700', // Gold
                'is_active' => true,
            ],
            [
                'name' => 'Regular Customers',
                'description' => 'Standard customers with regular service',
                'color' => '#4CAF50', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Potential Leads',
                'description' => 'Potential customers who are interested',
                'color' => '#2196F3', // Blue
                'is_active' => true,
            ],
            [
                'name' => 'Cold Leads',
                'description' => 'Leads that need follow-up',
                'color' => '#9E9E9E', // Gray
                'is_active' => true,
            ],
            [
                'name' => 'Hot Leads',
                'description' => 'Highly interested leads ready to purchase',
                'color' => '#F44336', // Red
                'is_active' => true,
            ],
            [
                'name' => 'Follow-up Required',
                'description' => 'Customers requiring follow-up calls',
                'color' => '#FF9800', // Orange
                'is_active' => true,
            ],
            [
                'name' => 'Closed Deals',
                'description' => 'Customers who have completed purchase',
                'color' => '#8BC34A', // Light Green
                'is_active' => true,
            ],
            [
                'name' => 'Lost Customers',
                'description' => 'Customers who did not proceed',
                'color' => '#757575', // Dark Gray
                'is_active' => false,
            ],
        ];

        foreach ($groups as $group) {
            CustomerGroup::create([
                'name' => $group['name'],
                'description' => $group['description'],
                'color' => $group['color'],
                'is_active' => $group['is_active'],
                'created_by' => $adminId,
            ]);
        }

        $this->command->info('CustomerGroupSeeder completed successfully!');
        $this->command->info('Created ' . count($groups) . ' customer groups');
    }
}
