<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\Profession;
use App\Models\Address;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate customers table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('customers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get admin user for created_by
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }
        $adminId = $admin ? $admin->id : 1;

        // Get available customer groups
        $customerGroups = CustomerGroup::where('is_active', true)->get();
        $customerGroupIds = $customerGroups->pluck('id')->toArray();

        // Get available professions
        $professions = Profession::all();
        $professionIds = $professions->pluck('id')->toArray();

        // Get available addresses (prefer areas and districts)
        $addresses = Address::whereIn('type', ['area', 'district', 'upazila'])->where('is_active', true)->get();
        $addressIds = $addresses->pluck('id')->toArray();

        // Generate unique mobile numbers
        $usedMobiles = [];
        $generateMobile = function () use (&$usedMobiles) {
            do {
                $mobile = '01' . rand(3, 9) . rand(10000000, 99999999);
            } while (in_array($mobile, $usedMobiles));
            $usedMobiles[] = $mobile;
            return $mobile;
        };

        // Create 10 customers with realistic data
        $customers = [
            [
                'name' => 'Ahmed Rahman',
                'mobile' => $generateMobile(),
                'email' => 'ahmed.rahman@example.com',
                'alternative_mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'House 45, Road 12, Block C',
                'nearest_market' => 'Gulshan Market',
                'preferred_area' => 'Gulshan',
                'target_real_estate' => '3 BHK Apartment',
                'notes' => 'Interested in buying property. Prefers upper floors.',
                'info' => [
                    'date_of_birth' => '1985-05-15',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'family_members' => 4,
                    'income_range' => '50000-100000',
                    'budget_range' => '5000000-8000000',
                    'preferred_contact_time' => 'evening',
                    'referral_source' => 'facebook',
                    'nid_number' => '1234567890123',
                    'emergency_contact' => [
                        'name' => 'Fatima Rahman',
                        'relation' => 'wife',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'ahmed.rahman',
                        'whatsapp' => true,
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Fatima Begum',
                'mobile' => $generateMobile(),
                'email' => 'fatima.begum@example.com',
                'alternative_mobile' => null,
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'Villa 23, Sector 7',
                'nearest_market' => 'Banani Market',
                'preferred_area' => 'Banani',
                'target_real_estate' => '2 BHK Apartment',
                'notes' => 'Looking for rental property first, then purchase.',
                'info' => [
                    'date_of_birth' => '1990-08-22',
                    'gender' => 'female',
                    'marital_status' => 'single',
                    'family_members' => 1,
                    'income_range' => '30000-50000',
                    'budget_range' => '3000000-5000000',
                    'preferred_contact_time' => 'afternoon',
                    'referral_source' => 'website',
                    'nid_number' => '9876543210987',
                    'emergency_contact' => [
                        'name' => 'Karim Uddin',
                        'relation' => 'brother',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => null,
                        'whatsapp' => true,
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Karim Uddin',
                'mobile' => $generateMobile(),
                'email' => 'karim.uddin@example.com',
                'alternative_mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'Flat 5B, Building 12',
                'nearest_market' => 'Dhanmondi Market',
                'preferred_area' => 'Dhanmondi',
                'target_real_estate' => '4 BHK Apartment',
                'notes' => 'Wants to invest in real estate. Has multiple properties.',
                'info' => [
                    'date_of_birth' => '1978-12-10',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'family_members' => 5,
                    'income_range' => '100000-200000',
                    'budget_range' => '10000000-15000000',
                    'preferred_contact_time' => 'morning',
                    'referral_source' => 'referral',
                    'nid_number' => '4567890123456',
                    'emergency_contact' => [
                        'name' => 'Rashida Begum',
                        'relation' => 'wife',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'karim.uddin',
                        'whatsapp' => true,
                    ],
                    'existing_properties' => 2,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Rashida Begum',
                'mobile' => $generateMobile(),
                'email' => 'rashida.begum@example.com',
                'alternative_mobile' => null,
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'House 78, Road 5',
                'nearest_market' => 'Mirpur Market',
                'preferred_area' => 'Mirpur',
                'target_real_estate' => '2 BHK Apartment',
                'notes' => 'First-time buyer. Needs guidance.',
                'info' => [
                    'date_of_birth' => '1992-03-25',
                    'gender' => 'female',
                    'marital_status' => 'married',
                    'family_members' => 3,
                    'income_range' => '40000-60000',
                    'budget_range' => '4000000-6000000',
                    'preferred_contact_time' => 'evening',
                    'referral_source' => 'newspaper',
                    'nid_number' => '7890123456789',
                    'emergency_contact' => [
                        'name' => 'Karim Uddin',
                        'relation' => 'husband',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => null,
                        'whatsapp' => true,
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Ali',
                'mobile' => $generateMobile(),
                'email' => 'mohammad.ali@example.com',
                'alternative_mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'Shop 12, Market Complex',
                'nearest_market' => 'Uttara Market',
                'preferred_area' => 'Uttara',
                'target_real_estate' => 'Commercial Space',
                'notes' => 'Looking for commercial property for business expansion.',
                'info' => [
                    'date_of_birth' => '1980-07-18',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'family_members' => 4,
                    'income_range' => '80000-120000',
                    'budget_range' => '8000000-12000000',
                    'preferred_contact_time' => 'anytime',
                    'referral_source' => 'walk-in',
                    'nid_number' => '2345678901234',
                    'emergency_contact' => [
                        'name' => 'Ayesha Ali',
                        'relation' => 'wife',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'mohammad.ali',
                        'whatsapp' => true,
                    ],
                    'business_type' => 'retail',
                    'business_years' => 5,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Ayesha Khatun',
                'mobile' => $generateMobile(),
                'email' => 'ayesha.khatun@example.com',
                'alternative_mobile' => null,
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'Flat 3A, Tower 5',
                'nearest_market' => 'Gulshan Market',
                'preferred_area' => 'Gulshan',
                'target_real_estate' => 'Penthouse',
                'notes' => 'High-end buyer. Prefers luxury properties.',
                'info' => [
                    'date_of_birth' => '1988-11-30',
                    'gender' => 'female',
                    'marital_status' => 'married',
                    'family_members' => 3,
                    'income_range' => '150000-300000',
                    'budget_range' => '20000000-30000000',
                    'preferred_contact_time' => 'afternoon',
                    'referral_source' => 'referral',
                    'nid_number' => '5678901234567',
                    'emergency_contact' => [
                        'name' => 'Mohammad Ali',
                        'relation' => 'husband',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'ayesha.khatun',
                        'whatsapp' => true,
                    ],
                    'preferred_property_type' => 'luxury',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Hasan Mahmud',
                'mobile' => $generateMobile(),
                'email' => 'hasan.mahmud@example.com',
                'alternative_mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'House 34, Lane 8',
                'nearest_market' => 'Dhanmondi Market',
                'preferred_area' => 'Dhanmondi',
                'target_real_estate' => '3 BHK Apartment',
                'notes' => 'Student looking for rental property.',
                'info' => [
                    'date_of_birth' => '1998-02-14',
                    'gender' => 'male',
                    'marital_status' => 'single',
                    'family_members' => 1,
                    'income_range' => '20000-30000',
                    'budget_range' => '2000000-3000000',
                    'preferred_contact_time' => 'evening',
                    'referral_source' => 'university',
                    'nid_number' => '8901234567890',
                    'emergency_contact' => [
                        'name' => 'Rashida Mahmud',
                        'relation' => 'mother',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'hasan.mahmud',
                        'whatsapp' => true,
                    ],
                    'student_id' => 'STU2024001',
                    'university' => 'Dhaka University',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Nusrat Jahan',
                'mobile' => $generateMobile(),
                'email' => 'nusrat.jahan@example.com',
                'alternative_mobile' => null,
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'Villa 67, Block A',
                'nearest_market' => 'Banani Market',
                'preferred_area' => 'Banani',
                'target_real_estate' => '2 BHK Apartment',
                'notes' => 'Housewife looking for family home.',
                'info' => [
                    'date_of_birth' => '1995-09-05',
                    'gender' => 'female',
                    'marital_status' => 'married',
                    'family_members' => 4,
                    'income_range' => '50000-70000',
                    'budget_range' => '5000000-7000000',
                    'preferred_contact_time' => 'morning',
                    'referral_source' => 'friend',
                    'nid_number' => '3456789012345',
                    'emergency_contact' => [
                        'name' => 'Rafiqul Islam',
                        'relation' => 'husband',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'nusrat.jahan',
                        'whatsapp' => true,
                    ],
                    'spouse_income' => '60000',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Rafiqul Islam',
                'mobile' => $generateMobile(),
                'email' => 'rafiqul.islam@example.com',
                'alternative_mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'Flat 8C, Building 20',
                'nearest_market' => 'Uttara Market',
                'preferred_area' => 'Uttara',
                'target_real_estate' => '4 BHK Apartment',
                'notes' => 'Wants to relocate from current area. Prefers new construction.',
                'info' => [
                    'date_of_birth' => '1991-04-20',
                    'gender' => 'male',
                    'marital_status' => 'married',
                    'family_members' => 4,
                    'income_range' => '70000-90000',
                    'budget_range' => '7000000-9000000',
                    'preferred_contact_time' => 'weekend',
                    'referral_source' => 'website',
                    'nid_number' => '6789012345678',
                    'emergency_contact' => [
                        'name' => 'Nusrat Jahan',
                        'relation' => 'wife',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'rafiqul.islam',
                        'whatsapp' => true,
                    ],
                    'preferred_property_age' => 'new',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Sharmin Akter',
                'mobile' => $generateMobile(),
                'email' => 'sharmin.akter@example.com',
                'alternative_mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                'customer_group_id' => $customerGroupIds[array_rand($customerGroupIds)] ?? null,
                'profession_id' => $professionIds[array_rand($professionIds)] ?? null,
                'current_address_id' => $addressIds[array_rand($addressIds)] ?? null,
                'current_address_text' => 'House 12, Road 3',
                'nearest_market' => 'Mirpur Market',
                'preferred_area' => 'Mirpur',
                'target_real_estate' => '3 BHK Apartment',
                'notes' => 'Interested in both rental and purchase options.',
                'info' => [
                    'date_of_birth' => '1987-06-12',
                    'gender' => 'female',
                    'marital_status' => 'divorced',
                    'family_members' => 2,
                    'income_range' => '40000-60000',
                    'budget_range' => '4000000-6000000',
                    'preferred_contact_time' => 'anytime',
                    'referral_source' => 'google',
                    'nid_number' => '9012345678901',
                    'emergency_contact' => [
                        'name' => 'Rashida Begum',
                        'relation' => 'sister',
                        'mobile' => '01' . rand(3, 9) . rand(10000000, 99999999),
                    ],
                    'social_media' => [
                        'facebook' => 'sharmin.akter',
                        'whatsapp' => true,
                    ],
                    'preferred_payment_plan' => 'installment',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create([
                'name' => $customerData['name'],
                'mobile' => $customerData['mobile'],
                'email' => $customerData['email'],
                'image' => 'avatar.png',
                'alternative_mobile' => $customerData['alternative_mobile'],
                'customer_group_id' => $customerData['customer_group_id'],
                'profession_id' => $customerData['profession_id'],
                'current_address_id' => $customerData['current_address_id'],
                'current_address_text' => $customerData['current_address_text'],
                'nearest_market' => $customerData['nearest_market'],
                'preferred_area' => $customerData['preferred_area'],
                'target_real_estate' => $customerData['target_real_estate'],
                'notes' => $customerData['notes'],
                'info' => $customerData['info'],
                'is_active' => $customerData['is_active'],
                'created_by' => $adminId,
            ]);
        }

        $this->command->info('CustomerSeeder completed successfully!');
        $this->command->info('Created ' . count($customers) . ' customers');
    }
}

