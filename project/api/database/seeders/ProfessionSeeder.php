<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Profession;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate professions table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('professions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create professions by type
        $professions = [
            // Job type professions
            [
                'type' => 'job',
                'job_title' => 'Software Engineer',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Software development and programming',
            ],
            [
                'type' => 'job',
                'job_title' => 'Doctor',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Medical professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Teacher',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Education professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Engineer',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Engineering professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Banker',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Banking professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Accountant',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Accounting professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Lawyer',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Legal professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Manager',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Management professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Sales Executive',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Sales professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Government Employee',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Government service',
            ],

            // Business type professions
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Retail Shop',
                'description' => 'Retail business owner',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Wholesale Business',
                'description' => 'Wholesale business owner',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Real Estate',
                'description' => 'Real estate business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Construction',
                'description' => 'Construction business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Trading',
                'description' => 'Trading business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Manufacturing',
                'description' => 'Manufacturing business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Restaurant',
                'description' => 'Restaurant business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Transport',
                'description' => 'Transport business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Garments',
                'description' => 'Garments business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Import/Export',
                'description' => 'Import/Export business',
            ],

            // Student type
            [
                'type' => 'student',
                'job_title' => null,
                'company_name' => null,
                'business_type' => null,
                'description' => 'Student',
            ],

            // Housewife type
            [
                'type' => 'housewife',
                'job_title' => null,
                'company_name' => null,
                'business_type' => null,
                'description' => 'Housewife',
            ],

            // Other common types
            [
                'type' => 'job',
                'job_title' => 'Nurse',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Nursing professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Pharmacist',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Pharmacy professional',
            ],
            [
                'type' => 'job',
                'job_title' => 'Architect',
                'company_name' => null,
                'business_type' => null,
                'description' => 'Architecture professional',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Pharmacy',
                'description' => 'Pharmacy business',
            ],
            [
                'type' => 'business',
                'job_title' => null,
                'company_name' => null,
                'business_type' => 'Clinic',
                'description' => 'Medical clinic',
            ],
        ];

        foreach ($professions as $profession) {
            Profession::create($profession);
        }

        $this->command->info('ProfessionSeeder completed successfully!');
        $this->command->info('Created ' . count($professions) . ' professions');
        $this->command->info('  - Job type: ' . collect($professions)->where('type', 'job')->count());
        $this->command->info('  - Business type: ' . collect($professions)->where('type', 'business')->count());
        $this->command->info('  - Student type: ' . collect($professions)->where('type', 'student')->count());
        $this->command->info('  - Housewife type: ' . collect($professions)->where('type', 'housewife')->count());
    }
}
